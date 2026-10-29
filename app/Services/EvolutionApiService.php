<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\SystemSetting;
use App\Models\WhatsAppLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $instance;

    public function __construct()
    {
        $this->baseUrl = rtrim(SystemSetting::get('evolution_api_url', config('evolution.api_url', '')), '/');
        $this->apiKey = SystemSetting::get('evolution_api_key', config('evolution.api_key', ''));
        $this->instance = SystemSetting::get('evolution_instance_name', config('evolution.instance_name', 'aquatica_referidos'));
    }

    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->apiKey) && !empty($this->instance);
    }

    /**
     * Normaliza el destinatario (JID de grupo o número con código de país 506)
     */
    public function formatPhoneNumber(string $recipient): string
    {
        $trimmed = trim($recipient);
        if (empty($trimmed)) {
            return '';
        }

        // Si es un JID (@g.us o @s.whatsapp.net), se conserva tal cual
        if (str_contains($trimmed, '@')) {
            return $trimmed;
        }

        // Remover espacios, guiones, paréntesis y signos
        $digits = preg_replace('/\D+/', '', $trimmed);

        // Si son 8 dígitos (Costa Rica), anteponer el prefijo 506
        if (strlen($digits) === 8) {
            return '506' . $digits;
        }

        return $digits;
    }

    /**
     * Envía mensaje de texto a un número individual o grupo
     */
    public function sendTextMessage(string $recipient, string $message, ?int $referralId = null, string $type = 'general'): array
    {
        $formattedRecipient = $this->formatPhoneNumber($recipient);
        if (empty($formattedRecipient)) {
            return ['success' => false, 'error' => 'Destinatario vacío'];
        }

        if (!$this->isConfigured()) {
            Log::info("Evolution API no configurada. Simulando envío a {$formattedRecipient}: {$message}");
            WhatsAppLog::create([
                'referral_id' => $referralId,
                'phone' => $formattedRecipient,
                'message_type' => $type,
                'direction' => 'outgoing',
                'message_body' => $message,
                'status' => 'sent',
                'evolution_message_id' => 'simulated_' . uniqid(),
                'sent_at' => now(),
            ]);
            return ['status' => 'simulated', 'success' => true];
        }

        try {
            $endpoint = "{$this->baseUrl}/message/sendText/{$this->instance}";
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($endpoint, [
                'number' => $formattedRecipient,
                'text' => $message,
            ]);

            $responseData = $response->json();
            $isSuccess = $response->successful();

            WhatsAppLog::create([
                'referral_id' => $referralId,
                'phone' => $formattedRecipient,
                'message_type' => $type,
                'direction' => 'outgoing',
                'message_body' => $message,
                'payload' => $responseData,
                'status' => $isSuccess ? 'sent' : 'failed',
                'evolution_message_id' => $responseData['key']['id'] ?? null,
                'error_message' => $isSuccess ? null : ($response->body()),
                'sent_at' => $isSuccess ? now() : null,
            ]);

            return [
                'success' => $isSuccess,
                'response' => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error("Error enviando mensaje Evolution API a {$formattedRecipient}: " . $e->getMessage());

            WhatsAppLog::create([
                'referral_id' => $referralId,
                'phone' => $formattedRecipient,
                'message_type' => $type,
                'direction' => 'outgoing',
                'message_body' => $message,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * 1. Alerta inmediata al equipo administrativo (Grupo por JID y/o números individuales)
     */
    public function notifyAdminGroup(Referral $referral): void
    {
        $enabled = SystemSetting::get('evolution_notify_admin_group', true);
        if (!$enabled) {
            return;
        }

        $serviceName = $referral->service->name ?? 'Servicio AQUATICA';
        $referrerName = $referral->referrer->full_name;
        $referrerPhone = $referral->referrer->phone;
        $refereeName = $referral->referee->full_name;
        $refereePhone = $referral->referee->phone;

        $referrerDigits = preg_replace('/\D+/', '', $referrerPhone);
        if (strlen($referrerDigits) === 8) {
            $referrerDigits = '506' . $referrerDigits;
        }

        $refereeDigits = preg_replace('/\D+/', '', $refereePhone);
        if (strlen($refereeDigits) === 8) {
            $refereeDigits = '506' . $refereeDigits;
        }

        $baseUrl = rtrim(config('app.url', 'https://referidos.aquatica.cr'), '/');
        $adminExpedienteUrl = "{$baseUrl}/admin/referrals/{$referral->id}";

        $message = "🏊‍♂️ *¡NUEVO REFERIDO REGISTRADO EN AQUATICA!* 🏊‍♂️\n\n"
            . "📋 *Código de Expediente:* `{$referral->referral_code}`\n\n"
            . "👤 *¿QUIÉN REFIERE? (Cliente Actual):*\n"
            . "• Nombre: *{$referrerName}*\n"
            . "• Teléfono: {$referrerPhone}\n"
            . (!empty($referrerDigits) ? "• Chat WhatsApp: https://wa.me/{$referrerDigits}\n\n" : "\n")
            . "🎯 *CONTACTO REFERIDO (¿Quién asistirá a clases?):*\n"
            . "• Nombre: *{$refereeName}*\n"
            . "• Teléfono: {$refereePhone}\n"
            . (!empty($refereeDigits) ? "• Contactar por WhatsApp: https://wa.me/{$refereeDigits}\n\n" : "\n")
            . "🌊 *SERVICIO DE INTERÉS:*\n"
            . "• *{$serviceName}*\n"
            . "• Tarifa preferencial: *₡30.800* (Precio regular: ~₡35.000~)\n\n"
            . "👉 *Ver expediente en Panel Administrativo:*\n"
            . "{$adminExpedienteUrl}";

        $recipients = [];

        // 1. Grupo de WhatsApp por JID (si está configurado)
        $groupJid = trim(SystemSetting::get('evolution_admin_group_jid', config('evolution.admin_group_jid', '')));
        if (!empty($groupJid)) {
            $recipients[] = $groupJid;
        }

        // 2. Números individuales de administración (si están configurados)
        $phonesRaw = SystemSetting::get('evolution_admin_phones', '');
        if (!empty($phonesRaw)) {
            $phones = preg_split('/[,\n\r;]+/', $phonesRaw, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($phones as $p) {
                $cleaned = $this->formatPhoneNumber(trim($p));
                if (!empty($cleaned) && !in_array($cleaned, $recipients)) {
                    $recipients[] = $cleaned;
                }
            }
        }

        if (empty($recipients)) {
            Log::info("No hay destinatarios administrativos configurados (ni JID ni números individuales). Omitiendo alerta.");
            return;
        }

        foreach ($recipients as $recipient) {
            $this->sendTextMessage($recipient, $message, $referral->id, 'admin_group_alert');
        }
    }

    /**
     * 2. Mensaje automático de bienvenida al referido (al registrarse en la web)
     */
    public function sendRefereeWelcome(Referral $referral): void
    {
        $serviceName = $referral->service->name ?? 'AQUATICA';
        $referee = $referral->referee;
        $referrerName = $referral->referrer->full_name;

        $message = "¡Hola *{$referee->full_name}*! 👋🏊‍♀️\n\n"
            . "*{$referrerName}* te ha referido al programa de natación de *AQUATICA* para el servicio *{$serviceName}*.\n\n"
            . "🎉 Tienes disponible el beneficio especial de *₡30.800 en tu mensualidad* (precio regular ₡35.000) una vez completada tu matrícula.\n\n"
            . "Tu código de expediente es: *{$referral->referral_code}*\n\n"
            . "Muy pronto nuestro equipo de recepción se comunicará contigo para coordinar tu horario y evaluación acuática. Si tienes consultas, puedes responder directamente a este mensaje.\n\n"
            . "¡Bienvenido a la familia AQUATICA! 💦";

        $this->sendTextMessage($referee->phone, $message, $referral->id, 'referee_welcome');
    }

    /**
     * 3. Mensaje al referente cuando el referido ya pagó y el beneficio de ₡30.800 está ACTIVO
     */
    public function sendReferrerBenefitApproved(Referral $referral): void
    {
        $referrer = $referral->referrer;
        $referee = $referral->referee;
        $serviceName = $referral->service->name ?? 'AQUATICA';

        $message = "¡Hola *{$referrer->full_name}*! 🎉🏊‍♂️\n\n"
            . "Te tenemos excelentes noticias: Tu referido *{$referee->full_name}* ha completado su matrícula y pago en *AQUATICA* ({$serviceName}).\n\n"
            . "✨ *¡Tu beneficio ha sido activado!* ✨\n"
            . "Tu próxima mensualidad será a la tarifa preferencial de *₡30.800*.\n\n"
            . "Expediente: *{$referral->referral_code}*\n\n"
            . "¡Muchas gracias por recomendarnos y seguir nadando con nosotros! 💦";

        $this->sendTextMessage($referrer->phone, $message, $referral->id, 'referrer_benefit_approved');
    }

    /**
     * 4. Mensaje al referido confirmándole que su mensualidad quedó oficialmente en ₡30.800
     */
    public function sendRefereeBenefitApproved(Referral $referral): void
    {
        $referee = $referral->referee;
        $serviceName = $referral->service->name ?? 'AQUATICA';

        $message = "¡Hola *{$referee->full_name}*! 🎉🏊‍♀️\n\n"
            . "Hemos validado tu matrícula y pago en *AQUATICA* ({$serviceName}).\n\n"
            . "✨ *¡Tu beneficio comercial de ₡30.800 está formalmente activado!* ✨\n"
            . "Agradecemos a *{$referral->referrer->full_name}* por haberte recomendado.\n\n"
            . "Expediente: *{$referral->referral_code}*\n\n"
            . "¡Nos vemos en la piscina! 💦";

        $this->sendTextMessage($referee->phone, $message, $referral->id, 'referee_benefit_approved');
    }
}

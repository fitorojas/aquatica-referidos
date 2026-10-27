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
     * Envía mensaje de texto a un número individual o grupo
     */
    public function sendTextMessage(string $recipient, string $message, ?int $referralId = null, string $type = 'general'): array
    {
        if (!$this->isConfigured()) {
            Log::info("Evolution API no configurada. Simulando envío a {$recipient}: {$message}");
            WhatsAppLog::create([
                'referral_id' => $referralId,
                'phone' => $recipient,
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
                'number' => $recipient,
                'text' => $message,
            ]);

            $responseData = $response->json();
            $isSuccess = $response->successful();

            WhatsAppLog::create([
                'referral_id' => $referralId,
                'phone' => $recipient,
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
            Log::error("Error enviando mensaje Evolution API a {$recipient}: " . $e->getMessage());

            WhatsAppLog::create([
                'referral_id' => $referralId,
                'phone' => $recipient,
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
     * Envía alerta inmediata al grupo administrativo de WhatsApp
     */
    public function notifyAdminGroup(Referral $referral): void
    {
        $groupJid = SystemSetting::get('evolution_admin_group_jid', config('evolution.admin_group_jid'));
        if (empty($groupJid)) {
            Log::info("No se ha configurado EVOLUTION_ADMIN_GROUP_JID. Omitiendo alerta a grupo.");
            return;
        }

        $serviceName = $referral->service->name ?? 'Servicio AQUATICA';
        $studentName = $referral->student->full_name ?? $referral->referee->full_name;
        $referrerName = $referral->referrer->full_name;
        $refereePhone = $referral->referee->phone;

        $message = "🏊‍♂️ *¡NUEVO REFERIDO REGISTRADO EN AQUATICA!* 🏊‍♂️\n\n"
            . "📋 *Código:* `{$referral->referral_code}`\n"
            . "👤 *Referente:* {$referrerName} ({$referral->referrer->phone})\n"
            . "🎯 *Referido:* {$referral->referee->full_name} ({$refereePhone})\n"
            . "🧒 *Alumno:* {$studentName}\n"
            . "🌊 *Servicio de interés:* {$serviceName}\n"
            . "💰 *Beneficio:* ₡30.800 (sujeto a validación)\n\n"
            . "👉 Ver expediente: " . url("/admin/referrals/{$referral->id}");

        $this->sendTextMessage($groupJid, $message, $referral->id, 'admin_group_alert');
    }

    /**
     * Envía mensaje de bienvenida al referido
     */
    public function sendRefereeWelcome(Referral $referral): void
    {
        $serviceName = $referral->service->name ?? 'AQUATICA';
        $referee = $referral->referee;
        $referrerName = $referral->referrer->full_name;

        $message = "¡Hola *{$referee->full_name}*! 👋🏊‍♀️\n\n"
            . "*{$referrerName}* te ha referido al programa de natación de *AQUATICA* para el servicio *{$serviceName}*.\n\n"
            . "🎉 Tienes disponible el beneficio especial de *₡30.800 en tu mensualidad* tras verificar tu matrícula.\n\n"
            . "Tu código de expediente es: *{$referral->referral_code}*\n\n"
            . "Muy pronto nuestro equipo administrativo se comunicará contigo para coordinar tu horario y evaluación. Si tienes consultas, puedes responder directamente a este mensaje.\n\n"
            . "¡Bienvenido a la familia AQUATICA! 💦";

        $this->sendTextMessage($referee->phone, $message, $referral->id, 'referee_welcome');
    }
}

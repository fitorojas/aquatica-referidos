<?php

namespace App\Actions\Referrals;

use App\Jobs\NotifyAdminGroupNewReferralJob;
use App\Jobs\ProcessWhatsAppNotificationJob;
use App\Models\Customer;
use App\Models\DuplicateDetection;
use App\Models\Referral;
use App\Models\ReferralActivity;
use App\Models\Service;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Services\DuplicateDetectionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateReferralAction
{
    public function __construct(
        protected DuplicateDetectionService $duplicateService
    ) {}

    public function execute(array $data): Referral
    {
        // 1. Análisis de duplicados y reglas de conflicto
        $analysis = $this->duplicateService->analyze(
            $data['referrer'],
            $data['referee'],
            $data['student'] ?? []
        );

        if ($analysis['is_blocked']) {
            throw ValidationException::withMessages([
                'referee.phone' => [$analysis['block_reason']],
            ]);
        }

        return DB::transaction(function () use ($data, $analysis) {
            // 2. Crear o actualizar Cliente Referente con los datos más recientes
            $referrerPhone = $this->duplicateService->normalizePhone($data['referrer']['phone']);
            $referrer = Customer::updateOrCreate(
                ['phone' => $referrerPhone],
                [
                    'full_name' => trim($data['referrer']['full_name']),
                    'email' => $data['referrer']['email'] ?? null,
                    'identification' => $this->duplicateService->normalizeIdentification($data['referrer']['identification'] ?? null),
                    'customer_type' => 'both',
                ]
            );

            // 3. Crear o actualizar Cliente Referido (Alumno) con los datos más recientes
            $refereePhone = $this->duplicateService->normalizePhone($data['referee']['phone']);
            $hasSeparateStudent = !empty($data['student']) && empty($data['student']['is_same_as_customer']) && !empty($data['student']['full_name']);

            $referee = Customer::updateOrCreate(
                ['phone' => $refereePhone],
                [
                    'full_name' => trim($data['referee']['full_name']),
                    'email' => $data['referee']['email'] ?? null,
                    'identification' => $this->duplicateService->normalizeIdentification($data['referee']['identification'] ?? null),
                    'customer_type' => $hasSeparateStudent ? 'guardian' : 'both',
                ]
            );

            // 4. Crear o actualizar registro del Alumno
            if (!$hasSeparateStudent) {
                $student = Student::updateOrCreate(
                    [
                        'customer_id' => $referee->id,
                        'is_same_as_customer' => true,
                    ],
                    [
                        'full_name' => $referee->full_name,
                        'birth_date' => $data['student']['birth_date'] ?? null,
                        'gender' => $data['student']['gender'] ?? null,
                    ]
                );
            } else {
                $student = Student::create([
                    'customer_id' => $referee->id,
                    'full_name' => trim($data['student']['full_name']),
                    'birth_date' => $data['student']['birth_date'] ?? null,
                    'gender' => $data['student']['gender'] ?? null,
                    'is_same_as_customer' => false,
                    'medical_notes' => $data['student']['medical_notes'] ?? null,
                ]);
            }

            // 5. Generar código de referencia único
            $prefix = SystemSetting::get('referral_code_prefix', 'AQ-REF-');
            $uniqueCode = $prefix . strtoupper(bin2hex(random_bytes(3)));
            while (Referral::where('referral_code', $uniqueCode)->exists()) {
                $uniqueCode = $prefix . strtoupper(bin2hex(random_bytes(3)));
            }

            // 6. Crear el expediente de Referido
            $referral = Referral::create([
                'referral_code' => $uniqueCode,
                'referrer_customer_id' => $referrer->id,
                'referee_customer_id' => $referee->id,
                'referee_student_id' => $student->id,
                'service_id' => $data['service_id'],
                'status' => 'pending',
                'admin_notes' => $data['notes'] ?? null,
            ]);

            // 7. Guardar advertencias de duplicados si existen
            foreach ($analysis['flags'] as $flag) {
                DuplicateDetection::create([
                    'referral_id' => $referral->id,
                    'matched_field' => $flag['field'],
                    'matched_value' => $flag['value'],
                    'existing_reference_id' => $flag['reference_id'] ?? null,
                    'match_confidence' => $flag['confidence'],
                    'resolution_notes' => $flag['reason'] ?? null,
                ]);
            }

            // 8. Registrar en bitácora de actividades
            ReferralActivity::create([
                'referral_id' => $referral->id,
                'user_id' => null,
                'activity_type' => 'status_change',
                'description' => "Expediente registrado desde el formulario público web. Referente: {$referrer->full_name}, Referido: {$referee->full_name}.",
                'metadata' => [
                    'source' => 'public_form',
                    'flags_count' => count($analysis['flags']),
                ],
            ]);

            // 9. Enviar notificaciones inmediatas por WhatsApp al grupo y al referido
            try {
                if (SystemSetting::get('evolution_notify_admin_group', true)) {
                    NotifyAdminGroupNewReferralJob::dispatchSync($referral);
                }
                if (SystemSetting::get('evolution_auto_notify_referee', true)) {
                    ProcessWhatsAppNotificationJob::dispatchSync($referral, 'referee_welcome');
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Error enviando WhatsApp en nuevo referido: " . $e->getMessage());
            }

            return $referral;
        });
    }
}

<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Referral;
use App\Models\Student;

class DuplicateDetectionService
{
    /**
     * Normaliza un número telefónico costarricense al formato E.164 (506XXXXXXXX)
     */
    public function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleaned) === 8) {
            return '506' . $cleaned;
        }
        return $cleaned;
    }

    /**
     * Normaliza cédulas eliminando guiones o espacios
     */
    public function normalizeIdentification(?string $id): ?string
    {
        if (!$id) {
            return null;
        }
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $id));
    }

    /**
     * Evalúa si un intento de registro tiene duplicados o conflicto de elegibilidad
     *
     * @param array $referrerData [name, phone, email, identification]
     * @param array $refereeData [name, phone, email, identification]
     * @param array $studentData [name, birth_date, is_same]
     * @return array
     */
    public function analyze(array $referrerData, array $refereeData, array $studentData): array
    {
        $referrerPhone = $this->normalizePhone($referrerData['phone']);
        $refereePhone = $this->normalizePhone($refereeData['phone']);

        $referrerId = $this->normalizeIdentification($referrerData['identification'] ?? null);
        $refereeId = $this->normalizeIdentification($refereeData['identification'] ?? null);

        $flags = [];
        $isBlocked = false;
        $blockReason = null;

        // 1. Regla crítica: Una persona no puede auto-referirse
        if ($referrerPhone === $refereePhone) {
            return [
                'is_blocked' => true,
                'block_reason' => 'No es posible referirse a sí mismo. El teléfono del referente y del referido no pueden ser iguales.',
                'flags' => [
                    [
                        'field' => 'phone',
                        'value' => $refereePhone,
                        'confidence' => 100.0,
                        'reason' => 'Auto-referido por teléfono idéntico',
                    ]
                ]
            ];
        }

        if ($referrerId && $refereeId && $referrerId === $refereeId) {
            return [
                'is_blocked' => true,
                'block_reason' => 'No es posible referirse a sí mismo. La identificación del referente y del referido es la misma.',
                'flags' => [
                    [
                        'field' => 'identification',
                        'value' => $refereeId,
                        'confidence' => 100.0,
                        'reason' => 'Auto-referido por identificación idéntica',
                    ]
                ]
            ];
        }

        // 2. Comprobar si el referido ya tiene un expediente de referencia activo o reciente
        $existingReferral = Referral::whereHas('referee', function ($q) use ($refereePhone) {
            $q->where('phone', $refereePhone);
        })->whereIn('status', ['pending', 'in_contact', 'eligible', 'enrolled', 'paid', 'benefit_approved'])
          ->first();

        if ($existingReferral) {
            $flags[] = [
                'field' => 'phone',
                'value' => $refereePhone,
                'confidence' => 100.0,
                'reference_id' => $existingReferral->id,
                'reason' => "El referido ya tiene un expediente en curso ({$existingReferral->referral_code}) con estado {$existingReferral->status_label}.",
            ];

            // Si ya está aprobado o matriculado, se bloquea el re-registro comercial
            if (in_array($existingReferral->status, ['enrolled', 'paid', 'benefit_approved'])) {
                $isBlocked = true;
                $blockReason = "El cliente referido ya se encuentra matriculado o con beneficio aprobado en el expediente {$existingReferral->referral_code}.";
            }
        }

        // 3. Comprobar duplicidad de identificación del referido
        if ($refereeId) {
            $existingById = Referral::whereHas('referee', function ($q) use ($refereeId) {
                $q->where('identification', $refereeId);
            })->first();

            if ($existingById && (!$existingReferral || $existingById->id !== $existingReferral->id)) {
                $flags[] = [
                    'field' => 'identification',
                    'value' => $refereeId,
                    'confidence' => 100.0,
                    'reference_id' => $existingById->id,
                    'reason' => "La identificación ya está registrada en el expediente {$existingById->referral_code}.",
                ];
            }
        }

        // 4. Comprobar coincidencia fonética / similitud de nombre de alumno
        $studentName = trim($studentData['name'] ?? '');
        if (!empty($studentName) && !($studentData['is_same'] ?? false)) {
            $similarStudents = Student::all();
            foreach ($similarStudents as $existingStudent) {
                similar_text(mb_strtolower($studentName), mb_strtolower($existingStudent->full_name), $percent);
                if ($percent >= 85.0) {
                    $flags[] = [
                        'field' => 'student_name',
                        'value' => $studentName,
                        'confidence' => round($percent, 2),
                        'reference_id' => $existingStudent->id,
                        'reason' => "Nombre de alumno con {$percent}% de similitud con '{$existingStudent->full_name}'.",
                    ];
                    break;
                }
            }
        }

        return [
            'is_blocked' => $isBlocked,
            'block_reason' => $blockReason,
            'flags' => $flags,
        ];
    }
}

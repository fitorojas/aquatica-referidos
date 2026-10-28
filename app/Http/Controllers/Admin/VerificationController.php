<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BenefitHistory;
use App\Models\Referral;
use App\Models\ReferralActivity;
use App\Models\ReferralVerification;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    /**
     * Paso 1: Verificación manual de Elegibilidad
     */
    public function verifyEligibility(Request $request, Referral $referral)
    {
        $validated = $request->validate([
            'result' => ['required', 'in:approved,rejected,pending_info'],
            'observations' => ['required', 'string', 'max:1000'],
        ]);

        return DB::transaction(function () use ($validated, $referral) {
            ReferralVerification::create([
                'referral_id' => $referral->id,
                'user_id' => Auth::id(),
                'verification_type' => 'eligibility',
                'result' => $validated['result'],
                'observations' => $validated['observations'],
            ]);

            if ($validated['result'] === 'approved') {
                $referral->status = 'eligible';
                $referral->eligible_at = now();
            } elseif ($validated['result'] === 'rejected') {
                $referral->status = 'rejected';
                $referral->rejected_at = now();
            }
            $referral->save();

            ReferralActivity::create([
                'referral_id' => $referral->id,
                'user_id' => Auth::id(),
                'activity_type' => 'verification',
                'description' => "Verificación de Elegibilidad: " . ($validated['result'] === 'approved' ? 'APROBADA' : 'RECHAZADA') . ". {$validated['observations']}",
            ]);

            return back()->with('status', 'Verificación de elegibilidad registrada.');
        });
    }

    /**
     * Paso 2: Verificación manual de Matrícula
     */
    public function verifyEnrollment(Request $request, Referral $referral)
    {
        $validated = $request->validate([
            'result' => ['required', 'in:approved,rejected,pending_info'],
            'observations' => ['required', 'string', 'max:1000'],
        ]);

        return DB::transaction(function () use ($validated, $referral) {
            ReferralVerification::create([
                'referral_id' => $referral->id,
                'user_id' => Auth::id(),
                'verification_type' => 'enrollment',
                'result' => $validated['result'],
                'observations' => $validated['observations'],
            ]);

            if ($validated['result'] === 'approved') {
                $referral->status = 'enrolled';
                $referral->enrolled_at = now();
            }
            $referral->save();

            ReferralActivity::create([
                'referral_id' => $referral->id,
                'user_id' => Auth::id(),
                'activity_type' => 'verification',
                'description' => "Registro de Matrícula: " . ($validated['result'] === 'approved' ? 'CONFIRMADA' : 'OBSERVADA') . ". {$validated['observations']}",
            ]);

            return back()->with('status', 'Registro de matrícula formalizado.');
        });
    }

    /**
     * Paso 3: Verificación manual de Pago y Desbloqueo de Beneficio (₡30.800 para ambos)
     */
    public function verifyPayment(Request $request, Referral $referral)
    {
        if ($referral->benefit_approved_at || $referral->benefits()->exists()) {
            return back()->withErrors(['payment' => 'Los beneficios de ₡30.800 para este expediente ya fueron generados previamente y no pueden duplicarse.']);
        }

        $validated = $request->validate([
            'result' => ['required', 'in:approved,rejected,pending_info'],
            'receipt_number' => ['required_if:result,approved', 'nullable', 'string', 'max:100'],
            'payment_amount' => ['required_if:result,approved', 'nullable', 'numeric', 'min:0'],
            'proof_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB
            'observations' => ['required', 'string', 'max:1000'],
            'apply_period' => ['nullable', 'string', 'regex:/^\d{4}-\d{2}$/'], // Ej. 2026-11
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_document')) {
            $proofPath = $request->file('proof_document')->store('payment_proofs', 'public');
        }

        return DB::transaction(function () use ($validated, $proofPath, $referral) {
            ReferralVerification::create([
                'referral_id' => $referral->id,
                'user_id' => Auth::id(),
                'verification_type' => 'payment',
                'result' => $validated['result'],
                'receipt_number' => $validated['receipt_number'] ?? null,
                'payment_amount' => $validated['payment_amount'] ?? null,
                'proof_document_path' => $proofPath,
                'observations' => $validated['observations'],
            ]);

            if ($validated['result'] === 'approved') {
                $referral->status = 'benefit_approved';
                $referral->paid_at = now();
                $referral->benefit_approved_at = now();
                $referral->save();

                $benefitAmount = SystemSetting::get('referral_benefit_amount', 30800.00);
                $period = $validated['apply_period'] ?? date('Y-m');

                // Beneficio para el Referente (updateOrCreate previene duplicados)
                BenefitHistory::updateOrCreate(
                    [
                        'referral_id' => $referral->id,
                        'beneficiary_type' => 'referrer',
                    ],
                    [
                        'beneficiary_customer_id' => $referral->referrer_customer_id,
                        'discount_amount' => $benefitAmount,
                        'apply_to_period' => $period,
                        'status' => 'applied',
                        'authorized_by' => Auth::id(),
                        'notes' => "Beneficio generado por la matrícula y pago de {$referral->referee->full_name}.",
                    ]
                );

                // Beneficio para el Referido (updateOrCreate previene duplicados)
                BenefitHistory::updateOrCreate(
                    [
                        'referral_id' => $referral->id,
                        'beneficiary_type' => 'referee',
                    ],
                    [
                        'beneficiary_customer_id' => $referral->referee_customer_id,
                        'discount_amount' => $benefitAmount,
                        'apply_to_period' => $period,
                        'status' => 'applied',
                        'authorized_by' => Auth::id(),
                        'notes' => "Beneficio de bienvenida aplicado a la mensualidad por recomendación de {$referral->referrer->full_name}.",
                    ]
                );

                ReferralActivity::create([
                    'referral_id' => $referral->id,
                    'user_id' => Auth::id(),
                    'activity_type' => 'benefit_approval',
                    'description' => "¡PAGO VERIFICADO Y BENEFICIOS ACTIVADOS! Comprobante: {$validated['receipt_number']}. Descuento de ₡" . number_format($benefitAmount, 0) . " otorgado a {$referral->referrer->full_name} y a {$referral->referee->full_name}.",
                ]);

                // Notificar por WhatsApp al referente y al referido que el beneficio de ₡30.800 ya está activo
                try {
                    if (SystemSetting::get('evolution_auto_notify_referrer', true)) {
                        \App\Jobs\ProcessWhatsAppNotificationJob::dispatch($referral, 'referrer_benefit_approved');
                        \App\Jobs\ProcessWhatsAppNotificationJob::dispatch($referral, 'referee_benefit_approved');
                    }
                } catch (\Throwable $e) {
                    // No interrumpe la transacción administrativa
                }

                return back()->with('status', '¡Pago verificado exitosamente! Se generaron los beneficios de ₡' . number_format($benefitAmount, 0) . ' para ambas partes y se notificó por WhatsApp.');
            }

            $referral->save();
            return back()->with('status', 'Resultado de verificación de pago registrado.');
        });
    }
}

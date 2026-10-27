<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function referrals(Request $request): StreamedResponse
    {
        $status = $request->input('status');
        $serviceId = $request->input('service_id');

        $query = Referral::with(['referrer', 'referee', 'student', 'service'])
            ->latest();

        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($serviceId)) {
            $query->where('service_id', $serviceId);
        }

        $referrals = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="aquatica_referidos_' . date('Y-m-d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($referrals) {
            $handle = fopen('php://output', 'w');

            // BOM para compatibilidad con Excel en UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Código',
                'Fecha Registro',
                'Estado',
                'Referente - Nombre',
                'Referente - Teléfono',
                'Referente - Identificación',
                'Referido - Nombre',
                'Referido - Teléfono',
                'Referido - Identificación',
                'Alumno - Nombre',
                'Servicio',
                'Monto Beneficio',
                'Fecha Matrícula',
                'Fecha Pago',
                'Fecha Aprobación Beneficio',
            ]);

            foreach ($referrals as $ref) {
                fputcsv($handle, [
                    $ref->referral_code,
                    $ref->created_at->format('Y-m-d H:i'),
                    $ref->status_label,
                    $ref->referrer->full_name,
                    $ref->referrer->phone,
                    $ref->referrer->identification ?? 'N/D',
                    $ref->referee->full_name,
                    $ref->referee->phone,
                    $ref->referee->identification ?? 'N/D',
                    $ref->student->full_name ?? $ref->referee->full_name,
                    $ref->service->name,
                    '30800',
                    $ref->enrolled_at ? $ref->enrolled_at->format('Y-m-d') : 'N/D',
                    $ref->paid_at ? $ref->paid_at->format('Y-m-d') : 'N/D',
                    $ref->benefit_approved_at ? $ref->benefit_approved_at->format('Y-m-d') : 'N/D',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

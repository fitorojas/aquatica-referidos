<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BenefitHistory;
use App\Models\ReferralActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BenefitHistoryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $type = $request->input('type');

        $query = BenefitHistory::with(['referral.service', 'beneficiary', 'authorizer'])->latest();

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($type)) {
            $query->where('beneficiary_type', $type);
        }

        $benefits = $query->paginate(20)->withQueryString();

        $totalGranted = BenefitHistory::where('status', 'granted')->sum('discount_amount');
        $totalApplied = BenefitHistory::where('status', 'applied')->sum('discount_amount');

        return view('admin.benefits.index', compact('benefits', 'status', 'type', 'totalGranted', 'totalApplied'));
    }

    public function applyDiscount(Request $request, BenefitHistory $benefit)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $benefit->status = 'applied';
        if (!empty($validated['notes'])) {
            $benefit->notes = ($benefit->notes ? $benefit->notes . " | " : "") . "Aplicado: " . $validated['notes'];
        }
        $benefit->save();

        ReferralActivity::create([
            'referral_id' => $benefit->referral_id,
            'user_id' => Auth::id(),
            'activity_type' => 'benefit_applied',
            'description' => "Descuento de ₡" . number_format($benefit->discount_amount, 0) . " marcado como APLICADO en facturación para {$benefit->beneficiary->full_name}.",
        ]);

        return back()->with('status', 'Beneficio marcado como aplicado en la facturación del alumno.');
    }
}

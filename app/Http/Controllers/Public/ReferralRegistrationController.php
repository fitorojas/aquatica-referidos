<?php

namespace App\Http\Controllers\Public;

use App\Actions\Referrals\CreateReferralAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicReferralRequest;
use App\Models\Referral;
use App\Models\Service;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class ReferralRegistrationController extends Controller
{
    public function create()
    {
        // Aquafitness de primero en la lista
        $services = Service::where('is_active', true)
            ->orderByRaw("CASE WHEN slug = 'aquafitness' THEN 0 ELSE 1 END, id ASC")
            ->get();
        $benefitAmount = SystemSetting::get('referral_benefit_amount', 30800.00);

        return view('public.register', compact('services', 'benefitAmount'));
    }

    public function store(StorePublicReferralRequest $request, CreateReferralAction $action)
    {
        $referral = $action->execute($request->validated());

        return redirect()->route('public.referral.success', ['code' => $referral->referral_code])
            ->with('status', '¡Referencia registrada exitosamente!');
    }

    public function success(string $code)
    {
        $referral = Referral::with(['service', 'referee', 'referrer', 'student'])
            ->where('referral_code', $code)
            ->firstOrFail();

        $benefitAmount = SystemSetting::get('referral_benefit_amount', 30800.00);

        return view('public.success', compact('referral', 'benefitAmount'));
    }

    public function track(Request $request)
    {
        $search = trim($request->input('q', ''));
        $referral = null;

        if (!empty($search)) {
            $referral = Referral::with(['service', 'referee', 'referrer', 'student', 'verifications'])
                ->where('referral_code', $search)
                ->orWhereHas('referee', function ($q) use ($search) {
                    $cleaned = preg_replace('/[^0-9]/', '', $search);
                    $q->where('phone', 'like', "%{$cleaned}%")
                      ->orWhere('identification', $search);
                })
                ->latest()
                ->first();
        }

        return view('public.track', compact('referral', 'search'));
    }

    public function terms()
    {
        return view('public.terms');
    }
}

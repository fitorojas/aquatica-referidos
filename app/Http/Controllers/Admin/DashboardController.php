<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BenefitHistory;
use App\Models\DuplicateDetection;
use App\Models\Referral;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas Clave (KPIs)
        $totalReferrals = Referral::count();
        $pendingCount = Referral::whereIn('status', ['pending', 'in_contact'])->count();
        $enrolledCount = Referral::whereIn('status', ['enrolled', 'paid', 'benefit_approved'])->count();
        $benefitsApprovedCount = Referral::where('status', 'benefit_approved')->count();
        $benefitsTotalAmount = BenefitHistory::where('status', '!=', 'cancelled')->sum('discount_amount');
        $duplicateAlertsCount = DuplicateDetection::where('is_resolved', false)->count();

        // Tasa de conversión
        $conversionRate = $totalReferrals > 0 ? round(($enrolledCount / $totalReferrals) * 100, 1) : 0;

        // Distribución por Estado
        $statusCounts = Referral::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Distribución por Servicio
        $servicesStats = Service::withCount('referrals')->get();

        // Últimos 10 referidos registrados
        $recentReferrals = Referral::with(['referrer', 'referee', 'student', 'service'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalReferrals',
            'pendingCount',
            'enrolledCount',
            'benefitsApprovedCount',
            'benefitsTotalAmount',
            'duplicateAlertsCount',
            'conversionRate',
            'statusCounts',
            'servicesStats',
            'recentReferrals'
        ));
    }
}

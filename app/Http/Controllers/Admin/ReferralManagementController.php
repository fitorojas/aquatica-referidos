<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralActivity;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralManagementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $serviceId = $request->input('service_id');
        $search = trim($request->input('q', ''));

        $query = Referral::with(['referrer', 'referee', 'student', 'service', 'duplicateDetections'])
            ->latest();

        if ($status === 'duplicates') {
            $query->whereHas('duplicateDetections', function ($q) {
                $q->where('is_resolved', false);
            });
        } elseif ($status === 'pending_contact') {
            $query->whereIn('status', ['pending', 'in_contact']);
        } elseif (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($serviceId)) {
            $query->where('service_id', $serviceId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('referral_code', 'like', "%{$search}%")
                  ->orWhereHas('referrer', function ($qr) use ($search) {
                      $qr->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('referee', function ($qe) use ($search) {
                      $qe->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $referrals = $query->paginate(15)->withQueryString();
        $services = Service::where('is_active', true)->get();

        $tabCounts = [
            'all' => Referral::count(),
            'pending_contact' => Referral::whereIn('status', ['pending', 'in_contact'])->count(),
            'eligible' => Referral::where('status', 'eligible')->count(),
            'enrolled' => Referral::where('status', 'enrolled')->count(),
            'benefit_approved' => Referral::where('status', 'benefit_approved')->count(),
            'duplicates' => Referral::whereHas('duplicateDetections', fn($q) => $q->where('is_resolved', false))->count(),
        ];

        return view('admin.referrals.index', compact('referrals', 'services', 'status', 'serviceId', 'search', 'tabCounts'));
    }

    public function show(Referral $referral)
    {
        $referral->load([
            'referrer',
            'referee',
            'student',
            'service',
            'verifications.user',
            'benefits.beneficiary',
            'benefits.authorizer',
            'activities.user',
            'duplicateDetections.existingReference',
            'whatsappLogs',
        ]);

        return view('admin.referrals.show', compact('referral'));
    }

    public function updateStatus(Request $request, Referral $referral)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_contact,rejected,expired'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $referral->status_label;
        $referral->status = $validated['status'];

        if ($validated['status'] === 'in_contact' && !$referral->contacted_at) {
            $referral->contacted_at = now();
        } elseif ($validated['status'] === 'rejected' && !$referral->rejected_at) {
            $referral->rejected_at = now();
        }

        $referral->save();

        ReferralActivity::create([
            'referral_id' => $referral->id,
            'user_id' => Auth::id(),
            'activity_type' => 'status_change',
            'description' => "Cambio manual de estado de '{$oldStatus}' a '{$referral->status_label}'. " . ($validated['note'] ?? ''),
        ]);

        return back()->with('status', 'Estado del expediente actualizado correctamente.');
    }

    public function storeActivity(Request $request, Referral $referral)
    {
        $validated = $request->validate([
            'activity_type' => ['required', 'string', 'in:call,whatsapp_sent,meeting,admin_note'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        ReferralActivity::create([
            'referral_id' => $referral->id,
            'user_id' => Auth::id(),
            'activity_type' => $validated['activity_type'],
            'description' => $validated['description'],
        ]);

        return back()->with('status', 'Bitácora de seguimiento actualizada.');
    }
}

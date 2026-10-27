<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DuplicateDetection;
use App\Models\ReferralActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DuplicateAuditController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'unresolved');

        $query = DuplicateDetection::with(['referral.referrer', 'referral.referee', 'referral.service'])
            ->latest();

        if ($status === 'unresolved') {
            $query->where('is_resolved', false);
        } elseif ($status === 'resolved') {
            $query->where('is_resolved', true);
        }

        $detections = $query->paginate(20)->withQueryString();

        return view('admin.duplicates.index', compact('detections', 'status'));
    }

    public function resolve(Request $request, DuplicateDetection $detection)
    {
        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'max:500'],
        ]);

        $detection->is_resolved = true;
        $detection->resolution_notes = $validated['resolution_notes'];
        $detection->save();

        ReferralActivity::create([
            'referral_id' => $detection->referral_id,
            'user_id' => Auth::id(),
            'activity_type' => 'duplicate_resolved',
            'description' => "Alerta de duplicado ({$detection->matched_field}) resuelta por administración. Notas: {$validated['resolution_notes']}",
        ]);

        return back()->with('status', 'Alerta de duplicado resuelta.');
    }
}

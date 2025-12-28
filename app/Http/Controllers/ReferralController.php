<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function index()
    {
        $referrals = Referral::where('source_doctor_id', Auth::id())
            ->with('patient')
            ->latest()
            ->paginate(10);
            
        return view('referrals.index', compact('referrals'));
    }

    public function create()
    {
        $patients = Patient::whereHas('owners')->orderBy('name')->get();
        return view('referrals.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'target_clinic_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $referral = Referral::create([
            'source_doctor_id' => Auth::id(),
            'patient_id' => $request->patient_id,
            'target_clinic_name' => $request->target_clinic_name,
            'notes' => $request->notes,
            'access_token' => (string) Str::uuid(),
            'valid_until' => now()->addHours(48), // Auto-Secure 48 hours
        ]);

        return redirect()->route('referrals.index')->with('success', 'Referral created successfully. Share the link with the target clinic.');
    }

    // Public Endpoint
    public function showPublic($token)
    {
        $referral = Referral::where('access_token', $token)->firstOrFail();

        // Check Expiry
        if ($referral->valid_until->isPast()) {
            return response()->view('referrals.expired', [], 403);
        }

        return view('referrals.public_show', compact('referral'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('onboarding.index');
    }

    public function checkOrRegister(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:15',
            'patient_id' => 'required|exists:patients,id', // Context: Doctor adding a co-owner to a patient
        ]);

        $phone = $request->phone;
        $patient = Patient::findOrFail($request->patient_id);

        $user = User::where('phone', $phone)->first();
        $isNewUser = false;
        $tempPassword = null;

        if (!$user) {
            // Create new user
            $isNewUser = true;
            $tempPassword = Str::random(8);
            $user = User::create([
                'name' => 'New Client (' . $phone . ')', // Placeholder name
                'email' => $phone . '@birawa.vet', // Placeholder email
                'phone' => $phone,
                'password' => Hash::make($tempPassword),
                'role' => 'client', 
            ]);
        }

        // Attach to patient if not already attached
        if (!$patient->owners()->where('user_id', $user->id)->exists()) {
            $patient->owners()->attach($user->id, ['is_primary' => false]);
        }

        if ($isNewUser) {
            $message = "Halo, Anda telah didaftarkan di Birawa Vet sebagai pemilik hewan {$patient->name}. Password sementara Anda: {$tempPassword}. Silakan login di " . route('login');
        } else {
            $message = "Halo, Anda telah ditambahkan sebagai pemilik hewan {$patient->name} di Birawa Vet.";
        }

        $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

        return redirect()->away($whatsappUrl);
    }
}

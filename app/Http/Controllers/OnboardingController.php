<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
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

        if (! $user) {
            // Create new user
            $isNewUser = true;
            $tempPassword = Str::random(8);
            $user = User::create([
                'name' => 'New Client ('.$phone.')', // Placeholder name
                'email' => $phone.'@birawa.vet', // Placeholder email
                'phone' => $phone,
                'password' => Hash::make($tempPassword),
                'role' => 'client',
            ]);
        }

        // Ensure user has a client profile
        $client = \App\Models\Client::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'phone' => $user->phone,
                'address' => $user->address ?? 'Address pending',
            ]
        );

        // Update patient owner
        // Note: System now supports single owner (Client). This action will transfer ownership.
        if ($patient->client_id !== $client->id) {
            $patient->client_id = $client->id;
            $patient->save();
        }

        if ($isNewUser) {
            $message = "Halo, Anda telah didaftarkan di Birawa Vet sebagai pemilik hewan {$patient->name}. Password sementara Anda: {$tempPassword}. Silakan login di ".route('login');
        } else {
            $message = "Halo, Anda telah ditambahkan sebagai pemilik hewan {$patient->name} di Birawa Vet.";
        }

        $whatsappUrl = "https://wa.me/{$phone}?text=".urlencode($message);

        return redirect()->away($whatsappUrl);
    }
}

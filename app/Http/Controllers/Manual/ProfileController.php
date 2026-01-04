<?php

namespace App\Http\Controllers\Manual;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Services\TimezoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    protected $timezoneService;

    public function __construct(TimezoneService $timezoneService)
    {
        $this->timezoneService = $timezoneService;
    }

    public function edit()
    {
        $user = Auth::user();
        $doctorProfile = $user->doctorProfile;

        // If no doctor profile exists, instantiate an empty one for the form (not persisted)
        if (! $doctorProfile) {
            $doctorProfile = new DoctorProfile([
                'user_id' => $user->id,
                'timezone' => config('app.timezone'),
                'service_radius_km' => 0,
                'base_transport_fee' => 0,
                'transport_fee_per_km' => 0,
            ]);
        }

        $timezones = $this->timezoneService->getTimezonesForSelect();

        return view('manual.profile.edit', compact('user', 'doctorProfile', 'timezones'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            // Password only if provided
            'password' => 'nullable|string|min:8|confirmed',

            // Doctor Profile
            'specialty' => 'nullable|string|max:100',
            'timezone' => 'required|string',
            'service_radius_km' => 'required|numeric|min:0',
            'base_transport_fee' => 'required|numeric|min:0',
            'transport_fee_per_km' => 'required|numeric|min:0',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_number' => 'nullable|string|max:20',
            'bank_account_details' => 'nullable|string|max:1000',
            'bio' => 'nullable|string|max:2000',
        ];

        $validated = $request->validate($rules);

        // Update User
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Update or Create Doctor Profile
        $profileData = [
            'specialty' => $validated['specialty'],
            'timezone' => $validated['timezone'],
            'service_radius_km' => $validated['service_radius_km'],
            'base_transport_fee' => $validated['base_transport_fee'],
            'transport_fee_per_km' => $validated['transport_fee_per_km'],
            'emergency_contact_name' => $validated['emergency_contact_name'],
            'emergency_contact_number' => $validated['emergency_contact_number'],
            'bank_account_details' => $validated['bank_account_details'],
            'bio' => $validated['bio'],
        ];

        if ($user->doctorProfile) {
            $user->doctorProfile->update($profileData);
        } else {
            $user->doctorProfile()->create($profileData);
        }

        return redirect()->route('manual.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = Visit::with(['patient.owners', 'user', 'invoice', 'medicalRecords'])->latest('scheduled_at')->paginate(10);
        return view('visits.index', compact('visits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patients = Patient::with('owners')->orderBy('name')->get();
        $selectedPatientId = $request->query('patient_id');
        // For simplicity, we assign the current user (doctor/admin) to the visit
        // In a real app, you might want to select a doctor
        return view('visits.create', compact('patients', 'selectedPatientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'scheduled_at' => 'required|date',
            'complaint' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'scheduled'; // Default status

        // Calculate distance if coordinates are provided
        $doctorProfile = Auth::user()->doctorProfile;
        if ($doctorProfile && $request->filled('latitude') && $request->filled('longitude') && $doctorProfile->latitude && $doctorProfile->longitude) {
            $distance = $this->calculateDistance(
                $doctorProfile->latitude,
                $doctorProfile->longitude,
                $request->latitude,
                $request->longitude
            );
            
            $data['distance_km'] = $distance;

            // Check Service Radius
            if ($doctorProfile->service_radius_km > 0 && $distance > $doctorProfile->service_radius_km) {
                return back()->withErrors(['address' => 'Location is outside of service radius (' . number_format($distance, 1) . ' km).']);
            }
            
            // Auto-calculate transport fee
            // Formula: Base Fee + (Distance * Rate per KM)
            $transportFee = $doctorProfile->base_transport_fee + ($distance * $doctorProfile->transport_fee_per_km);
            $data['transport_fee'] = round($transportFee, -2); // Round to nearest 100
        }

        Visit::create($data);

        return redirect()->route('visits.index')
            ->with('success', 'Visit scheduled successfully.');
    }

    /**
     * Update visit status.
     */
    public function updateStatus(Request $request, Visit $visit)
    {
        $request->validate([
            'status' => 'required|in:scheduled,otw,arrived,completed,cancelled',
        ]);

        $visit->update(['status' => $request->status]);

        return back()->with('success', 'Visit status updated to ' . ucfirst($request->status));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit)
    {
        return view('visits.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit)
    {
        $patients = Patient::with('owners')->orderBy('name')->get();
        return view('visits.edit', compact('visit', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visit $visit)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'scheduled_at' => 'required|date',
            'complaint' => 'nullable|string',
            'transport_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $visit->update($request->all());

        return redirect()->route('visits.index')
            ->with('success', 'Visit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        $visit->delete();

        return redirect()->route('visits.index')
            ->with('success', 'Visit deleted successfully.');
    }
}

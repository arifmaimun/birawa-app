<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Patient;
use App\Models\User;
use App\Models\VisitStatus;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $visits = Visit::with(['patient.client', 'user', 'invoice', 'medicalRecords', 'visitStatus'])
            ->where('user_id', Auth::id()) // SCOPED: Only show visits for the logged-in doctor
            ->when($search, function ($query) use ($search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('patient.client', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest('scheduled_at')
            ->paginate(10);
            
        return view('visits.index', compact('visits', 'search'));
    }

    public function calendar()
    {
        return view('visits.calendar');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // SCOPED: Only show patients linked to the doctor
        $user = Auth::user();
        $patients = Patient::with('client')
            ->where(function($q) use ($user) {
                $q->whereHas('visits', function($v) use ($user) {
                    $v->where('user_id', $user->id);
                })
                ->orWhereHas('medicalRecords', function($m) use ($user) {
                    $m->where('doctor_id', $user->id);
                });
            })
            ->orderBy('name')
            ->get();
            
        $selectedPatientId = $request->query('patient_id');

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
        // Default status: Scheduled
        $defaultStatus = VisitStatus::where('slug', 'scheduled')->first();
        $data['visit_status_id'] = $defaultStatus ? $defaultStatus->id : null;

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
        // SCOPED: Ensure the visit belongs to the current user
        if ($visit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|exists:visit_statuses,slug',
        ]);

        $status = VisitStatus::where('slug', $request->status)->firstOrFail();
        $visit->update(['visit_status_id' => $status->id]);

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
        if ($visit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('visits.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = Auth::user();
        $patients = Patient::with('client')
            ->where(function($q) use ($user) {
                $q->whereHas('visits', function($v) use ($user) {
                    $v->where('user_id', $user->id);
                })
                ->orWhereHas('medicalRecords', function($m) use ($user) {
                    $m->where('doctor_id', $user->id);
                });
            })
            ->orderBy('name')
            ->get();
            
        return view('visits.edit', compact('visit', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'scheduled_at' => 'required|date',
            'complaint' => 'nullable|string',
            'transport_fee' => 'nullable|numeric|min:0',
            'visit_status_id' => 'required|exists:visit_statuses,id',
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
        if ($visit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $visit->delete();

        return redirect()->route('visits.index')
            ->with('success', 'Visit deleted successfully.');
    }

    public function calendarEvents(Request $request)
    {
        try {
            $start = $request->query('start');
            $end = $request->query('end');

            $visits = Visit::where('user_id', Auth::id())
                ->whereBetween('scheduled_at', [$start, $end])
                ->with(['patient.client', 'visitStatus'])
                ->get();

            $events = $visits->map(function ($visit) {
                $color = $visit->visitStatus->color ?? '#6B7280';
                
                $patientName = $visit->patient->name ?? 'Unknown';
                $clientName = $visit->patient->client->name ?? 'No Client';

                return [
                    'id' => $visit->id,
                    'title' => "$patientName ($clientName)",
                    'start' => $visit->scheduled_at,
                    'url' => route('visits.show', $visit->id),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                ];
            });

            return response()->json($events);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Calendar Events Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch events'], 500);
        }
    }

    public function startTrip(Request $request, Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $departureTime = Carbon::now();
        $visit->departure_time = $departureTime;
        
        if ($request->estimated_hours) {
            $minutes = $request->estimated_hours * 60;
            $visit->estimated_travel_minutes = $minutes;
        }
        
        // Update status to 'otw' if possible
        $otwStatus = VisitStatus::where('slug', 'otw')->orWhere('slug', 'on-the-way')->first();
        if ($otwStatus) {
            $visit->visit_status_id = $otwStatus->id;
            // $visit->status = 'otw'; // Legacy fallback removed
        }

        $visit->save();

        // Check for message template
        $template = MessageTemplate::where('doctor_id', Auth::id())
            ->where('trigger_event', 'on_departure')
            ->first();

        $whatsappUrl = null;
        if ($template && $visit->patient->client && $visit->patient->client->phone) {
            $message = $this->formatMessage($template->content_pattern, $visit);
            $phone = $this->formatPhoneNumber($visit->patient->client->phone);
            $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
        }

        return response()->json([
            'success' => true,
            'message' => 'Perjalanan dimulai.',
            'whatsapp_url' => $whatsappUrl
        ]);
    }

    public function endTrip(Request $request, Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        $arrivalTime = Carbon::now();
        $visit->arrival_time = $arrivalTime;
        
        if ($visit->departure_time) {
            $start = Carbon::parse($visit->departure_time);
            $visit->actual_travel_minutes = $start->diffInMinutes($arrivalTime);
        }
        
        // Update status to 'arrived'
        $arrivedStatus = VisitStatus::where('slug', 'arrived')->first();
        if ($arrivedStatus) {
            $visit->visit_status_id = $arrivedStatus->id;
            // $visit->status = 'arrived'; // Legacy fallback removed
        }

        $visit->save();

        // Check for message template
        $template = MessageTemplate::where('doctor_id', Auth::id())
            ->where('trigger_event', 'on_arrival')
            ->first();

        $whatsappUrl = null;
        if ($template && $visit->patient->client && $visit->patient->client->phone) {
            $message = $this->formatMessage($template->content_pattern, $visit);
            $phone = $this->formatPhoneNumber($visit->patient->client->phone);
            $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sampai di lokasi.',
            'whatsapp_url' => $whatsappUrl,
            'duration_report' => $visit->actual_travel_minutes ? "Total perjalanan: {$visit->actual_travel_minutes} menit." : null
        ]);
    }

    private function formatMessage($pattern, $visit)
    {
        $replacements = [
            '{owner_name}' => $visit->patient->client->name ?? 'Owner',
            '{doctor_name}' => $visit->user->name ?? 'Dokter',
            '{patient_name}' => $visit->patient->name ?? 'Hewan',
            '{address}' => $visit->patient->client->address ?? '',
        ];
        
        if (strpos($pattern, '{eta}') !== false) {
            if ($visit->departure_time && $visit->estimated_travel_minutes) {
                $eta = Carbon::parse($visit->departure_time)->addMinutes($visit->estimated_travel_minutes)->format('H:i');
                $replacements['{eta}'] = $eta;
            } else {
                $replacements['{eta}'] = '(belum ada estimasi)';
            }
        }

        return str_replace(array_keys($replacements), array_values($replacements), $pattern);
    }
    
    private function formatPhoneNumber($phone)
    {
        // Simple formatter: replace 08 with 628, remove non-digits
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}

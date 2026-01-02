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
        $request->validate([
            'status' => ['nullable', function ($attribute, $value, $fail) {
                $slugs = is_array($value) ? $value : [$value];
                foreach ($slugs as $slug) {
                    if (!is_string($slug) || !VisitStatus::where('slug', $slug)->exists()) {
                        $fail('The selected status is invalid: ' . $slug);
                    }
                }
            }],
            'search' => 'nullable|string|max:100',
        ]);

        $search = $request->input('search');
        $status = $request->input('status');
        
        $visits = Visit::with(['patient.client', 'user', 'invoice', 'medicalRecords', 'visitStatus'])
            ->where('user_id', Auth::id()) // SCOPED: Only show visits for the logged-in doctor
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('patient', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    })->orWhereHas('patient.client', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->whereHas('visitStatus', function ($q) use ($status) {
                    if (is_array($status)) {
                        $q->whereIn('slug', $status);
                    } else {
                        $q->where('slug', $status);
                    }
                });
            })
            ->latest('scheduled_at')
            ->paginate(10)
            ->withQueryString();
            
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
        $user = Auth::user();
        $selectedPatientId = $request->query('patient_id');
        $search = $request->query('search');

        // Allow searching all patients, not just those with history
        $patients = Patient::with('client')
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('client', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            })
            ->when($selectedPatientId, function($q) use ($selectedPatientId) {
                 // Ensure selected patient is prioritized or included if we were paginating
                 // With limit, this ensures if we select one, it's definitely in the list
                 $q->orWhere('id', $selectedPatientId);
            })
            ->orderBy('name')
            ->limit(500) // Increase limit to reasonable amount for dropdown
            ->get();

        return view('visits.create', compact('patients', 'selectedPatientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Merge date/time if provided separately
        if ($request->has(['scheduled_date', 'scheduled_time']) && !$request->has('scheduled_at')) {
            $request->merge([
                'scheduled_at' => $request->scheduled_date . ' ' . $request->scheduled_time
            ]);
        }

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

        // Get prediction if not already set manually? 
        // For now, we don't set 'actual_travel_minutes' on creation, but we could return prediction in response if API
        
        Visit::create($data);

        return redirect()->route('visits.index')
            ->with('success', 'Visit scheduled successfully.');
    }

    /**
     * Get predicted travel time in minutes based on history
     */
    protected function getPredictedTravelTime($patientId, $currentDistanceKm = null)
    {
        // Strategy: 
        // 1. Find completed visits for this patient with actual_travel_minutes recorded
        // 2. Average them
        // 3. If no history for patient, look for visits with similar distance (+/- 10%)
        
        // 1. Patient history
        $avgPatientTime = Visit::where('patient_id', $patientId)
            ->whereNotNull('actual_travel_minutes')
            ->where('actual_travel_minutes', '>', 0)
            ->avg('actual_travel_minutes');
            
        if ($avgPatientTime) {
            return round($avgPatientTime);
        }

        // 2. Distance-based approximation if we have distance
        if ($currentDistanceKm) {
             $avgDistanceTime = Visit::whereNotNull('actual_travel_minutes')
                ->where('actual_travel_minutes', '>', 0)
                ->whereBetween('distance_km', [$currentDistanceKm * 0.9, $currentDistanceKm * 1.1])
                ->avg('actual_travel_minutes');

            if ($avgDistanceTime) {
                return round($avgDistanceTime);
            }

            // Fallback: 2 minutes per km + 5 mins buffer
            return round(($currentDistanceKm * 2) + 5);
        }

        return null; // Unknown
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
            'distance_km' => 'nullable|numeric|min:0',
            'actual_travel_minutes' => 'nullable|integer|min:0',
        ]);

        $status = VisitStatus::where('slug', $request->status)->firstOrFail();
        
        $data = ['visit_status_id' => $status->id];
        
        // Handle side effects based on status change
        if ($request->status === 'otw' || $request->status === 'on-the-way') {
            if (!$visit->departure_time) {
                $data['departure_time'] = now();
            }
        } elseif ($request->status === 'arrived') {
            if (!$visit->arrival_time) {
                $data['arrival_time'] = now();
                // Calculate duration if not provided manually and departure time exists
                if (!$request->has('actual_travel_minutes') && $visit->departure_time) {
                    $start = Carbon::parse($visit->departure_time);
                    $data['actual_travel_minutes'] = $start->diffInMinutes($data['arrival_time']);
                }
            }
        } elseif ($request->status === 'completed') {
             if (!$visit->arrival_time) {
                $data['arrival_time'] = now(); // Ensure arrival time is set if jumped straight to completed
            }
        }

        if ($request->has('distance_km')) {
            $data['distance_km'] = $request->distance_km;
        }
        if ($request->has('actual_travel_minutes')) {
            $data['actual_travel_minutes'] = $request->actual_travel_minutes;
        }

        $visit->update($data);

        if ($request->wantsJson()) {
            $visit->load(['patient.client', 'user', 'invoice', 'medicalRecords', 'visitStatus']);
            $visit->status = $visit->visitStatus?->slug;
            return response()->json($visit);
        }

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
    public function show(Request $request, Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            $visit->load(['patient.client', 'user', 'invoice', 'medicalRecords.doctor', 'visitStatus']);
            
            // Map status slug to simple status string if needed by frontend
            $visit->status = $visit->visitStatus?->slug ?? 'scheduled';
            
            // Add predicted travel time
            if (!$visit->actual_travel_minutes && $visit->status !== 'completed' && $visit->status !== 'cancelled') {
                $visit->predicted_travel_minutes = $this->getPredictedTravelTime($visit->patient_id, $visit->distance_km);
            }
            
            return response()->json($visit);
        }

        return view('react_spa');
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
                ->orWhereHas('medical_records', function($m) use ($user) {
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
            'estimated_minutes' => 'nullable|integer|min:0',
            'distance_km' => 'nullable|numeric|min:0',
        ]);

        $departureTime = Carbon::now();
        $visit->departure_time = $departureTime;
        
        if ($request->estimated_minutes) {
            $visit->estimated_travel_minutes = $request->estimated_minutes;
        } elseif ($request->estimated_hours) {
            $visit->estimated_travel_minutes = $request->estimated_hours * 60;
        }

        if ($request->has('distance_km')) {
            $visit->distance_km = $request->distance_km;
        }
        
        // Update status to 'otw' if possible
        $otwStatus = VisitStatus::where('slug', 'otw')->orWhere('slug', 'on-the-way')->first();
        if ($otwStatus) {
            $visit->visit_status_id = $otwStatus->id;
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

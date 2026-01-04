<?php

namespace App\Http\Controllers\Manual\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\VisitStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::query()
            ->with(['patient', 'visitStatus'])
            ->where('user_id', Auth::id());

        if ($search = $request->input('search')) {
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('visit_status_id', $status);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('scheduled_at', $date);
        }

        $visits = $query->latest('scheduled_at')->paginate(10);
        $statuses = VisitStatus::orderBy('name')->get();

        return view('manual.clinical.visits.index', compact('visits', 'statuses'));
    }

    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        $statuses = VisitStatus::orderBy('order')->get();
        
        return view('manual.clinical.visits.create', compact('patients', 'statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'scheduled_at' => 'required|date',
            'complaint' => 'nullable|string',
            'visit_status_id' => 'required|exists:visit_statuses,id',
            'transport_fee' => 'nullable|numeric|min:0',
        ]);

        Visit::create([
            'user_id' => Auth::id(),
            'patient_id' => $request->patient_id,
            'scheduled_at' => $request->scheduled_at,
            'complaint' => $request->complaint,
            'visit_status_id' => $request->visit_status_id,
            'transport_fee' => $request->transport_fee ?? 0,
        ]);

        return redirect()->route('manual.visits.index')
            ->with('success', 'Visit scheduled successfully.');
    }

    public function show(Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        return view('manual.clinical.visits.show', compact('visit'));
    }

    public function edit(Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        $patients = Patient::orderBy('name')->get();
        $statuses = VisitStatus::orderBy('order')->get();

        return view('manual.clinical.visits.edit', compact('visit', 'patients', 'statuses'));
    }

    public function update(Request $request, Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'scheduled_at' => 'required|date',
            'complaint' => 'nullable|string',
            'visit_status_id' => 'required|exists:visit_statuses,id',
            'transport_fee' => 'nullable|numeric|min:0',
        ]);

        $visit->update([
            'patient_id' => $request->patient_id,
            'scheduled_at' => $request->scheduled_at,
            'complaint' => $request->complaint,
            'visit_status_id' => $request->visit_status_id,
            'transport_fee' => $request->transport_fee,
        ]);

        return redirect()->route('manual.visits.index')
            ->with('success', 'Visit updated successfully.');
    }

    public function destroy(Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        $visit->delete();

        return redirect()->route('manual.visits.index')
            ->with('success', 'Visit removed successfully.');
    }
}

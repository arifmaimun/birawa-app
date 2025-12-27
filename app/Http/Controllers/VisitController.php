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
        $visits = Visit::with(['patient.owner', 'user'])->latest('scheduled_at')->paginate(10);
        return view('visits.index', compact('visits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patients = Patient::with('owner')->orderBy('name')->get();
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
            'transport_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        // Automatically assign current user if not provided (though we don't have user selection in UI yet)
        $data = $request->all();
        if (!isset($data['user_id'])) {
            $data['user_id'] = Auth::id() ?? User::first()->id; // Fallback for dev if no auth
        }

        Visit::create($data);

        return redirect()->route('visits.index')
            ->with('success', 'Visit scheduled successfully.');
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
        $patients = Patient::with('owner')->orderBy('name')->get();
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

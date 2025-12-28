<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // SCOPED: Only show patients that have been visited by the current doctor
        // or have medical records with the current doctor.
        // Also include patients created by the doctor (if we had that field, but we don't).
        // So we stick to Visits and Medical Records.
        $user = Auth::user();
        
        $patients = Patient::with('owners')
            ->where(function($q) use ($user) {
                $q->whereHas('visits', function($v) use ($user) {
                    $v->where('user_id', $user->id);
                })
                ->orWhereHas('medicalRecords', function($m) use ($user) {
                    $m->where('doctor_id', $user->id);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('owners', function ($o) use ($search) {
                          $o->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(10);

        return view('patients.index', compact('patients', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Fetch users who are clients
        $owners = User::where('role', 'client')->orderBy('name')->get();
        $selectedOwnerId = $request->query('owner_id');
        return view('patients.create', compact('owners', 'selectedOwnerId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:100',
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:jantan,betina',
            'dob' => 'nullable|date',
        ]);

        $patient = Patient::create($request->except('owner_id'));
        
        // Attach the selected owner as primary
        $patient->owners()->attach($request->owner_id, ['is_primary' => true]);

        return redirect()->route('patients.index')
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $owners = User::where('role', 'client')->orderBy('name')->get();
        return view('patients.edit', compact('patient', 'owners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'owner_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:100',
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:jantan,betina',
            'dob' => 'nullable|date',
        ]);

        $patient->update($request->except('owner_id'));

        // Sync owners (assuming single owner selection in UI for now, effectively replacing primary owner)
        // In a real multi-owner UI, we would handle multiple IDs.
        // For now, we update the relationship to the selected user.
        $patient->owners()->sync([$request->owner_id => ['is_primary' => true]]);

        return redirect()->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
}

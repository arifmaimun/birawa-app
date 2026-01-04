<?php

namespace App\Http\Controllers\Manual\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query()->with('client');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('client', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
        }

        $patients = $query->latest()->paginate(10);

        return view('manual.clinical.patients.index', compact('patients'));
    }

    public function create()
    {
        // For dropdown, limit to recent or provide search via AJAX in real app
        // For MVP, just get all clients sorted by name
        $clients = Client::orderBy('name')->get();

        return view('manual.clinical.patients.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:50',
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:Male,Female',
            'dob' => 'nullable|date',
            'is_sterile' => 'boolean',
            'allergies' => 'nullable|string',
            'special_conditions' => 'nullable|string',
        ]);

        $patient = Patient::create($request->all());

        // Also attach to pivot if needed, but client_id handles primary ownership
        if (! $patient->clients()->where('client_id', $request->client_id)->exists()) {
            $patient->clients()->attach($request->client_id);
        }

        return redirect()->route('manual.patients.index')
            ->with('success', 'Patient registered successfully.');
    }

    public function edit(Patient $patient)
    {
        $clients = Client::orderBy('name')->get();

        return view('manual.clinical.patients.edit', compact('patient', 'clients'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:50',
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:Male,Female',
            'dob' => 'nullable|date',
            'is_sterile' => 'boolean',
            'allergies' => 'nullable|string',
            'special_conditions' => 'nullable|string',
        ]);

        $patient->update($request->all());

        // Sync primary client to pivot
        if (! $patient->clients()->where('client_id', $request->client_id)->exists()) {
            $patient->clients()->attach($request->client_id);
        }

        return redirect()->route('manual.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('manual.patients.index')
            ->with('success', 'Patient record removed successfully.');
    }
}

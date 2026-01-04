<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::with('client')->latest()->paginate(10);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($patients);
        }

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();

        return view('patients.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'required|string|in:Jantan,Betina,Tidak Diketahui',
            'is_sterile' => 'nullable|in:0,1',
            'client_id' => 'required|exists:clients,id',
        ]);

        Patient::create($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient created successfully.');
    }

    public function show(Request $request, Patient $patient)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            $patient->load(['client', 'medical_records.diagnoses']);

            return response()->json($patient);
        }

        return view('patients.show', compact('patient'));
    }

    public function edit(Request $request, Patient $patient)
    {
        $clients = Client::orderBy('name')->get();

        if ($request->ajax()) {
            return view('patients.partials.edit-form', compact('patient', 'clients'))->render();
        }

        return view('patients.edit', compact('patient', 'clients'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'required|string|in:Jantan,Betina,Tidak Diketahui',
            'is_sterile' => 'nullable|in:0,1',
            'client_id' => 'required|exists:clients,id',
        ]);

        $patient->update($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }
}

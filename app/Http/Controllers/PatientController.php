<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('client')->latest()->paginate(10);
        $clients = Client::orderBy('name')->get(); // Needed for Create Modal
        return view('patients.index', compact('patients', 'clients'));
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

    public function show(Patient $patient)
    {
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

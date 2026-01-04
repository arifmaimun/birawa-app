<?php

namespace App\Http\Controllers\Manual\Clinical;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalRecord::query()
            ->with(['patient', 'patient.client'])
            ->where('doctor_id', Auth::id());

        if ($search = $request->input('search')) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $records = $query->latest()->paginate(10);

        return view('manual.clinical.medical_records.index', compact('records'));
    }

    public function create(Request $request)
    {
        $patientId = $request->input('patient_id');
        $visitId = $request->input('visit_id');

        $patient = null;
        if ($patientId) {
            $patient = Patient::findOrFail($patientId);
        }

        $visit = null;
        if ($visitId) {
            $visit = Visit::findOrFail($visitId);
            // Ensure patient matches
            if ($patient && $visit->patient_id !== $patient->id) {
                abort(400, 'Patient mismatch');
            }
            if (! $patient) {
                $patient = $visit->patient;
            }
        }

        // For dropdown, limit to recent or provide search via AJAX in real app
        // For MVP, just get all clients sorted by name
        // If we are creating from scratch without patient, we might need a search field in the form.
        // For MVP, let's assume we start from Patient list or have a patient_id.
        // If not, we'll fetch all patients (bad for performance, but ok for MVP with limited data).
        $patients = Patient::orderBy('name')->get();

        return view('manual.clinical.medical_records.create', compact('patient', 'patients', 'visit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'subjective' => 'required|string',
            'objective' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan_treatment' => 'nullable|string',
            'plan_recipe' => 'nullable|string',
        ]);

        $visitId = $request->visit_id;

        if (! $visitId) {
            // Auto-create a Visit for this record if not provided
            $visit = Visit::create([
                'patient_id' => $request->patient_id,
                'user_id' => Auth::id(), // Doctor
                'scheduled_at' => now(),
                // Find a default status, or use ID 1 (Scheduled) or similar.
                // Better to query 'completed' or 'in-progress' if possible, but fallback to 1.
                'visit_status_id' => 1,
                'complaint' => 'Manual Entry (Direct Medical Record)',
                // Default location
                'latitude' => 0,
                'longitude' => 0,
                'distance_km' => 0,
                'actual_travel_minutes' => 0,
            ]);
            $visitId = $visit->id;
        }

        $record = MedicalRecord::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => Auth::id(),
            'visit_id' => $visitId,
            'subjective' => $request->subjective,
            'objective' => $request->objective,
            'assessment' => $request->assessment,
            'plan_treatment' => $request->plan_treatment,
            'plan_recipe' => $request->plan_recipe,
            'is_locked' => false,
        ]);

        return redirect()->route('manual.medical-records.index')
            ->with('success', 'Medical Record created successfully.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        // Check access
        if ($medicalRecord->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this medical record.');
        }

        return view('manual.clinical.medical_records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        if ($medicalRecord->doctor_id !== Auth::id()) {
            abort(403);
        }
        if ($medicalRecord->is_locked) {
            return redirect()->back()->with('error', 'This record is locked and cannot be edited.');
        }

        return view('manual.clinical.medical_records.edit', compact('medicalRecord'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        if ($medicalRecord->doctor_id !== Auth::id()) {
            abort(403);
        }
        if ($medicalRecord->is_locked) {
            return redirect()->back()->with('error', 'This record is locked and cannot be edited.');
        }

        $request->validate([
            'subjective' => 'required|string',
            'objective' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan_treatment' => 'nullable|string',
            'plan_recipe' => 'nullable|string',
        ]);

        $medicalRecord->update([
            'subjective' => $request->subjective,
            'objective' => $request->objective,
            'assessment' => $request->assessment,
            'plan_treatment' => $request->plan_treatment,
            'plan_recipe' => $request->plan_recipe,
        ]);

        return redirect()->route('manual.medical-records.show', $medicalRecord)
            ->with('success', 'Medical Record updated successfully.');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        if ($medicalRecord->doctor_id !== Auth::id()) {
            abort(403);
        }
        if ($medicalRecord->is_locked) {
            return redirect()->back()->with('error', 'This record is locked and cannot be deleted.');
        }

        $medicalRecord->delete();

        return redirect()->route('manual.medical-records.index')
            ->with('success', 'Medical Record deleted successfully.');
    }
}

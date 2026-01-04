<?php

namespace App\Http\Controllers;

use App\DTOs\MedicalRecordDTO;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Models\AccessRequest;
use App\Models\Diagnosis;
use App\Models\DoctorServiceCatalog;
use App\Models\MedicalRecord;
use App\Models\Visit;
use App\Models\VitalSignSetting;
use App\Services\MedicalRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function __construct(protected MedicalRecordService $medicalRecordService) {}

    public function create(Visit $visit)
    {
        // Ensure the visit belongs to the doctor or doctor has access
        if ($visit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $inventories = $user->inventories()->with('storageLocation')->orderBy('item_name')->get();
        $services = DoctorServiceCatalog::where('user_id', $user->id)->orderBy('service_name')->get();
        $diagnoses = Diagnosis::forUser($user->id)->orderBy('category')->orderBy('name')->get();

        // Fetch Medical History
        $medicalHistory = MedicalRecord::where('patient_id', $visit->patient_id)
            ->where('id', '!=', $visit->id) // Exclude current if somehow it existed, though we are creating new
            ->with(['doctor', 'diagnoses', 'vitalSign'])
            ->latest()
            ->take(5)
            ->get();

        $vitalSignSettings = VitalSignSetting::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        return view('medical_records.create', compact('visit', 'inventories', 'services', 'diagnoses', 'medicalHistory', 'vitalSignSettings'));
    }

    public function store(StoreMedicalRecordRequest $request, Visit $visit)
    {
        $dto = MedicalRecordDTO::fromRequest($request);
        $record = $this->medicalRecordService->createMedicalRecord($visit, $dto);

        if ($request->wantsJson()) {
            return response()->json($record, 201);
        }

        return redirect()->route('visits.show', $visit->id)->with('success', 'Medical Record saved successfully.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        // Audit Log: Read Access
        \Illuminate\Support\Facades\Log::info("User {$user->id} viewed Medical Record {$medicalRecord->id} at ".now());

        // 1. If user is the creator (doctor), grant access
        if ($medicalRecord->doctor_id === $user->id) {
            return view('medical_records.show', compact('medicalRecord'));
        }

        // 2. If user has been granted access
        if ($medicalRecord->hasAccessGranted($user->id)) {
            return view('medical_records.show', compact('medicalRecord'));
        }

        // 3. Otherwise, show locked view with Request Access button
        // Check if there is a pending request
        $pendingRequest = AccessRequest::where('target_medical_record_id', $medicalRecord->id)
            ->where('requester_doctor_id', $user->id)
            ->where('status', 'pending')
            ->first();

        return view('medical_records.locked', compact('medicalRecord', 'pendingRequest'));
    }

    public function requestAccess(Request $request, MedicalRecord $medicalRecord)
    {
        $existing = AccessRequest::where('target_medical_record_id', $medicalRecord->id)
            ->where('requester_doctor_id', Auth::id())
            ->first();

        if ($existing && $existing->status === 'approved') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Access already granted'], 200);
            }

            return redirect()->route('medical-records.show', $medicalRecord->id);
        }

        if (! $existing) {
            AccessRequest::create([
                'requester_doctor_id' => Auth::id(),
                'target_medical_record_id' => $medicalRecord->id,
                'owner_doctor_id' => $medicalRecord->doctor_id,
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Access request sent successfully'], 201);
        }

        return back()->with('success', 'Access request sent.');
    }

    public function approveAccess(AccessRequest $accessRequest)
    {
        if ($accessRequest->owner_doctor_id !== Auth::id()) {
            abort(403);
        }

        $accessRequest->update(['status' => 'approved']);

        return back()->with('success', 'Access granted.');
    }
}

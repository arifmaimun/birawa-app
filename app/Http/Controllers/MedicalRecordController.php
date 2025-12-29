<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\MedicalUsageLog;
use App\Models\DoctorInventory;
use App\Models\InventoryTransaction;
use App\Models\AccessRequest;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Diagnosis;
use App\Models\VitalSign;
use App\Models\VitalSignSetting;
use App\Services\InventoryService;

class MedicalRecordController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function create(Visit $visit)
    {
        // Ensure the visit belongs to the doctor or doctor has access
        if ($visit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $inventories = $user->inventories()->orderBy('item_name')->get();
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

        return view('medical_records.create', compact('visit', 'inventories', 'diagnoses', 'medicalHistory', 'vitalSignSettings'));
    }

    public function store(Request $request, Visit $visit)
    {
        $request->validate([
            'subjective' => 'required|string',
            'objective' => 'required|string',
            'assessment' => 'nullable|string', // Made nullable as we encourage using diagnosis dropdown
            'diagnoses' => 'nullable|array',
            'diagnoses.*' => 'exists:diagnoses,id',
            'plan_treatment' => 'required|string',
            'plan_recipe' => 'nullable|string',
            'inventory_items' => 'nullable|array',
            'inventory_items.*.id' => 'exists:doctor_inventories,id',
            'inventory_items.*.qty' => 'numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $visit) {
            // Auto-generate assessment text from selected diagnoses if assessment is empty
            $assessmentText = $request->assessment;
            if (empty($assessmentText) && $request->has('diagnoses')) {
                $selectedDiagnoses = Diagnosis::whereIn('id', $request->diagnoses)->pluck('name')->toArray();
                $assessmentText = implode(', ', $selectedDiagnoses);
            }

            $record = MedicalRecord::create([
                'visit_id' => $visit->id,
                'doctor_id' => Auth::id(),
                'patient_id' => $visit->patient_id,
                'subjective' => $request->subjective,
                'objective' => $request->objective,
                'assessment' => $assessmentText ?? 'N/A', // Fallback
                'plan_treatment' => $request->plan_treatment,
                'plan_recipe' => $request->plan_recipe,
                'is_locked' => true,
            ]);

            // Save Vital Signs
            $vitalData = [
                'medical_record_id' => $record->id,
                'temperature' => $request->temperature,
                'weight' => $request->weight,
                'heart_rate' => $request->heart_rate,
            ];

            // Handle Custom Fields
            $customData = [];
            if ($request->has('custom_vital_signs')) {
                foreach ($request->custom_vital_signs as $key => $value) {
                    if ($value !== null && $value !== '') {
                        $customData[$key] = $value;
                    }
                }
            }
            $vitalData['custom_data'] = $customData;

            VitalSign::create($vitalData);

            // Attach Diagnoses
            if ($request->has('diagnoses')) {
                $record->diagnoses()->attach($request->diagnoses);
            }

            if ($request->has('inventory_items')) {
                foreach ($request->inventory_items as $item) {
                    if ($item['qty'] > 0) {
                        try {
                            // Use InventoryService to handle deduction and conversion
                            $this->inventoryService->deductStock($item['id'], $item['qty']);
                            
                            // Log usage in MedicalUsageLog (Medical Context)
                            // Note: InventoryTransaction is already handled inside InventoryService
                            MedicalUsageLog::create([
                                'medical_record_id' => $record->id,
                                'doctor_inventory_id' => $item['id'],
                                'quantity_used' => $item['qty'],
                            ]);

                        } catch (\Exception $e) {
                            // Throwing error to rollback transaction
                            throw new \Exception("Inventory Error: " . $e->getMessage());
                        }
                    }
                }
            }
            
            // Update visit status to completed
            $completedStatus = \App\Models\VisitStatus::where('slug', 'completed')->first();
            if ($completedStatus) {
                $visit->update(['visit_status_id' => $completedStatus->id]);
            }
        });

        return redirect()->route('visits.show', $visit->id)->with('success', 'Medical Record saved successfully.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

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
            return redirect()->route('medical-records.show', $medicalRecord->id);
        }

        if (!$existing) {
            AccessRequest::create([
                'requester_doctor_id' => Auth::id(),
                'target_medical_record_id' => $medicalRecord->id,
                'owner_doctor_id' => $medicalRecord->doctor_id,
                'status' => 'pending',
            ]);
        }

        return back()->with('success', 'Access request sent to the doctor.');
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

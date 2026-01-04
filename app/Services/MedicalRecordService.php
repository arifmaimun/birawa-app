<?php

namespace App\Services;

use App\DTOs\MedicalRecordDTO;
use App\Models\Diagnosis;
use App\Models\MedicalRecord;
use App\Models\MedicalUsageLog;
use App\Models\Visit;
use App\Models\VitalSign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicalRecordService
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function createMedicalRecord(Visit $visit, MedicalRecordDTO $data): MedicalRecord
    {
        return DB::transaction(function () use ($visit, $data) {
            // Auto-generate assessment text from selected diagnoses if assessment is empty
            $assessmentText = $data->assessment;
            if (empty($assessmentText) && ! empty($data->diagnoses)) {
                $selectedDiagnoses = Diagnosis::whereIn('id', $data->diagnoses)->pluck('name')->toArray();
                $assessmentText = implode(', ', $selectedDiagnoses);
            }

            $record = MedicalRecord::create([
                'visit_id' => $visit->id,
                'doctor_id' => Auth::id(),
                'patient_id' => $visit->patient_id,
                'subjective' => $data->subjective,
                'objective' => $data->objective,
                'assessment' => $assessmentText ?? 'N/A',
                'plan_diagnostic' => $data->plan_diagnostic,
                'plan_treatment' => $data->plan_treatment,
                'plan_recipe' => $data->plan_recipe,
                'is_locked' => true,
            ]);

            // Save Vital Signs
            $vitalData = [
                'medical_record_id' => $record->id,
                'temperature' => $data->vital_signs['temperature'] ?? null,
                'weight' => $data->vital_signs['weight'] ?? null,
                'heart_rate' => $data->vital_signs['heart_rate'] ?? null,
            ];

            // Handle Custom Fields
            $customData = [];
            if (! empty($data->custom_vital_signs)) {
                foreach ($data->custom_vital_signs as $key => $value) {
                    if ($value !== null && $value !== '') {
                        $customData[$key] = $value;
                    }
                }
            }
            $vitalData['custom_data'] = $customData;

            VitalSign::create($vitalData);

            // Attach Diagnoses
            if (! empty($data->diagnoses)) {
                $record->diagnoses()->attach($data->diagnoses);
            }

            if (! empty($data->inventory_items)) {
                foreach ($data->inventory_items as $item) {
                    if ($item['qty'] > 0) {
                        try {
                            // Use InventoryService to reserve stock first (committed on payment/invoice)
                            $this->inventoryService->reserveStock($item['id'], $item['qty']);

                            // Log usage in MedicalUsageLog (Medical Context)
                            MedicalUsageLog::create([
                                'medical_record_id' => $record->id,
                                'doctor_inventory_id' => $item['id'],
                                'quantity_used' => $item['qty'],
                            ]);

                        } catch (\Exception $e) {
                            // Throwing error to rollback transaction
                            throw new \Exception('Inventory Error: '.$e->getMessage());
                        }
                    }
                }
            }

            if (! empty($data->service_items)) {
                foreach ($data->service_items as $serviceData) {
                    if (($serviceData['qty'] ?? 0) > 0) {
                        $service = \App\Models\DoctorServiceCatalog::with('materials')->find($serviceData['id']);
                        if (! $service) {
                            continue;
                        }

                        MedicalUsageLog::create([
                            'medical_record_id' => $record->id,
                            'doctor_service_catalog_id' => $service->id,
                            'quantity_used' => $serviceData['qty'],
                        ]);

                        // Handle Bundled Materials
                        foreach ($service->materials as $material) {
                            $requiredQty = $material->pivot->quantity * $serviceData['qty'];

                            // Find doctor's inventory for this product
                            // Prioritize finding inventory with enough stock? Or just the default one?
                            // Assuming one inventory entry per product per doctor usually.
                            $inventory = \App\Models\DoctorInventory::where('user_id', Auth::id())
                                ->where('product_id', $material->id)
                                ->first();

                            if ($inventory) {
                                try {
                                    $this->inventoryService->reserveStock($inventory->id, $requiredQty, $material->pivot->unit ?? 'unit');

                                    // Log bundled usage
                                    MedicalUsageLog::create([
                                        'medical_record_id' => $record->id,
                                        'doctor_inventory_id' => $inventory->id,
                                        'quantity_used' => $requiredQty,
                                    ]);
                                } catch (\Exception $e) {
                                    throw new \Exception("Bundled Inventory Error ({$material->name}): ".$e->getMessage());
                                }
                            }
                        }
                    }
                }
            }

            // Update visit status to completed
            $completedStatus = \App\Models\VisitStatus::where('slug', 'completed')->first();
            if ($completedStatus) {
                $visit->update(['visit_status_id' => $completedStatus->id]);
            }

            return $record;
        });
    }
}

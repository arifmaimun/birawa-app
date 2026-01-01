<?php

namespace Tests\Feature;

use App\Models\DoctorInventory;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_create_medical_record_and_inventory_deducted()
    {
        // 0. Setup Statuses
        $scheduledStatus = \App\Models\VisitStatus::where('slug', 'scheduled')->first();
        $completedStatus = \App\Models\VisitStatus::where('slug', 'completed')->first();

        // 1. Setup Doctor
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        // 2. Setup Patient & Visit
        $patient = Patient::create([
            'name' => 'Fluffy',
            'species' => 'Cat',
            'breed' => 'Persian',
            'dob' => '2020-01-01',
            'gender' => 'Female',
        ]);
        
        $visit = Visit::create([
            'patient_id' => $patient->id,
            'user_id' => $doctor->id,
            'scheduled_at' => now(),
            'visit_status_id' => $scheduledStatus->id,
            'complaint' => 'Flu',
        ]);

        // 3. Setup Inventory
        $inventory = DoctorInventory::create([
            'user_id' => $doctor->id,
            'item_name' => 'Amoxicillin',
            'sku' => 'AMX-001',
            'stock_qty' => 100,
            'unit' => 'ml',
            'min_stock_alert' => 10,
        ]);

        // 4. Submit Medical Record
        $response = $this->post(route('medical-records.store', $visit), [
            'subjective' => 'Batuk pilek',
            'objective' => 'Suhu 39C',
            'assessment' => 'Flu berat',
            'plan_treatment' => 'Antibiotik',
            'inventory_items' => [
                [
                    'id' => $inventory->id,
                    'qty' => 5,
                ]
            ],
        ]);

        // 5. Assertions
        $response->assertRedirect();
        
        // Check Record Created
        $this->assertDatabaseHas('medical_records', [
            'visit_id' => $visit->id,
            'subjective' => 'Batuk pilek',
        ]);

        // Check Stock Deducted
        $this->assertDatabaseHas('doctor_inventories', [
            'id' => $inventory->id,
            'stock_qty' => 95, // 100 - 5
        ]);

        // Check Usage Log
        $this->assertDatabaseHas('medical_usage_logs', [
            'doctor_inventory_id' => $inventory->id,
            'quantity_used' => 5,
        ]);

        // Check Inventory Transaction (OUT)
        $this->assertDatabaseHas('inventory_transactions', [
            'doctor_inventory_id' => $inventory->id,
            'type' => 'OUT',
            'quantity_change' => -5,
        ]);

        // Check Visit Status Completed
        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'visit_status_id' => $completedStatus->id,
        ]);
    }

    public function test_access_control_flow()
    {
        // Setup Doctor A (Owner)
        $doctorA = User::factory()->create(['role' => 'veterinarian']);
        
        // Setup Doctor B (Requester)
        $doctorB = User::factory()->create(['role' => 'veterinarian']);

        // Setup Record for Doctor A
        $patient = Patient::create(['name' => 'Doggo', 'species' => 'Dog', 'gender' => 'Male']);
        $visit = Visit::create([
            'patient_id' => $patient->id,
            'user_id' => $doctorA->id,
            'scheduled_at' => now(),
            'status' => 'completed',
        ]);
        $record = MedicalRecord::create([
            'visit_id' => $visit->id,
            'doctor_id' => $doctorA->id,
            'patient_id' => $patient->id,
            'subjective' => 'Sick',
            'objective' => 'Fever',
            'assessment' => 'Flu',
            'plan_treatment' => 'Rest',
            'is_locked' => true,
        ]);

        // 1. Doctor B tries to view (Should see locked view)
        // Note: The controller returns a view 'medical_records.locked' instead of 403, 
        // so we check if we get successful response but with specific text or view
        $this->actingAs($doctorB);
        $response = $this->get(route('medical-records.show', $record));
        $response->assertStatus(200);
        $response->assertViewIs('medical_records.locked');

        // 2. Doctor B requests access
        $this->post(route('medical-records.request-access', $record));
        
        $this->assertDatabaseHas('access_requests', [
            'requester_doctor_id' => $doctorB->id,
            'target_medical_record_id' => $record->id,
            'status' => 'pending',
        ]);

        // 3. Doctor A approves access
        $this->actingAs($doctorA);
        $accessRequest = \App\Models\AccessRequest::first();
        $this->patch(route('access-requests.approve', $accessRequest));
        
        $this->assertDatabaseHas('access_requests', [
            'id' => $accessRequest->id,
            'status' => 'approved',
        ]);

        // 4. Doctor B tries to view again (Should see full view)
        $this->actingAs($doctorB);
        $response = $this->get(route('medical-records.show', $record));
        $response->assertStatus(200);
        $response->assertViewIs('medical_records.show');
        $response->assertSee('Sick'); // Subjective content
    }
}

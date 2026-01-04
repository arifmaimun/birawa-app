<?php

namespace Tests\Feature;

use App\Models\DoctorInventory;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\MedicalUsageLog;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_invoice_from_visit_with_transport_and_medication()
    {
        // 1. Setup Doctor & Data
        $doctor = User::factory()->create();
        $this->actingAs($doctor);

        // Create Client for the patient
        $clientUser = User::factory()->create(['role' => 'client']);
        $client = \App\Models\Client::create([
            'user_id' => $clientUser->id,
            'name' => $clientUser->name,
            'phone' => '08123456789',
        ]);

        $patient = Patient::factory()->create([
            'client_id' => $client->id,
        ]);

        // 2. Create Visit with Transport Fee
        $completedStatus = \App\Models\VisitStatus::where('slug', 'completed')->first();
        $visit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'transport_fee' => 50000,
            'distance_km' => 5.5,
            'visit_status_id' => $completedStatus ? $completedStatus->id : \App\Models\VisitStatus::factory()->create(['slug' => 'completed'])->id,
        ]);

        // 3. Create Inventory Item
        $inventory = DoctorInventory::create([
            'user_id' => $doctor->id,
            'item_name' => 'Amoxicillin',
            'stock_qty' => 100,
            'unit' => 'tablet',
            'base_unit' => 'tablet',
            'average_cost_price' => 1000,
            'selling_price' => 2000, // Margin 100%
        ]);

        // 4. Create Medical Record & Usage Log
        $record = MedicalRecord::create([
            'visit_id' => $visit->id,
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'subjective' => 'Test',
            'objective' => 'Test',
            'assessment' => 'Test',
            'plan_treatment' => 'Test',
            'plan_recipe' => 'Test',
        ]);

        MedicalUsageLog::create([
            'medical_record_id' => $record->id,
            'doctor_inventory_id' => $inventory->id,
            'quantity_used' => 10,
        ]);

        // 5. Generate Invoice
        $response = $this->post(route('invoices.createFromVisit', $visit));

        $response->assertRedirect();

        // 6. Verify Invoice
        $this->assertDatabaseHas('invoices', [
            'visit_id' => $visit->id,
            'total_amount' => 50000 + (10 * 2000), // 50000 + 20000 = 70000
            'payment_status' => 'unpaid',
        ]);

        $invoice = Invoice::where('visit_id', $visit->id)->first();

        // Verify Invoice Items
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'description' => 'Transport Fee (5.5 km)',
            'unit_price' => 50000,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'description' => 'Amoxicillin',
            'quantity' => 10,
            'unit_price' => 2000,
        ]);
    }
}

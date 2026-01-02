<?php

namespace Tests\Feature;

use App\Filament\Resources\VisitResource\Pages\DoctorConsultation;
use App\Models\DoctorInventory;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DoctorConsultationTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_access_consultation_page()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $patient = Patient::factory()->create(); // Patient without client is possible in factory, handled by view
        $visit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
        ]);

        $this->get(DoctorConsultation::getUrl(['record' => $visit]))
            ->assertSuccessful();
    }

    public function test_consultation_saves_medical_record_and_creates_invoice()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $patient = Patient::factory()->create();
        $visit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'transport_fee' => 50000,
            'distance_km' => 10,
        ]);

        // Create Inventory Item
        $inventory = DoctorInventory::create([
            'user_id' => $doctor->id,
            'item_name' => 'Vaccine X',
            'stock_qty' => 10,
            'unit' => 'dose',
            'base_unit' => 'dose',
            'average_cost_price' => 50000,
            'selling_price' => 100000,
        ]);

        Livewire::test(DoctorConsultation::class, ['record' => $visit])
            ->fillForm([
                'vitalSign' => [
                    'weight' => 5.5,
                    'temperature' => 38.5,
                    'heart_rate' => 120,
                ],
                'subjective' => 'Patient looks sad',
                'objective' => 'Fever',
                'assessment' => 'Flu',
                'plan_treatment' => 'Rest',
                'plan_recipe' => 'Vaccine',
                'usageLogs' => [
                    [
                        'doctor_inventory_id' => $inventory->id,
                        'quantity_used' => 1,
                        'unit_price' => 100000,
                    ]
                ],
            ])
            ->call('save')
            ->assertHasNoErrors();

        // Assert Medical Record Created
        $this->assertDatabaseHas('medical_records', [
            'visit_id' => $visit->id,
            'patient_id' => $patient->id,
            // 'subjective' is encrypted, so we can't check raw value easily unless we decrypt, 
            // but existence is enough for now.
        ]);

        $record = MedicalRecord::where('visit_id', $visit->id)->first();
        
        // Assert Vital Signs
        $this->assertDatabaseHas('vital_signs', [
            'medical_record_id' => $record->id,
            'weight' => 5.5,
        ]);

        // Assert Usage Log
        $this->assertDatabaseHas('medical_usage_logs', [
            'medical_record_id' => $record->id,
            'doctor_inventory_id' => $inventory->id,
            'quantity_used' => 1,
        ]);

        // Assert Inventory Deducted
        $inventory->refresh();
        $this->assertEquals(9, $inventory->stock_qty);

        // Assert Invoice Created
        $this->assertDatabaseHas('invoices', [
            'visit_id' => $visit->id,
            'payment_status' => 'unpaid',
        ]);

        $invoice = Invoice::where('visit_id', $visit->id)->first();
        
        // Check Invoice Items
        // 1. Transport Fee
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'unit_price' => 50000,
            'description' => 'Transport Fee (10 km)',
        ]);

        // 2. Vaccine
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'unit_price' => 100000,
            'description' => 'Vaccine X',
        ]);

        // Check Total (100k + 50k = 150k)
        $this->assertEquals(150000, $invoice->total_amount);
    }
}

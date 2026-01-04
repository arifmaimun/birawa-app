<?php

namespace Tests\Feature;

use App\DTOs\MedicalRecordDTO;
use App\Models\DoctorInventory;
use App\Models\DoctorServiceCatalog;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Product;
use App\Models\User;
use App\Models\Visit;
use App\Services\InventoryService;
use App\Services\MedicalRecordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BirawaFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_bundling_reserves_stock()
    {
        $user = User::factory()->create();
        Auth::login($user);

        // 1. Create Product (Material)
        $material = Product::factory()->create(['stock' => 100]); // Global stock, but we use DoctorInventory

        // 2. Create DoctorInventory
        $inventory = DoctorInventory::create([
            'user_id' => $user->id,
            'product_id' => $material->id,
            'item_name' => $material->name,
            'stock_qty' => 50,
            'reserved_qty' => 0,
            'unit' => 'unit',
        ]);

        // 3. Create Service
        $service = DoctorServiceCatalog::create([
            'user_id' => $user->id,
            'service_name' => 'Vaccination',
            'price' => 100000,
        ]);

        // 4. Attach Material to Service (Bundling)
        // 2 units of material per service
        $service->materials()->attach($material->id, ['quantity' => 2, 'unit' => 'unit']);

        // 5. Create Visit
        $patient = Patient::factory()->create();
        $visit = Visit::factory()->create(['patient_id' => $patient->id]);

        // 6. Create MedicalRecord using Service
        $dto = new MedicalRecordDTO(
            subjective: 'S', objective: 'O', assessment: 'A', plan_diagnostic: 'D', plan_treatment: 'P', plan_recipe: 'R',
            diagnoses: [],
            inventory_items: [],
            service_items: [['id' => $service->id, 'qty' => 1]], // 1 Service used
            vital_signs: [], custom_vital_signs: []
        );

        $medicalService = app(MedicalRecordService::class);
        $medicalService->createMedicalRecord($visit, $dto);

        // 7. Verify Stock Reserved
        // Required: 1 service * 2 units = 2 units reserved.
        $inventory->refresh();
        $this->assertEquals(2, $inventory->reserved_qty);
        $this->assertEquals(50, $inventory->stock_qty); // Stock not deducted yet, only reserved
    }

    public function test_stock_reservation_flow_commit_on_payment()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $product = Product::factory()->create();
        $inventory = DoctorInventory::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'stock_qty' => 10,
            'reserved_qty' => 5, // Already reserved via MedicalRecord (simulated)
            'unit' => 'unit',
        ]);

        // Create Invoice linked to this inventory item
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'patient_id' => Patient::factory()->create()->id,
            'total_amount' => 50000,
            'payment_status' => 'unpaid',
            'stock_committed' => false,
            'status' => 'draft',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'doctor_inventory_id' => $inventory->id,
            'description' => $product->name,
            'quantity' => 5,
            'unit_price' => 10000,
        ]);

        // Simulate Payment
        $response = $this->post(route('invoices.payments.store', $invoice), [
            'amount' => 50000,
            'method' => 'cash',
            'paid_at' => now()->toDateTimeString(),
        ]);

        $response->assertRedirect(); // Should redirect back

        $inventory->refresh();
        $invoice->refresh();

        $this->assertEquals(0, $inventory->reserved_qty); // 5 - 5 = 0
        $this->assertEquals(5, $inventory->stock_qty);    // 10 - 5 = 5
        $this->assertTrue((bool) $invoice->stock_committed);
        $this->assertEquals('paid', $invoice->payment_status);
    }

    public function test_stock_reservation_flow_release_on_cancel()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $product = Product::factory()->create();
        $inventory = DoctorInventory::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'stock_qty' => 10,
            'reserved_qty' => 5,
            'unit' => 'unit',
        ]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'patient_id' => Patient::factory()->create()->id,
            'total_amount' => 50000,
            'payment_status' => 'unpaid',
            'stock_committed' => false,
            'status' => 'draft',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'doctor_inventory_id' => $inventory->id,
            'description' => $product->name,
            'quantity' => 5,
            'unit_price' => 10000,
        ]);

        // Cancel Invoice
        $controller = new \App\Http\Controllers\InvoiceController;
        $inventoryService = app(InventoryService::class);
        $controller->cancel($invoice, $inventoryService);

        $inventory->refresh();
        $invoice->refresh();

        $this->assertEquals(0, $inventory->reserved_qty); // Released back to available (reserved decr)
        $this->assertEquals(10, $inventory->stock_qty);   // Stock unchanged (it was never deducted, just un-reserved)
        $this->assertEquals('cancelled', $invoice->status);
    }


}

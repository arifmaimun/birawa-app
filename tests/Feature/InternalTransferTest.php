<?php

namespace Tests\Feature;

use App\Models\DoctorInventory;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_transfer_stock_between_locations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create locations
        $warehouse = StorageLocation::create(['user_id' => $user->id, 'name' => 'Gudang', 'type' => 'warehouse']);
        $bag = StorageLocation::create(['user_id' => $user->id, 'name' => 'Tas', 'type' => 'bag']);

        // Create item in Warehouse
        $item = DoctorInventory::create([
            'user_id' => $user->id,
            'storage_location_id' => $warehouse->id,
            'item_name' => 'Panadol',
            'sku' => 'PANA123',
            'stock_qty' => 100,
            'unit' => 'pcs',
            'base_unit' => 'pcs',
            'purchase_unit' => 'box',
            'conversion_ratio' => 10,
            'selling_price' => 5000,
            'alert_threshold' => 10,
        ]);

        // Transfer 20 to Bag
        $response = $this->post(route('internal-transfers.store'), [
            'inventory_id' => $item->id,
            'target_location_id' => $bag->id,
            'quantity' => 20,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check source stock
        $this->assertDatabaseHas('doctor_inventories', [
            'id' => $item->id,
            'stock_qty' => 80,
        ]);

        // Check target stock
        $this->assertDatabaseHas('doctor_inventories', [
            'user_id' => $user->id,
            'storage_location_id' => $bag->id,
            'sku' => 'PANA123',
            'stock_qty' => 20,
        ]);

        // Check transactions
        $this->assertDatabaseCount('inventory_transactions', 2);
    }
}

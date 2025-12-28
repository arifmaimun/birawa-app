<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DoctorInventory;
use App\Models\Expense;
use App\Models\InventoryTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_create_inventory_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('inventory.store'), [
            'item_name' => 'Antibiotic X',
            'base_unit' => 'tablet',
            'purchase_unit' => 'box',
            'conversion_ratio' => 10, // 1 box = 10 tablets
            'min_stock_alert' => 20,
        ]);

        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('doctor_inventories', [
            'item_name' => 'Antibiotic X',
            'base_unit' => 'tablet',
            'purchase_unit' => 'box',
            'conversion_ratio' => 10,
            'user_id' => $user->id,
            'stock_qty' => 0,
        ]);
    }

    public function test_doctor_can_restock_item_with_multi_uom()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = DoctorInventory::create([
            'user_id' => $user->id,
            'item_name' => 'Antibiotic X',
            'base_unit' => 'tablet',
            'purchase_unit' => 'box',
            'conversion_ratio' => 10,
            'unit' => 'tablet', // Legacy field required
            'stock_qty' => 5, // Initial stock
            'average_cost_price' => 1000, // Initial avg cost per tablet
            'alert_threshold' => 10,
        ]);

        // Restock: Buy 2 boxes at 12000 per box
        // 2 boxes = 20 tablets. Cost = 24000.
        // New Total Stock = 5 + 20 = 25 tablets.
        // New Total Value = (5 * 1000) + 24000 = 5000 + 24000 = 29000.
        // New Avg Cost = 29000 / 25 = 1160.

        $response = $this->post(route('inventory.restock.store', $item), [
            'quantity_purchase_unit' => 2,
            'cost_per_purchase_unit' => 12000,
        ]);

        $response->assertRedirect(route('inventory.index'));

        // Check Inventory Update
        $this->assertDatabaseHas('doctor_inventories', [
            'id' => $item->id,
            'stock_qty' => 25,
            'average_cost_price' => 1160,
        ]);

        // Check Expense Creation
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'category' => 'Medicine Restock',
            'amount' => 24000,
            'notes' => 'Restock Antibiotic X: 2 box',
        ]);

        // Check Transaction Log
        $this->assertDatabaseHas('inventory_transactions', [
            'doctor_inventory_id' => $item->id,
            'type' => 'IN',
            'quantity_change' => 20, // 2 boxes * 10
        ]);
    }
}

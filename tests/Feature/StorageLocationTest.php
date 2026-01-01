<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\StorageLocation;
use App\Models\DoctorInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StorageLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_storage_location()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('storage-locations.store'), [
            'name' => 'Tas Visit A',
            'type' => 'bag',
            'description' => 'Tas untuk kunjungan rutin',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('storage_locations', [
            'user_id' => $user->id,
            'name' => 'Tas Visit A',
            'type' => 'bag',
        ]);
    }

    public function test_user_can_update_storage_location()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $location = StorageLocation::create([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'type' => 'warehouse',
        ]);

        $response = $this->put(route('storage-locations.update', $location), [
            'name' => 'New Name',
            'type' => 'bag',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('storage_locations', [
            'id' => $location->id,
            'name' => 'New Name',
            'type' => 'bag',
            'description' => 'Updated description',
        ]);
    }

    public function test_user_can_add_inventory_to_specific_location()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create location
        $location = StorageLocation::create([
            'user_id' => $user->id,
            'name' => 'Tas Visit A',
            'type' => 'bag',
        ]);

        // Add item
        $response = $this->post(route('inventory.store'), [
            'storage_location_id' => $location->id,
            'item_name' => 'Paracetamol',
            'sku' => 'PARA500',
            // 'stock_qty' => 10, // Controller ignores this on create
            'base_unit' => 'tablet',
            'purchase_unit' => 'box',
            'conversion_ratio' => 10,
            'selling_price' => 1000,
            'min_stock_alert' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('doctor_inventories', [
            'user_id' => $user->id,
            'storage_location_id' => $location->id,
            'item_name' => 'Paracetamol',
            'sku' => 'PARA500',
        ]);
    }

    public function test_sku_uniqueness_is_per_location()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $loc1 = StorageLocation::create(['user_id' => $user->id, 'name' => 'Loc 1', 'type' => 'warehouse']);
        $loc2 = StorageLocation::create(['user_id' => $user->id, 'name' => 'Loc 2', 'type' => 'bag']);

        // Add to Loc 1
        $this->post(route('inventory.store'), [
            'storage_location_id' => $loc1->id,
            'item_name' => 'Item 1',
            'sku' => 'SKU123',
            'base_unit' => 'pcs',
            'purchase_unit' => 'pcs',
            'conversion_ratio' => 1,
            'selling_price' => 1000,
            'min_stock_alert' => 5,
        ]);

        // Add same SKU to Loc 2 (Should succeed)
        $response = $this->post(route('inventory.store'), [
            'storage_location_id' => $loc2->id,
            'item_name' => 'Item 1',
            'sku' => 'SKU123',
            'base_unit' => 'pcs',
            'purchase_unit' => 'pcs',
            'conversion_ratio' => 1,
            'selling_price' => 1000,
            'min_stock_alert' => 5,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(2, DoctorInventory::where('sku', 'SKU123')->count());

        // Add same SKU to Loc 1 again (Should fail)
        $response = $this->post(route('inventory.store'), [
            'storage_location_id' => $loc1->id,
            'item_name' => 'Item 1',
            'sku' => 'SKU123',
            'base_unit' => 'pcs',
            'purchase_unit' => 'pcs',
            'conversion_ratio' => 1,
            'selling_price' => 1000,
            'min_stock_alert' => 5,
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_user_cannot_delete_location_with_stock()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $location = StorageLocation::create(['user_id' => $user->id, 'name' => 'Loc 1', 'type' => 'warehouse']);
        
        DoctorInventory::create([
            'user_id' => $user->id,
            'storage_location_id' => $location->id,
            'item_name' => 'Item 1',
            'sku' => 'SKU123',
            'stock_qty' => 10, // Has stock
            'unit' => 'pcs',
            'base_unit' => 'pcs',
        ]);

        $response = $this->delete(route('storage-locations.destroy', $location));
        $response->assertRedirect();
        $response->assertSessionHas('error'); // Should fail
        $this->assertDatabaseHas('storage_locations', ['id' => $location->id]);
    }

    public function test_user_can_delete_location_without_stock()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $location = StorageLocation::create(['user_id' => $user->id, 'name' => 'Loc 1', 'type' => 'warehouse']);
        
        // No items or items with 0 stock
        DoctorInventory::create([
            'user_id' => $user->id,
            'storage_location_id' => $location->id,
            'item_name' => 'Item 1',
            'sku' => 'SKU123',
            'stock_qty' => 0,
            'unit' => 'pcs',
            'base_unit' => 'pcs',
        ]);

        $response = $this->delete(route('storage-locations.destroy', $location));
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('storage_locations', ['id' => $location->id]);
    }
}

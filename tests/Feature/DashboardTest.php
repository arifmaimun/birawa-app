<?php

namespace Tests\Feature;

use App\Models\DoctorInventory;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_stats_and_alerts()
    {
        $user = User::factory()->create();

        // Create Patient (owners are now via pivot table)
        $patient = Patient::create([
            'name' => 'Fluffy',
            'species' => 'Cat',
            'gender' => 'female',
        ]);
        
        // Attach user as owner
        $patient->owners()->attach($user->id, ['is_primary' => true]);
        
        $inventory = DoctorInventory::create([
            'user_id' => $user->id,
            'item_name' => 'Low Stock Med',
            'stock_qty' => 2,
            'unit' => 'tablet', // legacy
            'base_unit' => 'tablet',
            'purchase_unit' => 'box',
            'conversion_ratio' => 10,
            'alert_threshold' => 5, // Should trigger alert
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Low Stock Alerts');
        $response->assertSee('Low Stock Med');
        $response->assertSee('My Inventory Items');
    }
}

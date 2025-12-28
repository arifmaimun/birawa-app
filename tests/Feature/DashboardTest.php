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

        // Create Client profile for user
        $client = \App\Models\Client::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone ?? '08123456789',
            'address' => 'Test Address',
        ]);

        // Create Patient linked to client
        $patient = Patient::create([
            'client_id' => $client->id,
            'name' => 'Fluffy',
            'species' => 'Cat',
            'gender' => 'female',
        ]);
        
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
        $response->assertSee('Low Stock Alert');
        $response->assertSee('Low Stock Med');
        // $response->assertSee('My Inventory Items');
    }
}

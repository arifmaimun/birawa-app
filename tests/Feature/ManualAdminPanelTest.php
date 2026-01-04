<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManualAdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        if (! Role::where('name', 'superadmin')->exists()) {
            Role::create(['name' => 'superadmin']);
        }
    }

    private function createAdminUser()
    {
        $user = User::factory()->create(['role' => 'superadmin']);
        $user->assignRole('superadmin');

        return $user;
    }

    public function test_manual_dashboard_is_accessible()
    {
        $user = $this->createAdminUser();
        $prefix = config('migration.route_prefix', 'app');

        $response = $this->actingAs($user)->get("/{$prefix}");

        $response->assertStatus(200);
    }

    public function test_manual_patients_index_is_accessible()
    {
        $user = $this->createAdminUser();
        $prefix = config('migration.route_prefix', 'app');

        $response = $this->actingAs($user)->get("/{$prefix}/patients");

        $response->assertStatus(200);
    }

    public function test_manual_products_index_is_accessible()
    {
        $user = $this->createAdminUser();
        $prefix = config('migration.route_prefix', 'app');

        $response = $this->actingAs($user)->get("/{$prefix}/products");

        $response->assertStatus(200);
    }

    public function test_manual_users_index_is_accessible()
    {
        $user = $this->createAdminUser();
        $prefix = config('migration.route_prefix', 'app');

        $response = $this->actingAs($user)->get("/{$prefix}/users");

        $response->assertStatus(200);
    }

    public function test_manual_inventory_index_is_accessible()
    {
        $user = $this->createAdminUser();
        $prefix = config('migration.route_prefix', 'app');

        $response = $this->actingAs($user)->get("/{$prefix}/inventory");

        $response->assertStatus(200);
    }
}

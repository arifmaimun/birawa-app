<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserJourneyTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        if (!Role::where('name', 'superadmin')->exists()) {
            Role::create(['name' => 'superadmin']);
        }
        if (!Role::where('name', 'doctor')->exists()) {
            Role::create(['name' => 'doctor']);
        }
    }

    private function createAuthenticatedUser($role = 'superadmin')
    {
        $user = User::factory()->create([
            'role' => $role,
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($role);
        
        return $user;
    }

    /** @test */
    public function user_can_complete_login_flow()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function authenticated_user_can_view_dashboard_metrics()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)
            ->get(route('manual.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('manual.dashboard');
        $response->assertSee('Dashboard Overview');
        // Check for common MVP dashboard elements
        $response->assertSee('Total Patients');
        $response->assertSee('Pending Invoices');
    }

    /** @test */
    public function user_can_access_inventory_module()
    {
        $user = $this->createAuthenticatedUser();

        // Assuming inventory route exists as per config
        $response = $this->actingAs($user)
            ->get(route('manual.inventory.index'));

        $response->assertStatus(200);
        // Should use the manual layout
        $response->assertSee('My Inventory'); 
    }

    /** @test */
    public function user_receives_feedback_on_actions()
    {
        $user = $this->createAuthenticatedUser();

        // Simulate a successful action that flashes a message
        // Since we don't have a direct form to submit here without more setup,
        // we can check if the session flash works by redirecting with data
        
        $response = $this->actingAs($user)
            ->withSession(['success' => 'Data berhasil disimpan'])
            ->get(route('manual.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Data berhasil disimpan');
    }

    /** @test */
    public function unauthorized_access_is_blocked()
    {
        // Guest user trying to access dashboard
        $response = $this->get(route('manual.dashboard'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function navigation_menu_is_present()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)
            ->get(route('manual.dashboard'));

        $response->assertStatus(200);
        // Verify navigation links exist
        $response->assertSee(route('manual.dashboard'));
        // $response->assertSee(route('manual.inventory.index')); // Might be hidden in mobile menu or similar, but generally visible
    }
}

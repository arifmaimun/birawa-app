<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebInterfaceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a regular user (doctor/staff)
        $this->user = User::factory()->create([
            'role' => 'veterinarian',
        ]);
    }

    public function test_dashboard_is_accessible()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_patients_index_is_accessible_and_uses_blade()
    {
        $response = $this->actingAs($this->user)->get(route('patients.index'));

        $response->assertStatus(200);
        $response->assertViewIs('patients.index');
        $response->assertSee('Patient Management');
    }

    public function test_clients_index_is_accessible_and_uses_blade()
    {
        $response = $this->actingAs($this->user)->get(route('clients.index'));

        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertSee('Client Management');
    }

    public function test_visits_index_is_accessible_and_uses_blade()
    {
        $response = $this->actingAs($this->user)->get(route('visits.index'));

        $response->assertStatus(200);
        $response->assertViewIs('visits.calendar');
        $response->assertSee('Filter Jadwal');
    }

    public function test_patient_show_page()
    {
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);

        $response = $this->actingAs($this->user)->get(route('patients.show', $patient));

        $response->assertStatus(200);
        $response->assertViewIs('patients.show');
        $response->assertSee($patient->name);
    }

    public function test_client_show_page()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->get(route('clients.show', $client));

        $response->assertStatus(200);
        $response->assertViewIs('clients.show');
        $response->assertSee($client->name);
    }

    public function test_visit_show_page()
    {
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        $visit = Visit::factory()->create([
            'user_id' => $this->user->id,
            'patient_id' => $patient->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('visits.show', $visit));

        $response->assertStatus(200);
        $response->assertViewIs('visits.show');
    }

    public function test_flash_messages_are_displayed()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['success' => 'Operation successful'])
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Operation successful');

        $response = $this->actingAs($this->user)
            ->withSession(['error' => 'Operation failed'])
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Operation failed');
    }
}

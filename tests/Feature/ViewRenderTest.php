<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_screen_can_be_rendered()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $patient->owners()->attach($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee($patient->name);
    }

    public function test_patients_index_can_be_rendered()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $patient->owners()->attach($user);

        $response = $this->actingAs($user)->get(route('patients.index'));

        $response->assertStatus(200);
        $response->assertSee($patient->name);
    }

    public function test_patient_show_can_be_rendered()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $patient->owners()->attach($user);

        $response = $this->actingAs($user)->get(route('patients.show', $patient));

        $response->assertStatus(200);
        $response->assertSee($patient->name);
    }

    public function test_visits_index_can_be_rendered()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $patient->owners()->attach($user);
        $visit = Visit::factory()->create([
            'patient_id' => $patient->id,
            'user_id' => $user->id, // Assuming doctor/user
        ]);

        $response = $this->actingAs($user)->get(route('visits.index'));

        $response->assertStatus(200);
    }

    public function test_visit_show_can_be_rendered()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $patient->owners()->attach($user);
        $visit = Visit::factory()->create([
            'patient_id' => $patient->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('visits.show', $visit));

        $response->assertStatus(200);
    }

    public function test_visit_edit_can_be_rendered()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $patient->owners()->attach($user);
        $visit = Visit::factory()->create([
            'patient_id' => $patient->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('visits.edit', $visit));

        $response->assertStatus(200);
    }

    public function test_visit_create_can_be_rendered()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $patient->owners()->attach($user);

        $response = $this->actingAs($user)->get(route('visits.create'));

        $response->assertStatus(200);
    }

    public function test_patient_edit_can_be_rendered()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $patient->owners()->attach($user);

        $response = $this->actingAs($user)->get(route('patients.edit', $patient));

        $response->assertStatus(200);
    }
}

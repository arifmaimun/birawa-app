<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_api_returns_correct_structure()
    {
        $user = User::factory()->create(['role' => 'veterinarian']);
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        $status = VisitStatus::firstOrCreate(
            ['slug' => 'scheduled'],
            ['name' => 'Scheduled', 'color' => '#blue']
        );

        $visit = Visit::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'scheduled_at' => now(),
            'visit_status_id' => $status->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/visits/{$visit->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'patient' => ['client'],
                'user',
                'medical_records',
                'status',
            ]);
    }

    public function test_visit_api_handles_null_status()
    {
        $user = User::factory()->create(['role' => 'veterinarian']);
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);

        // Visit without status
        $visit = Visit::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'scheduled_at' => now(),
            'visit_status_id' => null,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/visits/{$visit->id}");

        $response->assertStatus(200);
        $this->assertEquals('scheduled', $response->json('status'));
    }

    public function test_create_medical_record_via_api()
    {
        $user = User::factory()->create(['role' => 'veterinarian']);
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        $visit = Visit::create([
            'user_id' => $user->id,
            'patient_id' => $patient->id,
            'scheduled_at' => now(),
        ]);

        $data = [
            'subjective' => 'Subjective notes',
            'objective' => 'Objective findings',
            'assessment' => 'Assessment',
            'plan_diagnostic' => 'Diagnostic plan',
            'plan_treatment' => 'Treatment plan',
        ];

        $response = $this->actingAs($user)
            ->postJson("/visits/{$visit->id}/medical-record", $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'subjective' => 'Subjective notes',
                'plan_diagnostic' => 'Diagnostic plan',
            ]);

        $this->assertDatabaseHas('medical_records', [
            'visit_id' => $visit->id,
            'plan_diagnostic' => 'Diagnostic plan', // Not encrypted
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup user for authentication
        $this->user = User::factory()->create();
    }

    public function test_api_patients_list_requires_authentication()
    {
        $response = $this->getJson('/api/patients');
        $response->assertStatus(401);
    }

    public function test_api_patients_list_returns_json_structure()
    {
        Sanctum::actingAs($this->user);

        $count = 5;
        Patient::factory()->count($count)->create();

        $response = $this->getJson('/api/patients');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'species', 'client',
                    ],
                ],
                'links',
                'current_page',
            ]);

        $this->assertCount($count, $response->json('data'));
    }

    public function test_api_patient_detail_returns_correct_relations()
    {
        Sanctum::actingAs($this->user);

        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);

        // Create visit and medical records manually
        $visit = Visit::factory()->create(['patient_id' => $patient->id]);
        $record = new MedicalRecord;
        $record->patient_id = $patient->id;
        $record->visit_id = $visit->id;
        $record->doctor_id = $this->user->id;
        $record->created_at = now();
        // Skip encrypted fields for simple test or use factory if available
        $record->save();

        $response = $this->getJson("/api/patients/{$patient->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'client' => ['id', 'name'],
                'medical_records' => [
                    '*' => ['id', 'created_at'],
                ],
            ]);

        $this->assertEquals($patient->id, $response->json('id'));
        $this->assertEquals($client->id, $response->json('client.id'));
    }

    public function test_api_clients_list_returns_json()
    {
        Sanctum::actingAs($this->user);

        Client::factory()->count(3)->create();

        $response = $this->getJson('/api/clients');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'phone'],
                ],
            ]);
    }

    public function test_api_client_detail_returns_patients()
    {
        Sanctum::actingAs($this->user);

        $client = Client::factory()->create();
        $patient = Patient::factory()->create();
        $client->patients()->attach($patient->id);

        $response = $this->getJson("/api/clients/{$client->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'patients' => [
                    '*' => ['id', 'name'],
                ],
            ]);

        $this->assertCount(1, $response->json('patients'));
    }
}

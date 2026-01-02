<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_create_page_loads_all_patients_including_new_ones()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        // Create a patient with NO history
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id, 'name' => 'New Patient']);

        $response = $this->get(route('visits.create'));

        $response->assertStatus(200);
        $response->assertViewHas('patients', function($patients) use ($patient) {
            // Check if collection contains the patient
            return $patients->contains('id', $patient->id);
        });
    }

    public function test_create_page_search_parameter_works()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $client1 = Client::factory()->create(['name' => 'Alpha']);
        $patient1 = Patient::factory()->create(['client_id' => $client1->id, 'name' => 'Dog A']);
        
        $client2 = Client::factory()->create(['name' => 'Beta']);
        $patient2 = Patient::factory()->create(['client_id' => $client2->id, 'name' => 'Cat B']);

        // Search for 'Alpha'
        $response = $this->get(route('visits.create', ['search' => 'Alpha']));
        $response->assertViewHas('patients', function($patients) use ($patient1, $patient2) {
            return $patients->contains('id', $patient1->id) && !$patients->contains('id', $patient2->id);
        });
    }

    public function test_index_filters_by_status_correctly()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        // Statuses are seeded by migration, so we fetch them or create if missing
        $statusScheduled = VisitStatus::where('slug', 'scheduled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'scheduled', 'name' => 'Scheduled']);
            
        $statusCompleted = VisitStatus::where('slug', 'completed')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'completed', 'name' => 'Completed']);
            
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);

        $visit1 = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $statusScheduled->id,
            'scheduled_at' => now(),
        ]);
        
        $visit2 = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $statusCompleted->id,
            'scheduled_at' => now()->subDay(),
        ]);

        // Filter by scheduled
        $response = $this->get(route('visits.index', ['status' => 'scheduled']));
        $response->assertStatus(200);
        $response->assertViewHas('visits', function($visits) use ($visit1, $visit2) {
            return $visits->contains($visit1) && !$visits->contains($visit2);
        });

        // Filter by completed
        $response = $this->get(route('visits.index', ['status' => 'completed']));
        $response->assertStatus(200);
        $response->assertViewHas('visits', function($visits) use ($visit1, $visit2) {
            return !$visits->contains($visit1) && $visits->contains($visit2);
        });
    }

    public function test_index_search_logic_is_grouped_correctly()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $otherDoctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $status = VisitStatus::where('slug', 'scheduled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'scheduled']);

        // My patient matching search
        $myClient = Client::factory()->create(['name' => 'John Doe']);
        $myPatient = Patient::factory()->create(['client_id' => $myClient->id, 'name' => 'Fluffy']);
        $myVisit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $myPatient->id,
            'visit_status_id' => $status->id
        ]);

        // Other doctor's patient matching search (Should NOT see this due to user_id scope + manual check)
        $otherClient = Client::factory()->create(['name' => 'John Smith']); // Matches "John"
        $otherPatient = Patient::factory()->create(['client_id' => $otherClient->id, 'name' => 'Rex']);
        $otherVisit = Visit::factory()->create([
            'user_id' => $otherDoctor->id,
            'patient_id' => $otherPatient->id,
            'visit_status_id' => $status->id
        ]);

        // Search for "John"
        $response = $this->get(route('visits.index', ['search' => 'John']));
        
        $response->assertStatus(200);
        $response->assertViewHas('visits', function($visits) use ($myVisit, $otherVisit) {
            return $visits->contains($myVisit) && !$visits->contains($otherVisit);
        });
    }
    
    public function test_index_rejects_invalid_status()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $response = $this->get(route('visits.index', ['status' => 'invalid-status-slug']));
        $response->assertSessionHasErrors('status');
    }
}

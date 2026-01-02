<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_visits_by_single_status()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        
        $scheduledStatus = VisitStatus::where('slug', 'scheduled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'scheduled', 'name' => 'Scheduled']);
        $completedStatus = VisitStatus::where('slug', 'completed')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'completed', 'name' => 'Completed']);

        $visit1 = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $scheduledStatus->id
        ]);

        $visit2 = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $completedStatus->id
        ]);

        $response = $this->get(route('visits.index', ['status' => 'scheduled']));

        $response->assertOk();
        $response->assertViewHas('visits', function ($visits) use ($visit1, $visit2) {
            return $visits->contains($visit1) && !$visits->contains($visit2);
        });
    }

    public function test_can_filter_visits_by_multiple_statuses()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        
        $scheduledStatus = VisitStatus::where('slug', 'scheduled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'scheduled', 'name' => 'Scheduled']);
        $completedStatus = VisitStatus::where('slug', 'completed')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'completed', 'name' => 'Completed']);
        $canceledStatus = VisitStatus::where('slug', 'canceled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'canceled', 'name' => 'Canceled']);

        $visit1 = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $scheduledStatus->id
        ]);

        $visit2 = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $completedStatus->id
        ]);

        $visit3 = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $canceledStatus->id
        ]);

        // Requesting scheduled AND completed
        $response = $this->get(route('visits.index', ['status' => ['scheduled', 'completed']]));

        $response->assertOk(); 
        $response->assertViewHas('visits', function ($visits) use ($visit1, $visit2, $visit3) {
            return $visits->contains($visit1) 
                && $visits->contains($visit2) 
                && !$visits->contains($visit3);
        });
    }

    public function test_handles_invalid_status_gracefully()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $response = $this->get(route('visits.index', ['status' => 'invalid-slug']));
        
        $response->assertSessionHasErrors('status');
    }
    
    public function test_handles_empty_filter_parameter()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);
        
        $status = VisitStatus::where('slug', 'scheduled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'scheduled']);
        $visit = Visit::factory()->create(['user_id' => $doctor->id, 'visit_status_id' => $status->id]);

        $response = $this->get(route('visits.index', ['status' => '']));
        
        $response->assertOk();
        $response->assertViewHas('visits', function ($visits) use ($visit) {
            return $visits->contains($visit);
        });
    }

    public function test_can_filter_visits_by_search_and_status_combined()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $client1 = Client::factory()->create(['name' => 'John Doe']);
        $patient1 = Patient::factory()->create(['client_id' => $client1->id, 'name' => 'Fluffy']);
        
        $client2 = Client::factory()->create(['name' => 'Jane Doe']);
        $patient2 = Patient::factory()->create(['client_id' => $client2->id, 'name' => 'Rex']);

        $scheduledStatus = VisitStatus::where('slug', 'scheduled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'scheduled']);
        $completedStatus = VisitStatus::where('slug', 'completed')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'completed']);

        // Target: John Doe + Scheduled
        $visitTarget = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient1->id,
            'visit_status_id' => $scheduledStatus->id
        ]);

        // Wrong Status: John Doe + Completed
        $visitWrongStatus = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient1->id,
            'visit_status_id' => $completedStatus->id
        ]);

        // Wrong Name: Jane Doe + Scheduled
        $visitWrongName = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient2->id,
            'visit_status_id' => $scheduledStatus->id
        ]);

        // Search for "John" with status "scheduled"
        $response = $this->get(route('visits.index', [
            'search' => 'John',
            'status' => 'scheduled'
        ]));

        $response->assertOk();
        $response->assertViewHas('visits', function ($visits) use ($visitTarget, $visitWrongStatus, $visitWrongName) {
            return $visits->contains($visitTarget) 
                && !$visits->contains($visitWrongStatus) 
                && !$visits->contains($visitWrongName);
        });
    }
}

<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\DoctorProfile;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitSchedulingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_schedule_visit_with_separated_date_time()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        
        // Ensure status exists
        $status = VisitStatus::where('slug', 'scheduled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'scheduled', 'name' => 'Scheduled']);

        $data = [
            'patient_id' => $patient->id,
            'scheduled_date' => '2025-01-01',
            'scheduled_time' => '10:00',
            'complaint' => 'Checkup',
        ];

        $response = $this->post(route('visits.store'), $data);

        $response->assertRedirect(route('visits.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('visits', [
            'patient_id' => $patient->id,
            'scheduled_at' => '2025-01-01 10:00:00',
            'user_id' => $doctor->id,
        ]);
    }

    public function test_validation_errors()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $response = $this->post(route('visits.store'), []);

        $response->assertSessionHasErrors(['patient_id', 'scheduled_at']);
    }

    public function test_handles_missing_default_status_gracefully()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);

        // Ensure NO statuses exist
        VisitStatus::query()->delete();

        $data = [
            'patient_id' => $patient->id,
            'scheduled_at' => '2025-01-01 10:00:00',
            'complaint' => 'Checkup',
        ];

        $response = $this->post(route('visits.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('visits', [
            'patient_id' => $patient->id,
            'visit_status_id' => null
        ]);
    }

    public function test_calculates_distance_and_fee_correctly()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        
        // Setup Doctor Profile (Jakarta Center)
        DoctorProfile::create([
            'user_id' => $doctor->id,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'service_radius_km' => 10,
            'base_transport_fee' => 50000,
            'transport_fee_per_km' => 5000,
        ]);

        // Mock RouteOptimizationService to ensure consistent distance regardless of routing engine
        $this->mock(\App\Services\RouteOptimizationService::class, function ($mock) {
            $mock->shouldReceive('getDistanceDuration')
                ->andReturn([
                    'distance' => 5.0, // Exactly 5km
                    'duration' => 15
                ]);
        });

        $this->actingAs($doctor);
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        
        $status = VisitStatus::where('slug', 'scheduled')->first() 
            ?? VisitStatus::factory()->create(['slug' => 'scheduled']);

        // Case: ~5km away
        $data = [
            'patient_id' => $patient->id,
            'scheduled_at' => '2025-01-01 10:00:00',
            'latitude' => -6.245000, 
            'longitude' => 106.816666,
        ];

        $response = $this->post(route('visits.store'), $data);
        $response->assertSessionHasNoErrors();
        
        $visit = Visit::latest()->first();
        $this->assertNotNull($visit->distance_km);
        $this->assertEquals(75000, $visit->transport_fee); // 50k + (5 * 5k)
    }

    public function test_rejects_location_outside_service_radius()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        
        DoctorProfile::create([
            'user_id' => $doctor->id,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'service_radius_km' => 10,
        ]);

        // Mock RouteOptimizationService
        $this->mock(\App\Services\RouteOptimizationService::class, function ($mock) {
            $mock->shouldReceive('getDistanceDuration')
                ->andReturn([
                    'distance' => 20.0, // 20km
                    'duration' => 60
                ]);
        });

        $this->actingAs($doctor);
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);

        // Case: ~20km away
        $data = [
            'patient_id' => $patient->id,
            'scheduled_at' => '2025-01-01 10:00:00',
            'latitude' => -6.400000, 
            'longitude' => 106.816666,
        ];

        $response = $this->post(route('visits.store'), $data);
        $response->assertSessionHasErrors('address');
    }
}

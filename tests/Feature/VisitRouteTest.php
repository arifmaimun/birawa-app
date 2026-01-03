<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Client;
use App\Models\VisitStatus;
use App\Models\DoctorProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class VisitRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_recommend_route_endpoint_returns_optimized_path()
    {
        // Setup
        $cancelledStatus = VisitStatus::firstOrCreate(['slug' => 'cancelled'], ['name' => 'Cancelled', 'color' => '#ff0000']);
        $pendingStatus = VisitStatus::firstOrCreate(['slug' => 'pending'], ['name' => 'Pending', 'color' => '#ffff00']);
        
        $doctor = User::factory()->create();
        $profile = DoctorProfile::create([
            'user_id' => $doctor->id,
            'latitude' => -6.200000,
            'longitude' => 106.816666, // Jakarta Center
        ]);
        
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        
        // Create 3 visits: Far, Near, Medium
        $date = Carbon::tomorrow()->toDateString();
        
        // Visit 1: Far (10km away)
        $visitFar = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $pendingStatus->id,
            'scheduled_at' => $date . ' 10:00:00',
            'latitude' => -6.290000, 
            'longitude' => 106.816666,
        ]);

        // Visit 2: Near (1km away)
        $visitNear = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $pendingStatus->id,
            'scheduled_at' => $date . ' 12:00:00',
            'latitude' => -6.210000, 
            'longitude' => 106.816666,
        ]);
        
        // Visit 3: Medium (5km away)
        $visitMedium = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $pendingStatus->id,
            'scheduled_at' => $date . ' 14:00:00',
            'latitude' => -6.250000, 
            'longitude' => 106.816666,
        ]);

        $response = $this->actingAs($doctor)->getJson(route('visits.recommend-route', ['date' => $date]));

        $response->assertStatus(200);
        
        // With Nearest Neighbor from Doctor (-6.20):
        // 1. Doctor -> Near (-6.21) (Dist: 0.01)
        // 2. Near -> Medium (-6.25) (Dist: 0.04)
        // 3. Medium -> Far (-6.29) (Dist: 0.04)
        // Order should be: Near, Medium, Far
        
        $data = $response->json();
        $this->assertCount(3, $data);
        $this->assertEquals($visitNear->id, $data[0]['id']);
        $this->assertEquals($visitMedium->id, $data[1]['id']);
        $this->assertEquals($visitFar->id, $data[2]['id']);
        
        // Assert Route Source and Estimates
        $this->assertContains($data[0]['route_source'], ['Haversine (Heuristic)', 'OSM/Valhalla', 'Mapbox (Real-time)']);
        $this->assertArrayHasKey('est_travel_minutes', $data[0]);
        $this->assertArrayHasKey('distance_from_prev', $data[0]);
    }

    public function test_calendar_events_endpoint_returns_geo_data()
    {
        $doctor = User::factory()->create();
        $client = Client::factory()->create(['address' => 'Test Address']);
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        
        $visit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'scheduled_at' => now(),
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $response = $this->actingAs($doctor)->getJson(route('visits.calendar-events', [
            'start' => now()->subDay()->toDateString(),
            'end' => now()->addDay()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'address' => 'Test Address',
        ]);
    }
}

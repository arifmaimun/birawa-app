<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitDistanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_calculates_distance_and_fee()
    {
        $user = User::factory()->create();
        
        // Setup Doctor Profile with Location and Fees
        // Location: Jakarta Monas (-6.175392, 106.827153)
        DoctorProfile::create([
            'user_id' => $user->id,
            'latitude' => -6.175392,
            'longitude' => 106.827153,
            'base_transport_fee' => 50000,
            'transport_fee_per_km' => 5000,
            'service_radius_km' => 20,
        ]);

        $this->actingAs($user);
        $patient = Patient::factory()->create();

        // Visit Location: Grand Indonesia (-6.195048, 106.820914) ~2.3 km away
        $response = $this->post(route('visits.store'), [
            'patient_id' => $patient->id,
            'scheduled_at' => now()->addDay(),
            'latitude' => -6.195048,
            'longitude' => 106.820914,
        ]);

        $response->assertRedirect(route('visits.index'));

        $visit = Visit::first();
        
        // Distance check (Approx 2.2 - 2.4 km)
        $this->assertTrue($visit->distance_km > 2.0 && $visit->distance_km < 2.5);

        // Fee Check
        // Fee = 50000 + (2.something * 5000) ~= 50000 + 11000 = 61000
        // We round to nearest 100
        $expectedFee = 50000 + ($visit->distance_km * 5000);
        $this->assertEquals(round($expectedFee, -2), $visit->transport_fee);
    }

    public function test_visit_rejected_outside_radius()
    {
        $user = User::factory()->create();
        
        // Setup Doctor Profile with Location (Monas)
        DoctorProfile::create([
            'user_id' => $user->id,
            'latitude' => -6.175392,
            'longitude' => 106.827153,
            'service_radius_km' => 5, // Small radius
        ]);

        $this->actingAs($user);
        $patient = Patient::factory()->create();

        // Visit Location: Bogor (-6.597147, 106.806039) ~40km away
        $response = $this->post(route('visits.store'), [
            'patient_id' => $patient->id,
            'scheduled_at' => now()->addDay(),
            'latitude' => -6.597147,
            'longitude' => 106.806039,
        ]);

        $response->assertSessionHasErrors('address'); // We used 'address' key for error
        $this->assertDatabaseCount('visits', 0);
    }
}

<?php

namespace Tests\Unit;

use App\Services\HaversineService;
use App\Services\OsrmService;
use App\Services\RoutingManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RoutingServiceTest extends TestCase
{
    /** @test */
    public function haversine_service_calculates_correctly()
    {
        $service = new HaversineService();
        // Distance between Jakarta (Monas) and Bogor (Kebun Raya) approx 44-50km straight line
        // Monas: -6.175392, 106.827153
        // Bogor: -6.597147, 106.799510
        
        $result = $service->getRoute(-6.175392, 106.827153, -6.597147, 106.799510);
        
        $this->assertArrayHasKey('distance_km', $result);
        $this->assertArrayHasKey('duration_minutes', $result);
        $this->assertEquals('haversine_fallback', $result['source']);
        $this->assertGreaterThan(40, $result['distance_km']);
        $this->assertLessThan(60, $result['distance_km']);
    }

    /** @test */
    public function osrm_service_returns_null_on_failure()
    {
        Http::fake([
            '*' => Http::response(null, 500)
        ]);

        $service = new OsrmService();
        $result = $service->getRoute(0, 0, 1, 1);

        $this->assertNull($result);
    }

    /** @test */
    public function osrm_service_triggers_circuit_breaker()
    {
        Http::fake([
            '*' => Http::response(null, 500)
        ]);

        $service = new OsrmService();
        
        // First fail
        $service->getRoute(0, 0, 1, 1);
        
        // Check cache
        $this->assertTrue(Cache::has('osrm_circuit_open'));
        
        // Next call should be skipped (logged as warning)
        $result = $service->getRoute(0, 0, 1, 1);
        $this->assertNull($result);
    }

    /** @test */
    public function routing_manager_falls_back_correctly()
    {
        // Mock OSRM to fail
        $osrm = \Mockery::mock(OsrmService::class);
        $osrm->shouldReceive('getRoute')->andReturn(null);

        // Mock Haversine to succeed
        $haversine = \Mockery::mock(HaversineService::class);
        $haversine->shouldReceive('getRoute')->andReturn([
            'distance_km' => 10,
            'duration_minutes' => 20,
            'source' => 'haversine_fallback'
        ]);

        $manager = new RoutingManager($osrm, $haversine);
        $result = $manager->getRoute(0, 0, 1, 1);

        $this->assertEquals(10, $result['distance_km']);
        $this->assertEquals('haversine_fallback', $result['source']);
    }
}

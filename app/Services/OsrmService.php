<?php

namespace App\Services;

use App\Contracts\RoutingServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\Response;

class OsrmService implements RoutingServiceInterface
{
    protected string $baseUrl = 'http://router.project-osrm.org/route/v1/driving';
    protected string $circuitBreakerKey = 'osrm_circuit_open';
    protected int $timeout = 5; // seconds

    public function getRoute(float $originLat, float $originLng, float $destLat, float $destLng): ?array
    {
        // Circuit Breaker Check
        if (Cache::has($this->circuitBreakerKey)) {
            Log::warning('OSRM Circuit Breaker is OPEN. Skipping request.');
            return null;
        }

        try {
            $url = "{$this->baseUrl}/{$originLng},{$originLat};{$destLng},{$destLat}?overview=false";
            
            $startTime = microtime(true);
            /** @var Response $response */
            $response = Http::timeout($this->timeout)->get($url);
            $duration = (microtime(true) - $startTime) * 1000;

            Log::debug('OSRM Request', [
                'url' => $url,
                'duration_ms' => $duration,
                'status' => $response->status()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['code']) && $data['code'] === 'Ok' && isset($data['routes'][0])) {
                    $route = $data['routes'][0];
                    return [
                        'distance_km' => isset($route['distance']) ? round($route['distance'] / 1000, 2) : 0,
                        'duration_minutes' => isset($route['duration']) ? round($route['duration'] / 60) : 0,
                        'source' => 'osrm'
                    ];
                }
            }

            $this->handleFailure();
            Log::error('OSRM API Error', ['body' => $response->body()]);
            
        } catch (\Exception $e) {
            $this->handleFailure();
            Log::error('OSRM Service Exception', ['message' => $e->getMessage()]);
        }

        return null;
    }

    protected function handleFailure()
    {
        // Simple failure counting could be added here. 
        // For now, if it fails, we assume it might be down or rate limited.
        // In a real scenario, we might count 3 failures in 1 minute before opening circuit.
        // For this implementation, let's open it for 30 seconds on any exception/error to be safe.
        Cache::put($this->circuitBreakerKey, true, 30);
    }
}

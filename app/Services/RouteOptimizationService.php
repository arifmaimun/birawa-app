<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RouteOptimizationService
{
    protected $mapboxToken;

    protected $valhallaUrl;

    protected $osrmUrl;

    public function __construct()
    {
        $this->mapboxToken = config('services.mapbox.token');
        $this->valhallaUrl = config('services.routing.valhalla_url');
        $this->osrmUrl = config('services.routing.osrm_url');
    }

    /**
     * Get optimized route using Nearest Neighbor with real-time distance/duration
     */
    public function optimizeRoute($visits, $startLocation)
    {
        $currentLocation = $startLocation;
        $unvisited = $visits->keyBy('id');
        $orderedVisits = collect([]);
        $totalDistance = 0;
        $totalDuration = 0;

        while ($unvisited->isNotEmpty()) {
            $nearestVisit = null;
            $minMetric = PHP_FLOAT_MAX; // Combined metric (distance + time)
            $bestLegData = null;

            foreach ($unvisited as $id => $visit) {
                $legData = $this->getDistanceDuration($currentLocation, [
                    'latitude' => $visit->latitude,
                    'longitude' => $visit->longitude,
                ]);

                // Metric: Primary is duration, fallback to distance
                // Use duration if available (real-time traffic), otherwise distance
                $metric = $legData['duration'] ?? ($legData['distance'] * 10); // arbitrary weight if no duration

                if ($metric < $minMetric) {
                    $minMetric = $metric;
                    $nearestVisit = $visit;
                    $bestLegData = $legData;
                }
            }

            if ($nearestVisit) {
                // Attach route info to the visit object
                $nearestVisit->distance_from_prev = round($bestLegData['distance'], 2); // km
                $nearestVisit->est_travel_minutes = round($bestLegData['duration'] / 60); // minutes
                $nearestVisit->route_source = $bestLegData['source'];

                $orderedVisits->push($nearestVisit);
                $unvisited->forget($nearestVisit->id);

                // Update current location
                $currentLocation = [
                    'latitude' => $nearestVisit->latitude,
                    'longitude' => $nearestVisit->longitude,
                ];

                $totalDistance += $bestLegData['distance'];
                $totalDuration += $bestLegData['duration'];
            } else {
                break; // Should not happen if unvisited is not empty
            }
        }

        return [
            'route' => $orderedVisits,
            'summary' => [
                'total_distance_km' => round($totalDistance, 2),
                'total_duration_minutes' => round($totalDuration / 60),
            ],
        ];
    }

    /**
     * Calculate distance and duration with fallback mechanism
     */
    public function getDistanceDuration($origin, $destination)
    {
        $cacheKey = "route_v2_{$origin['latitude']},{$origin['longitude']}_{$destination['latitude']},{$destination['longitude']}";

        return Cache::remember($cacheKey, 3600, function () use ($origin, $destination) {
            // 1. Try Mapbox (Highest Accuracy)
            if ($this->mapboxToken) {
                try {
                    $result = $this->getMapboxRoute($origin, $destination);
                    if ($result) {
                        return $result;
                    }
                } catch (\Exception $e) {
                    Log::warning('Mapbox API failed: '.$e->getMessage());
                }
            }

            // 2. Try Valhalla/OSM (Open Source)
            // Note: Prioritize Valhalla if configured locally as per instructions
            try {
                $result = $this->getValhallaRoute($origin, $destination);
                if ($result) {
                    return $result;
                }
            } catch (\Exception $e) {
                // Silently fail to next fallback
            }

            // 3. Fallback to Haversine Heuristic (Lowest Accuracy but Guaranteed)
            return $this->getHaversineHeuristic($origin, $destination);
        });
    }

    protected function getMapboxRoute($origin, $destination)
    {
        $url = "https://api.mapbox.com/directions/v5/mapbox/driving/{$origin['longitude']},{$origin['latitude']};{$destination['longitude']},{$destination['latitude']}";

        $response = Http::timeout(3)->get($url, [
            'access_token' => $this->mapboxToken,
            'geometries' => 'geojson',
            'overview' => 'false',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (! empty($data['routes'])) {
                $route = $data['routes'][0];

                return [
                    'distance' => $route['distance'] / 1000, // meters to km
                    'duration' => $route['duration'], // seconds
                    'source' => 'Mapbox (Real-time)',
                ];
            }
        }

        return null;
    }

    protected function getValhallaRoute($origin, $destination)
    {
        // Example implementation for Valhalla/OSRM
        // This assumes a standard OSRM or Valhalla API structure
        // If Valhalla URL is not reachable, this returns null

        // Simple check if URL is localhost and port is closed, skip... but Http timeout handles it.

        // Using OSRM format as generic open source example
        $url = "{$this->osrmUrl}/route/v1/driving/{$origin['longitude']},{$origin['latitude']};{$destination['longitude']},{$destination['latitude']}";

        $response = Http::timeout(2)->get($url); // Fast timeout for local/fallback

        if ($response->successful()) {
            $data = $response->json();
            if (! empty($data['routes'])) {
                $route = $data['routes'][0];

                return [
                    'distance' => $route['distance'] / 1000, // meters to km
                    'duration' => $route['duration'], // seconds
                    'source' => 'OSM/Valhalla',
                ];
            }
        }

        return null;
    }

    protected function getHaversineHeuristic($origin, $destination)
    {
        $lat1 = $origin['latitude'];
        $lon1 = $origin['longitude'];
        $lat2 = $destination['latitude'];
        $lon2 = $destination['longitude'];

        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        // Accuracy correction for short distances (<50km) - usually not needed for simple Haversine but can add "road factor"
        // Road factor: straight line distance * 1.4 (heuristic for urban road network)
        $roadFactor = 1.4;
        $estDistance = $distance * $roadFactor;

        // Time estimation
        // Base speed: 30 km/h (Urban default)
        // Can be adjusted by "crowdsourced" or user input factors in future
        $speedKmh = 30;
        $durationHours = $estDistance / $speedKmh;
        $durationSeconds = $durationHours * 3600;

        return [
            'distance' => $estDistance,
            'duration' => $durationSeconds,
            'source' => 'Haversine (Heuristic)',
        ];
    }
}

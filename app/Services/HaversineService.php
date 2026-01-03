<?php

namespace App\Services;

use App\Contracts\RoutingServiceInterface;

class HaversineService implements RoutingServiceInterface
{
    public function getRoute(float $originLat, float $originLng, float $destLat, float $destLng): ?array
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($destLat - $originLat);
        $dLon = deg2rad($destLng - $originLng);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($originLat)) * cos(deg2rad($destLat)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        // Estimate time: assume 30km/h average speed
        $estimatedTime = ($distance / 30) * 60;

        return [
            'distance_km' => round($distance, 2),
            'duration_minutes' => round($estimatedTime),
            'source' => 'haversine_fallback'
        ];
    }
}

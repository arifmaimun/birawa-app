<?php

namespace App\Contracts;

interface RoutingServiceInterface
{
    /**
     * Calculate distance and duration between two coordinates.
     *
     * @return array|null Returns ['distance_km' => float, 'duration_minutes' => int, 'source' => string]
     */
    public function getRoute(float $originLat, float $originLng, float $destLat, float $destLng): ?array;
}

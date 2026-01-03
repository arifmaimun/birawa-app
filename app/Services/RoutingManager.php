<?php

namespace App\Services;

use App\Contracts\RoutingServiceInterface;

class RoutingManager
{
    protected array $services = [];

    public function __construct(
        protected OsrmService $primaryService,
        protected HaversineService $fallbackService
    ) {}

    /**
     * Get route using primary service, falling back to secondary if needed.
     */
    public function getRoute(float $originLat, float $originLng, float $destLat, float $destLng): array
    {
        // Try Primary
        $result = $this->primaryService->getRoute($originLat, $originLng, $destLat, $destLng);
        
        if ($result) {
            return $result;
        }

        // Fallback
        return $this->fallbackService->getRoute($originLat, $originLng, $destLat, $destLng);
    }
}

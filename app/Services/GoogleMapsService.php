<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.key', env('GOOGLE_MAPS_API_KEY'));
    }

    /**
     * Calculate distance and duration between two coordinates.
     *
     * @return array|null Returns ['distance_km' => float, 'duration_minutes' => int] or null on failure
     */
    public function getDistanceAndDuration(float $originLat, float $originLng, float $destLat, float $destLng): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Google Maps API Key is missing.');

            return null;
        }

        try {
            $response = Http::get($this->baseUrl, [
                'origins' => "{$originLat},{$originLng}",
                'destinations' => "{$destLat},{$destLng}",
                'key' => $this->apiKey,
                'mode' => 'driving', // Assumed mode
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK' && isset($data['rows'][0]['elements'][0])) {
                    $element = $data['rows'][0]['elements'][0];

                    if ($element['status'] === 'OK') {
                        // distance.value is in meters
                        $distanceKm = isset($element['distance']['value']) ? round($element['distance']['value'] / 1000, 2) : 0;

                        // duration.value is in seconds
                        $durationMinutes = isset($element['duration']['value']) ? round($element['duration']['value'] / 60) : 0;

                        return [
                            'distance_km' => $distanceKm,
                            'duration_minutes' => $durationMinutes,
                        ];
                    }
                }
            }

            Log::error('Google Maps API Error: '.$response->body());
        } catch (\Exception $e) {
            Log::error('Google Maps Service Exception: '.$e->getMessage());
        }

        return null;
    }
}

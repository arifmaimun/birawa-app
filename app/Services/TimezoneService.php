<?php

namespace App\Services;

use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TimezoneService
{
    /**
     * Get list of all valid IANA timezones.
     * Cached for performance.
     *
     * @return array
     */
    public function getTimezones(): array
    {
        return Cache::remember('iana_timezones_list', 86400, function () {
            return DateTimeZone::listIdentifiers();
        });
    }

    /**
     * Get timezones formatted for Filament Select options.
     *
     * @return array
     */
    public function getTimezonesForSelect(): array
    {
        $timezones = $this->getTimezones();
        return array_combine($timezones, $timezones);
    }

    /**
     * Validate if a timezone string is valid.
     *
     * @param string|null $timezone
     * @return bool
     */
    public function isValidTimezone(?string $timezone): bool
    {
        if (empty($timezone)) {
            return false;
        }

        return in_array($timezone, $this->getTimezones());
    }

    /**
     * Convert local time to UTC.
     *
     * @param string|Carbon $time
     * @param string $localTimezone
     * @return Carbon|null
     */
    public function convertFromLocalToUtc($time, string $localTimezone): ?Carbon
    {
        try {
            if (!$this->isValidTimezone($localTimezone)) {
                throw new Exception("Invalid timezone: {$localTimezone}");
            }

            if (!$time instanceof Carbon) {
                $time = Carbon::parse($time, $localTimezone);
            } else {
                $time->setTimezone($localTimezone);
            }

            $utcTime = $time->copy()->setTimezone('UTC');
            
            Log::info('Timezone conversion (Local -> UTC)', [
                'local_time' => $time->toDateTimeString(),
                'local_timezone' => $localTimezone,
                'utc_time' => $utcTime->toDateTimeString(),
            ]);

            return $utcTime;
        } catch (Exception $e) {
            Log::error('Timezone conversion failed (Local -> UTC)', [
                'time' => $time,
                'timezone' => $localTimezone,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Convert UTC time to local.
     *
     * @param string|Carbon $utcTime
     * @param string $localTimezone
     * @return Carbon|null
     */
    public function convertFromUtcToLocal($utcTime, string $localTimezone): ?Carbon
    {
        try {
            if (!$this->isValidTimezone($localTimezone)) {
                throw new Exception("Invalid timezone: {$localTimezone}");
            }

            if (!$utcTime instanceof Carbon) {
                $utcTime = Carbon::parse($utcTime, 'UTC');
            } else {
                $utcTime->setTimezone('UTC');
            }

            $localTime = $utcTime->copy()->setTimezone($localTimezone);

            // Detailed logging as requested
            // Log::debug('Timezone conversion (UTC -> Local)', [
            //     'utc_time' => $utcTime->toDateTimeString(),
            //     'local_timezone' => $localTimezone,
            //     'local_time' => $localTime->toDateTimeString(),
            // ]);

            return $localTime;
        } catch (Exception $e) {
            Log::error('Timezone conversion failed (UTC -> Local)', [
                'time' => $utcTime,
                'timezone' => $localTimezone,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

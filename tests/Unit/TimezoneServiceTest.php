<?php

namespace Tests\Unit;

use App\Services\TimezoneService;
use Carbon\Carbon;
use Tests\TestCase;

class TimezoneServiceTest extends TestCase
{
    protected TimezoneService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TimezoneService();
    }

    public function test_get_timezones_returns_array()
    {
        $timezones = $this->service->getTimezones();
        $this->assertIsArray($timezones);
        $this->assertContains('UTC', $timezones);
        $this->assertContains('Asia/Jakarta', $timezones);
    }

    public function test_is_valid_timezone()
    {
        $this->assertTrue($this->service->isValidTimezone('Asia/Jakarta'));
        $this->assertTrue($this->service->isValidTimezone('UTC'));
        $this->assertFalse($this->service->isValidTimezone('Invalid/Timezone'));
        $this->assertFalse($this->service->isValidTimezone(null));
    }

    public function test_convert_local_to_utc()
    {
        $localTimezone = 'Asia/Jakarta'; // UTC+7
        $localTime = '2023-10-10 10:00:00';
        
        $utcTime = $this->service->convertFromLocalToUtc($localTime, $localTimezone);
        
        $this->assertEquals('2023-10-10 03:00:00', $utcTime->format('Y-m-d H:i:s'));
        $this->assertEquals('UTC', $utcTime->timezoneName);
    }

    public function test_convert_utc_to_local()
    {
        $localTimezone = 'Asia/Jakarta'; // UTC+7
        $utcTime = '2023-10-10 03:00:00';
        
        $localTime = $this->service->convertFromUtcToLocal($utcTime, $localTimezone);
        
        $this->assertEquals('2023-10-10 10:00:00', $localTime->format('Y-m-d H:i:s'));
        $this->assertEquals('Asia/Jakarta', $localTime->timezoneName);
    }

    public function test_dst_handling()
    {
        // New York has DST.
        // On Nov 5 2023, DST ended at 2am. Clocks went back 1 hour.
        // Before DST end: 2023-11-04 12:00:00 EDT is UTC-4.
        $timezone = 'America/New_York';
        $localTime = '2023-11-04 12:00:00';
        $utcTime = $this->service->convertFromLocalToUtc($localTime, $timezone);
        // 12:00 EDT + 4 = 16:00 UTC
        $this->assertEquals('16:00:00', $utcTime->format('H:i:s'));

        // After DST end: 2023-11-06 12:00:00 EST is UTC-5.
        $localTimeAfter = '2023-11-06 12:00:00';
        $utcTimeAfter = $this->service->convertFromLocalToUtc($localTimeAfter, $timezone);
        // 12:00 EST + 5 = 17:00 UTC
        $this->assertEquals('17:00:00', $utcTimeAfter->format('H:i:s'));
    }

    public function test_extreme_timezones()
    {
        // Pacific/Kiritimati is UTC+14
        $timezone = 'Pacific/Kiritimati';
        $localTime = '2023-01-01 14:00:00';
        $utcTime = $this->service->convertFromLocalToUtc($localTime, $timezone);
        // 14:00 - 14 = 00:00
        $this->assertEquals('2023-01-01 00:00:00', $utcTime->format('Y-m-d H:i:s'));

        // Pacific/Niue is UTC-11
        $timezone = 'Pacific/Niue';
        $localTime = '2023-01-01 01:00:00';
        $utcTime = $this->service->convertFromLocalToUtc($localTime, $timezone);
        // 01:00 + 11 = 12:00
        $this->assertEquals('2023-01-01 12:00:00', $utcTime->format('Y-m-d H:i:s'));
    }
}

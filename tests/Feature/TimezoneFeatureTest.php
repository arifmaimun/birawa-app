<?php

namespace Tests\Feature;

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TimezoneFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_sets_display_timezone_but_keeps_app_timezone_utc()
    {
        $user = User::factory()->create();
        $profile = DoctorProfile::create([
            'user_id' => $user->id,
            'timezone' => 'Asia/Jayapura', // UTC+9
            'service_radius_km' => 10,
            'base_transport_fee' => 10000,
            'transport_fee_per_km' => 2000,
        ]);

        $this->actingAs($user);

        // Make a request to a route that uses the middleware.
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        
        // Assert that the DISPLAY timezone is set
        $this->assertEquals('Asia/Jayapura', Config::get('app.display_timezone'));
        
        // Assert that the APP timezone remains UTC (critical for DB integrity)
        $this->assertEquals('UTC', Config::get('app.timezone'));
    }

    public function test_middleware_uses_default_if_no_profile_timezone()
    {
        $defaultTimezone = Config::get('app.timezone');
        
        $user = User::factory()->create();
        DoctorProfile::create([
            'user_id' => $user->id,
            'timezone' => null,
            'service_radius_km' => 10,
            'base_transport_fee' => 10000,
            'transport_fee_per_km' => 2000,
        ]);

        $this->actingAs($user);
        $this->get('/dashboard');

        $this->assertEquals($defaultTimezone, Config::get('app.timezone'));
        $this->assertEquals($defaultTimezone, Config::get('app.display_timezone', $defaultTimezone));
    }

    public function test_avatar_cache_busting()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        
        // Initial state
        $this->assertNull($user->avatar_url);
        
        // Upload avatar
        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);
        
        // Check URL has timestamp
        $url = $user->avatar_url;
        $this->assertNotNull($url);
        $this->assertStringContainsString('?t=' . $user->updated_at->timestamp, $url);
        
        // Update again
        sleep(1); // Ensure timestamp changes
        $user->touch(); // Update updated_at
        $user->refresh();
        
        $newUrl = $user->avatar_url;
        $this->assertNotEquals($url, $newUrl);
        $this->assertStringContainsString('?t=' . $user->updated_at->timestamp, $newUrl);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_avatar()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('avatar.webp');

        $response = $this->postJson(route('profile.avatar.update'), [
            'avatar' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Check if file is stored
        // Filename is ID-TIMESTAMP.webp, we can't guess timestamp exactly but we can check if directory has files
        $this->assertNotEmpty(Storage::disk('public')->files('avatars'));
        
        // Check user record
        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_upload_invalid_format()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson(route('profile.avatar.update'), [
            'avatar' => $file,
        ]);

        $response->assertStatus(422);
    }

    public function test_upload_too_large()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 3MB file (limit is 2MB)
        $file = UploadedFile::fake()->create('large.jpg', 3072);

        $response = $this->postJson(route('profile.avatar.update'), [
            'avatar' => $file,
        ]);

        $response->assertStatus(422);
    }
}

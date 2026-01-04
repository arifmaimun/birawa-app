<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Friendship;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FullFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_manage_users()
    {
        // Setup Roles
        $role = Role::create(['name' => 'superadmin']);

        $admin = User::factory()->create(['role' => 'superadmin']);
        $admin->assignRole('superadmin');

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Doctor',
            'email' => 'doc@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'veterinarian',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'doc@example.com', 'role' => 'veterinarian']);

        $user = User::where('email', 'doc@example.com')->first();

        // Admin updates password
        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'New Doctor Updated',
            'email' => 'doc@example.com',
            'role' => 'veterinarian',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    public function test_visit_flow_and_prediction()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $client = Client::factory()->create();
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        $status = VisitStatus::firstOrCreate(
            ['slug' => 'scheduled'],
            ['name' => 'Scheduled', 'color' => '#000000']
        );
        VisitStatus::firstOrCreate(
            ['slug' => 'completed'],
            ['name' => 'Completed', 'color' => '#00FF00']
        );

        // 1. Create a past visit with travel time
        Visit::create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $status->id, // simplified
            'scheduled_at' => now()->subDays(1),
            'distance_km' => 10,
            'actual_travel_minutes' => 25, // 25 mins for 10km
        ]);

        // 2. Create a new visit via Controller to trigger prediction logic check (though logic is in controller helper)
        // We will test the helper logic by hitting the show endpoint or just using the model/controller logic

        $response = $this->actingAs($doctor)->getJson(route('visits.index'));
        $response->assertStatus(200);

        // Let's create another visit and check if show returns prediction
        $visit2 = Visit::create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $status->id,
            'scheduled_at' => now()->addDays(1),
            'distance_km' => 10,
        ]);

        $response = $this->actingAs($doctor)->getJson(route('visits.show', $visit2));
        $response->assertStatus(200);

        // Assert prediction is present and close to 25
        $response->assertJsonFragment(['predicted_travel_minutes' => 25]);
    }

    public function test_social_features()
    {
        $doc1 = User::factory()->create(['role' => 'veterinarian']);
        $doc2 = User::factory()->create(['role' => 'veterinarian']);

        // 1. Doc1 sends request to Doc2
        $response = $this->actingAs($doc1)->postJson(route('friends.request'), [
            'friend_id' => $doc2->id,
        ]);
        $response->assertStatus(201);

        $this->assertDatabaseHas('friendships', [
            'user_id' => $doc1->id,
            'friend_id' => $doc2->id,
            'status' => 'pending',
        ]);

        // 2. Doc2 accepts
        $friendship = Friendship::where('user_id', $doc1->id)->where('friend_id', $doc2->id)->first();

        $response = $this->actingAs($doc2)->patchJson(route('friends.accept', $friendship));
        $response->assertStatus(200);

        // 3. Check bidirectional
        $this->assertDatabaseHas('friendships', [
            'user_id' => $doc1->id,
            'friend_id' => $doc2->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('friendships', [
            'user_id' => $doc2->id,
            'friend_id' => $doc1->id,
            'status' => 'accepted',
        ]);
    }
}

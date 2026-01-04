<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Friendship;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_friend_request()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('friends.request'), [
            'friend_id' => $user2->id,
        ]);

        $response->assertStatus(302); // Redirects back
        $this->assertDatabaseHas('friendships', [
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_accept_friend_request()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $friendship = Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user2)->post(route('friends.accept', $friendship->id));

        $response->assertStatus(302); // Redirects back
        $this->assertDatabaseHas('friendships', [
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'accepted',
        ]);
        
        // Check reverse creation (simplified logic in controller)
        $this->assertDatabaseHas('friendships', [
            'user_id' => $user2->id,
            'friend_id' => $user1->id,
            'status' => 'accepted',
        ]);
    }

    public function test_user_can_send_message()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->postJson(route('chat.store'), [
            'receiver_id' => $user2->id,
            'message' => 'Hello Doctor!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Hello Doctor!',
        ]);
    }

    public function test_user_can_get_messages()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Message::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Hi there',
        ]);

        $response = $this->actingAs($user1)->getJson(route('chat.messages', $user2->id));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }
}

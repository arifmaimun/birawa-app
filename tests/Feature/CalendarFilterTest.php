<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatus;
use App\Models\Patient;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class CalendarFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $statuses;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'veterinarian',
        ]);

        // Create standard statuses
        $this->statuses = [
            'scheduled' => VisitStatus::firstOrCreate(['slug' => 'scheduled'], ['name' => 'Scheduled', 'color' => '#3B82F6']),
            'completed' => VisitStatus::firstOrCreate(['slug' => 'completed'], ['name' => 'Completed', 'color' => '#10B981']),
            'cancelled' => VisitStatus::firstOrCreate(['slug' => 'cancelled'], ['name' => 'Cancelled', 'color' => '#EF4444']),
        ];
    }

    public function test_calendar_events_endpoint_returns_events()
    {
        $visit = Visit::factory()->create([
            'user_id' => $this->user->id,
            'scheduled_at' => Carbon::now(),
            'visit_status_id' => $this->statuses['scheduled']->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('visits.calendar-events', [
                'start' => Carbon::now()->subDays(10)->toIso8601String(),
                'end' => Carbon::now()->addDays(10)->toIso8601String(),
            ]));

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_calendar_events_filter_by_status()
    {
        // Scheduled Visit (Should appear)
        Visit::factory()->create([
            'user_id' => $this->user->id,
            'scheduled_at' => Carbon::now(),
            'visit_status_id' => $this->statuses['scheduled']->id,
        ]);

        // Completed Visit (Should NOT appear)
        Visit::factory()->create([
            'user_id' => $this->user->id,
            'scheduled_at' => Carbon::now(),
            'visit_status_id' => $this->statuses['completed']->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('visits.calendar-events', [
                'start' => Carbon::now()->subDays(10)->toIso8601String(),
                'end' => Carbon::now()->addDays(10)->toIso8601String(),
                'status' => ['scheduled'] // Filter only scheduled
            ]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['backgroundColor' => $this->statuses['scheduled']->color]); // Check color match
    }

    public function test_calendar_events_filter_by_search_patient_name()
    {
        $client1 = Client::factory()->create(['name' => 'John Doe']);
        $patient1 = Patient::factory()->create(['client_id' => $client1->id, 'name' => 'Fluffy']);

        $client2 = Client::factory()->create(['name' => 'Jane Smith']);
        $patient2 = Patient::factory()->create(['client_id' => $client2->id, 'name' => 'Rex']);

        // Visit for Fluffy (Should appear)
        Visit::factory()->create([
            'user_id' => $this->user->id,
            'patient_id' => $patient1->id,
            'scheduled_at' => Carbon::now(),
            'visit_status_id' => $this->statuses['scheduled']->id,
        ]);

        // Visit for Rex (Should NOT appear)
        Visit::factory()->create([
            'user_id' => $this->user->id,
            'patient_id' => $patient2->id,
            'scheduled_at' => Carbon::now(),
            'visit_status_id' => $this->statuses['scheduled']->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('visits.calendar-events', [
                'start' => Carbon::now()->subDays(10)->toIso8601String(),
                'end' => Carbon::now()->addDays(10)->toIso8601String(),
                'search' => 'Fluffy'
            ]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => "Fluffy (John Doe)"]);
    }
}
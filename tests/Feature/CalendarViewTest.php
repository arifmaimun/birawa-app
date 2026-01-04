<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VisitStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarViewTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'veterinarian',
        ]);

        // Ensure statuses exist for the view
        VisitStatus::firstOrCreate(['slug' => 'scheduled'], ['name' => 'Scheduled', 'color' => '#3B82F6']);
    }

    public function test_visits_index_returns_calendar_view()
    {
        $response = $this->actingAs($this->user)
            ->get(route('visits.index'));

        $response->assertStatus(200);
        $response->assertSee('Filter Jadwal');
        $response->assertSee('id="calendar"', false);
    }

    public function test_visits_calendar_redirects_to_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('visits.calendar'));

        $response->assertRedirect(route('visits.index'));
    }
}

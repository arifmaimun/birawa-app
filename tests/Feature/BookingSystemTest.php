<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MessageTemplate;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_create_message_template()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $this->actingAs($doctor);

        $response = $this->post(route('message-templates.store'), [
            'title' => 'Departure Template',
            'type' => 'whatsapp',
            'trigger_event' => 'on_departure',
            'content_pattern' => 'Hello {owner_name}, doctor {doctor_name} is leaving. ETA: {eta}.',
        ]);

        $response->assertRedirect(route('message-templates.index'));
        $this->assertDatabaseHas('message_templates', [
            'doctor_id' => $doctor->id,
            'title' => 'Departure Template',
            'trigger_event' => 'on_departure',
        ]);
    }

    public function test_start_trip_updates_visit_and_returns_whatsapp_url()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $client = Client::factory()->create(['phone' => '08123456789']);
        $patient = Patient::factory()->create(['client_id' => $client->id]);

        // Get or create status
        $scheduledStatus = VisitStatus::where('slug', 'scheduled')->first() ?? VisitStatus::factory()->create(['slug' => 'scheduled']);

        $visit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $scheduledStatus->id,
        ]);

        // Ensure OTW status exists
        $otwStatus = VisitStatus::where('slug', 'otw')->first() ?? VisitStatus::factory()->create(['slug' => 'otw']);

        // Create template
        MessageTemplate::factory()->create([
            'doctor_id' => $doctor->id,
            'trigger_event' => 'on_departure',
            'content_pattern' => 'OTW to {owner_name}. ETA {eta}',
            'type' => 'whatsapp',
        ]);

        $this->actingAs($doctor);

        $response = $this->postJson(route('visits.start-trip', $visit), [
            'estimated_hours' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertNotNull($response->json('whatsapp_url'));
        $this->assertStringContainsString('OTW to '.$client->name, urldecode($response->json('whatsapp_url')));

        $visit->refresh();
        $this->assertNotNull($visit->departure_time);
        $this->assertEquals(60, $visit->estimated_travel_minutes);
        // $this->assertEquals('otw', $visit->status); // Legacy column check
        $this->assertEquals($otwStatus->id, $visit->visit_status_id);
    }

    public function test_end_trip_updates_visit_and_returns_whatsapp_url()
    {
        $doctor = User::factory()->create(['role' => 'veterinarian']);
        $client = Client::factory()->create(['phone' => '08123456789']);
        $patient = Patient::factory()->create(['client_id' => $client->id]);

        $otwStatus = VisitStatus::where('slug', 'otw')->first() ?? VisitStatus::factory()->create(['slug' => 'otw']);

        $visit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'visit_status_id' => $otwStatus->id,
            'departure_time' => Carbon::now()->subMinutes(30),
            'estimated_travel_minutes' => 30,
        ]);

        // Ensure Arrived status exists
        $arrivedStatus = VisitStatus::where('slug', 'arrived')->first() ?? VisitStatus::factory()->create(['slug' => 'arrived']);

        // Create template
        MessageTemplate::factory()->create([
            'doctor_id' => $doctor->id,
            'trigger_event' => 'on_arrival',
            'content_pattern' => 'Arrived at {address}.',
            'type' => 'whatsapp',
        ]);

        $this->actingAs($doctor);

        $response = $this->postJson(route('visits.end-trip', $visit));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertNotNull($response->json('whatsapp_url'));
        $this->assertStringContainsString('Arrived at', urldecode($response->json('whatsapp_url')));

        $visit->refresh();
        $this->assertNotNull($visit->arrival_time);
        $this->assertNotNull($visit->actual_travel_minutes);
        // $this->assertEquals('arrived', $visit->status); // Checking legacy status column update
        $this->assertEquals($arrivedStatus->id, $visit->visit_status_id);
    }
}

<?php

namespace Tests\Feature;

use App\Filament\Resources\VisitResource\Pages\ListVisits;
use App\Models\MessageTemplate;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_template_populates_message()
    {
        // 1. Setup Doctor
        $doctor = User::factory()->create();
        $this->actingAs($doctor);

        // 2. Setup Data
        $clientUser = User::factory()->create();
        $client = Client::create(['user_id' => $clientUser->id, 'name' => 'John Doe', 'phone' => '08123456789']);
        $patient = Patient::create(['client_id' => $client->id, 'name' => 'Fluffy', 'species' => 'Cat']);
        $visit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'scheduled_at' => '2025-01-01 10:00:00',
        ]);

        // 3. Create Template
        $template = MessageTemplate::create([
            'doctor_id' => $doctor->id,
            'type' => 'other',
            'title' => 'Reminder',
            'content_pattern' => 'Hello {nama_klien}, reminder for {nama_pasien} at {jam_visit}.',
        ]);

        // 4. Test Livewire
        // Note: The action name in VisitResource is 'whatsapp'
        // The form field is 'template_id'
        
        Livewire::test(ListVisits::class)
            ->mountTableAction('whatsapp', $visit)
            ->setTableActionData(['template_id' => $template->id])
            ->assertTableActionDataSet([
                'message' => 'Hello John Doe, reminder for Fluffy at 10:00.',
            ]);
    }
}

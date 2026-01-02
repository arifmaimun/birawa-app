<?php

namespace Tests\Feature;

use App\Filament\Resources\ClientResource;
use App\Filament\Resources\PatientResource;
use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PatientClientCardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $user = User::factory()->create();
        
        // Check if role exists or create it
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
        
        // Create permissions and assign to role
        $permissions = [
            'view_any_patient', 'view_patient', 'create_patient', 'update_patient',
            'view_any_client', 'view_client', 'create_client', 'update_client',
        ];
        
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }
        
        $role->syncPermissions($permissions);
        $user->assignRole($role);
        
        $this->actingAs($user);
    }

    public function test_can_render_patient_cards()
    {
        // Ensure some data exists
        $patient = Patient::factory()->create();

        // Visit the page
        $this->get(PatientResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_patient_has_many_clients()
    {
        $patient = Patient::factory()->create();
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();

        $patient->clients()->attach([$client1->id, $client2->id]);

        $this->assertCount(2, $patient->fresh()->clients);
    }

    public function test_can_attach_client_to_patient_action()
    {
        $patient = Patient::factory()->create();
        $client = Client::factory()->create();

        // We simulate calling the action on the List page
        Livewire::test(PatientResource\Pages\ListPatients::class)
            ->callTableAction('addOwner', $patient, data: [
                'client_id' => $client->id,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertTrue($patient->fresh()->clients->contains($client->id));
    }

    public function test_can_create_and_link_patient_from_client_card_action()
    {
        $client = Client::factory()->create();

        Livewire::test(ClientResource\Pages\ListClients::class)
            ->callTableAction('addPatient', $client, data: [
                'name' => 'Fluffy',
                'species' => 'Cat',
                'gender' => 'female',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertTrue($client->fresh()->patients()->where('name', 'Fluffy')->exists());
    }

    public function test_can_link_existing_patient_from_client_card_action()
    {
        $client = Client::factory()->create();
        $patient = Patient::factory()->create();

        Livewire::test(ClientResource\Pages\ListClients::class)
            ->callTableAction('linkPatient', $client, data: [
                'patient_id' => $patient->id,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertTrue($client->fresh()->patients->contains($patient->id));
    }

    public function test_can_render_patient_without_owner()
    {
        $patient = Patient::factory()->create();
        // Detach any automatically created clients if factory does that
        $patient->clients()->detach();

        $this->get(PatientResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_render_client_without_patient()
    {
        $client = Client::factory()->create();
        $client->patients()->detach();

        $this->get(ClientResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_prevent_duplicate_relations()
    {
        $client = Client::factory()->create();
        $patient = Patient::factory()->create();

        // First link
        Livewire::test(ClientResource\Pages\ListClients::class)
            ->callTableAction('linkPatient', $client, data: [
                'patient_id' => $patient->id,
            ])
            ->assertHasNoTableActionErrors();

        // Second link (should not fail, but shouldn't duplicate)
        Livewire::test(ClientResource\Pages\ListClients::class)
            ->callTableAction('linkPatient', $client, data: [
                'patient_id' => $patient->id,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertCount(1, $client->fresh()->patients);
    }
}

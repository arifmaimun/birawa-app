<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Patient;
use App\Models\FormOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCreateTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_client_create_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('clients.create'));
        $response->assertStatus(200);
        $response->assertSee('Informasi Klien');
    }

    public function test_can_create_client_with_patient_and_address()
    {
        FormOption::firstOrCreate(
            ['category' => 'id_type', 'value' => 'KTP'],
            ['is_active' => true]
        );
        
        $data = [
            'is_business' => '0',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '08123456789',
            'email' => 'john@example.com',
            'id_type' => 'KTP',
            'id_number' => '1234567890',
            'gender' => 'Laki-laki',
            'addresses' => [
                [
                    'street' => 'Jl. Test No. 1',
                    'city' => 'Jakarta',
                    'country' => 'Indonesia',
                ]
            ],
            // Patient Data
            'patient_name' => 'Mochi',
            'patient_species' => 'Kucing',
            'patient_gender' => 'Jantan',
        ];

        $response = $this->actingAs($this->user)->post(route('clients.store'), $data);

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clients', [
            'phone' => '08123456789',
            'first_name' => 'John',
        ]);

        $client = Client::where('phone', '08123456789')->first();
        
        $this->assertDatabaseHas('client_addresses', [
            'client_id' => $client->id,
            'street' => 'Jl. Test No. 1',
        ]);

        $this->assertDatabaseHas('patients', [
            'client_id' => $client->id,
            'name' => 'Mochi',
            'species' => 'Kucing',
        ]);
    }

    public function test_validation_fails_if_patient_info_missing()
    {
        $data = [
            'is_business' => '0',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '08123456789',
            'addresses' => [
                ['street' => 'Jl. Test']
            ],
            // Missing patient data
        ];

        $response = $this->actingAs($this->user)->post(route('clients.store'), $data);
        $response->assertSessionHasErrors(['patient_name', 'patient_species', 'patient_gender']);
    }
}

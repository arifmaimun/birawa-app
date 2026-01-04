<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OptimizedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user to act as
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($this->user);
    }

    public function test_client_creation_with_patient_and_address()
    {
        $response = $this->post(route('clients.store'), [
            // Client Basic
            'is_business' => 0,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '081234567890',
            'email' => 'john@example.com',

            // Client Details
            'gender' => 'Laki-laki',
            'dob' => '1990-01-01',

            // Addresses
            'addresses' => [
                [
                    'street' => 'Jl. Test No. 123',
                    'city' => 'Jakarta',
                    'province' => 'DKI Jakarta',
                    'country' => 'Indonesia',
                ],
            ],

            // Patient Section (Prefixed)
            'patient_name' => 'Mochi',
            'patient_species' => 'Kucing',
            'patient_breed' => 'Persia',
            'patient_dob' => '2020-01-01',
            'patient_gender' => 'Jantan',
            'patient_is_sterile' => 1,
        ]);

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clients', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '081234567890',
        ]);

        $client = Client::where('phone', '081234567890')->first();

        $this->assertDatabaseHas('patients', [
            'client_id' => $client->id,
            'name' => 'Mochi',
            'species' => 'Kucing',
            'gender' => 'Jantan',
        ]);

        $this->assertDatabaseHas('client_addresses', [
            'client_id' => $client->id,
            'street' => 'Jl. Test No. 123',
        ]);
    }

    public function test_patient_creation_via_modal()
    {
        // Create a client first
        $client = Client::create([
            'user_id' => $this->user->id,
            'name' => 'Jane Doe',
            'phone' => '08987654321',
            'address' => 'Jl. Client',
        ]);

        $response = $this->post(route('patients.store'), [
            'client_id' => $client->id,
            'name' => 'Bruno',
            'species' => 'Anjing',
            'breed' => 'Bulldog',
            'dob' => '2019-05-05',
            'gender' => 'Jantan', // Capitalized as per component
            'is_sterile' => 0,
        ]);

        $response->assertRedirect(route('patients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('patients', [
            'client_id' => $client->id,
            'name' => 'Bruno',
            'species' => 'Anjing',
            'gender' => 'Jantan',
        ]);
    }

    public function test_product_creation_with_sku()
    {
        $response = $this->post(route('products.store'), [
            'name' => 'Vaksin Rabies',
            'sku' => 'VAK-RAB-001',
            'price' => 150000,
            'cost' => 100000,
            'stock' => 10,
            'category' => 'Vaksin',
            'type' => 'goods',
            'unit' => 'Vial',
            'description' => 'Vaksin anti rabies',
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Vaksin Rabies',
            'sku' => 'VAK-RAB-001',
        ]);
    }

    public function test_profile_update()
    {
        $response = $this->patch(route('profile.update'), [
            'name' => 'Dr. Updated',
            'email' => $this->user->email,
            'phone' => '0811111111',
            'specialty' => 'Surgeon',
            'bio' => 'Experienced vet.',
        ]);

        $response->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Dr. Updated',
        ]);

        $this->assertDatabaseHas('doctor_profiles', [
            'user_id' => $this->user->id,
            'specialty' => 'Surgeon',
        ]);
    }
}

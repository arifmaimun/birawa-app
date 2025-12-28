<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 1 Superadmin
        User::factory()->create([
            'name' => 'Drh. Arif',
            'email' => 'admin@birawa.vet',
            'role' => 'superadmin',
            'phone' => '08123456789',
            'address' => 'Jakarta',
        ]);

        // Create 1 Veterinarian
        User::factory()->create([
            'name' => 'Dr. Mitra',
            'email' => 'mitra@example.com',
            'role' => 'veterinarian',
            'phone' => '08198765432',
            'address' => 'Bandung',
        ]);

        // Create 20 Products
        \App\Models\Product::factory(20)->create();

        // Create 5 Clients (Users)
        $clients = User::factory(5)->create(['role' => 'client']);

        // Create 10 Patients and attach to random clients
        $patients = \App\Models\Patient::factory(10)->create();

        foreach ($patients as $patient) {
            // Attach to 1 or 2 random clients
            $randomClients = $clients->random(rand(1, 2));
            foreach ($randomClients as $client) {
                $patient->owners()->attach($client->id, ['is_primary' => true]); // Simplified
            }
        }
    }
}

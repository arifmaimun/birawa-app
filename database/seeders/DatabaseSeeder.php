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

        // Create 5 Users with Client profiles
        $users = User::factory(5)->create(['role' => 'client']);
        $clients = collect();

        foreach ($users as $user) {
            $clients->push(\App\Models\Client::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'address' => $user->address ?? 'Address',
            ]));
        }

        // Create 10 Patients assigned to random clients
        foreach ($clients as $client) {
            \App\Models\Patient::factory(2)->create([
                'client_id' => $client->id,
            ]);
        }
    }
}

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
            'email' => 'arif@example.com',
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

        // Create 10 Owners and 10 Patients (Patients will create Owners via factory)
        \App\Models\Patient::factory(10)->create();
    }
}

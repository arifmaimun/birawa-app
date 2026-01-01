<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StorageLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find Central Warehouse User (System User)
        $warehouseManager = User::firstOrCreate(
            ['email' => 'warehouse@birawavet.id'],
            [
                'name' => 'Central Warehouse Manager',
                'password' => Hash::make('password'),
                // Assign role if needed, e.g. 'warehouse_manager'
            ]
        );

        // Create Central Warehouse Location
        StorageLocation::firstOrCreate(
            [
                'user_id' => $warehouseManager->id,
                'name' => 'Central Warehouse',
            ],
            [
                'type' => 'warehouse',
                'description' => 'Main Central Storage for all clinical products',
                'capacity' => 100000,
                'is_default' => true,
            ]
        );
    }
}

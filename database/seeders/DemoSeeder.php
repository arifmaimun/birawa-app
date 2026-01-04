<?php

namespace Database\Seeders;

use App\Models\DoctorInventory;
use App\Models\Patient;
use App\Models\Product;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Get the Veterinarian (Dr. Mitra)
        $doctor = User::where('email', 'mitra@example.com')->first();
        if (! $doctor) {
            $this->command->error("Doctor 'mitra@example.com' not found. Run DatabaseSeeder first.");

            return;
        }

        // 2. Setup Doctor Profile (Required for Routing)
        if (! $doctor->doctorProfile) {
            \App\Models\DoctorProfile::create([
                'user_id' => $doctor->id,
                'specialty' => 'General Vet',
                'service_radius_km' => 20,
                'base_transport_fee' => 50000,
                'transport_fee_per_km' => 5000,
                'latitude' => -6.200000, // Jakarta Center
                'longitude' => 106.816666,
                // 'is_active' => true, // Removed if not in schema, assuming default or check model
            ]);
            $this->command->info('Doctor Profile created.');
        }

        // 3. Populate Inventory (DoctorInventory) from Products
        $products = Product::all();
        foreach ($products as $product) {
            DoctorInventory::firstOrCreate(
                [
                    'user_id' => $doctor->id,
                    'product_id' => $product->id,
                ],
                [
                    'item_name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category ?? 'medicine',
                    'stock_qty' => rand(10, 100),
                    'unit' => $product->unit ?? 'pcs',
                    // 'purchase_price' => $product->price * 0.7, // Column doesn't exist
                    'selling_price' => $product->price,
                    'average_cost_price' => $product->price * 0.7,
                    'alert_threshold' => 5, // Was min_stock_alert
                    // 'is_active' => true,
                ]
            );
        }
        $this->command->info('Inventory populated.');

        // 4. Create Visit Statuses (if not exist)
        $statuses = [
            ['name' => 'Scheduled', 'slug' => 'scheduled', 'color' => '#3b82f6'],
            ['name' => 'On The Way', 'slug' => 'otw', 'color' => '#eab308'],
            ['name' => 'Arrived', 'slug' => 'arrived', 'color' => '#22c55e'],
            ['name' => 'Completed', 'slug' => 'completed', 'color' => '#10b981'],
            ['name' => 'Cancelled', 'slug' => 'cancelled', 'color' => '#ef4444'],
        ];

        foreach ($statuses as $status) {
            VisitStatus::firstOrCreate(['slug' => $status['slug']], $status);
        }

        // 5. Create Visits (Today and Tomorrow)
        $patients = Patient::inRandomOrder()->take(5)->get();
        $scheduledStatus = VisitStatus::where('slug', 'scheduled')->first();

        // Locations around Jakarta
        $locations = [
            [-6.21, 106.82], // Sudirman
            [-6.22, 106.80], // Senayan
            [-6.19, 106.83], // Menteng
            [-6.24, 106.78], // Kebayoran
            [-6.18, 106.85], // Cempaka Putih
        ];

        foreach ($patients as $index => $patient) {
            $loc = $locations[$index % count($locations)];

            Visit::create([
                'user_id' => $doctor->id,
                'patient_id' => $patient->id,
                'visit_status_id' => $scheduledStatus->id,
                'scheduled_at' => Carbon::now()->addHours($index + 2), // Spaced out today
                'complaint' => 'Routine checkup and vaccination',
                'latitude' => $loc[0],
                'longitude' => $loc[1],
                'transport_fee' => 50000 + (rand(1, 10) * 5000),
            ]);
        }
        $this->command->info('Visits created.');
    }
}

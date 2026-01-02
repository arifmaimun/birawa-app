<?php

namespace Tests\Feature;

use App\Filament\Widgets\DailyProfitStats;
use App\Models\DoctorInventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DailyProfitStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_calculation()
    {
        // 1. Setup Doctor
        $doctor = User::factory()->create();
        $this->actingAs($doctor);

        // 2. Setup Data
        $client = Client::factory()->create(['user_id' => $doctor->id]); // Client linked to doctor? Or generic user?
        // Actually Client user_id usually refers to the Client's User account.
        // But for Visit, we need patient.
        $clientUser = User::factory()->create();
        $client = \App\Models\Client::create(['user_id' => $clientUser->id, 'name' => 'Client A', 'phone' => '081']);
        $patient = Patient::create(['client_id' => $client->id, 'name' => 'Cat A', 'species' => 'Cat']);

        // 3. Create Visit (Transport Cost)
        // Distance 10km -> Fuel Cost = 10 * 2000 = 20,000
        $visit = Visit::factory()->create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'scheduled_at' => now(),
            'distance_km' => 10,
        ]);

        // 4. Create Invoice (Revenue)
        $invoice = Invoice::create([
            'visit_id' => $visit->id,
            'invoice_number' => 'INV-001',
            'total_amount' => 100000, // Revenue
            'created_at' => now(),
        ]);

        // 5. Create Invoice Items (Medicine Cost)
        // Item 1: Cost 5000, Price 10000, Qty 2 -> Total Cost 10,000
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Med A',
            'quantity' => 2,
            'unit_price' => 10000,
            'unit_cost' => 5000,
        ]);

        // 6. Test Widget
        // Revenue = 100,000
        // COGS = Medicine (10,000) + Transport (20,000) = 30,000
        // Net Profit = 100,000 - 30,000 = 70,000

        Livewire::test(DailyProfitStats::class)
            ->assertSee('Rp 100.000') // Revenue
            ->assertSee('Rp 30.000')  // COGS
            ->assertSee('Rp 70.000'); // Net Profit
    }
}

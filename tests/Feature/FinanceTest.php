<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use App\Models\Invoice;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_view_finance_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('finance.index'));
        $response->assertStatus(200);
    }

    public function test_finance_dashboard_calculates_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Create Income (Paid Invoice)
        $visit = Visit::factory()->create(['user_id' => $user->id]);
        Invoice::create([
            'visit_id' => $visit->id,
            'invoice_number' => 'INV-001',
            'total_amount' => 100000,
            'payment_status' => 'paid',
            'created_at' => Carbon::now(),
        ]);

        // 2. Create Unpaid Invoice (Should be ignored)
        $visit2 = Visit::factory()->create(['user_id' => $user->id]);
        Invoice::create([
            'visit_id' => $visit2->id,
            'invoice_number' => 'INV-002',
            'total_amount' => 50000,
            'payment_status' => 'unpaid',
            'created_at' => Carbon::now(),
        ]);

        // 3. Create Expenses
        Expense::create([
            'user_id' => $user->id,
            'type' => 'OPEX',
            'category' => 'Fuel',
            'amount' => 20000,
            'transaction_date' => Carbon::now(),
        ]);

        Expense::create([
            'user_id' => $user->id,
            'type' => 'CAPEX',
            'category' => 'Equipment',
            'amount' => 500000,
            'transaction_date' => Carbon::now(),
        ]);

        // Access Dashboard
        $response = $this->get(route('finance.index'));

        // Assert View Data
        $response->assertViewHas('income', 100000);
        $response->assertViewHas('opex', 20000);
        $response->assertViewHas('capex', 500000);
        $response->assertViewHas('totalExpenses', 520000);
        $response->assertViewHas('netProfit', 100000 - 520000); // -420000
    }
}

<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

        // 1. Create Income (Paid Invoice via Payment)
        $visit = Visit::factory()->create(['user_id' => $user->id]);
        $invoice = Invoice::create([
            'visit_id' => $visit->id,
            'invoice_number' => 'INV-001',
            'total_amount' => 100000,
            'remaining_balance' => 0,
            'payment_status' => 'paid',
            'created_at' => Carbon::now(),
        ]);
        // Add payment record
        \App\Models\InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'amount' => 100000,
            'method' => 'cash',
            'paid_at' => Carbon::now(),
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

        // 3. Create Partial Invoice with Deposit
        $visit3 = Visit::factory()->create(['user_id' => $user->id]);
        Invoice::create([
            'visit_id' => $visit3->id,
            'invoice_number' => 'INV-003',
            'total_amount' => 200000,
            'deposit_amount' => 50000,
            'remaining_balance' => 150000,
            'payment_status' => 'partial',
            'created_at' => Carbon::now(),
        ]);

        // 4. Create Expenses
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
        // Income = 100000 (Payment) + 50000 (Deposit) = 150000
        $response->assertViewHas('income', 150000);
        $response->assertViewHas('opex', 20000);
        $response->assertViewHas('capex', 500000);
        $response->assertViewHas('totalExpenses', 520000);
        $response->assertViewHas('netProfit', 150000 - 520000); // -370000

        // Assert Recent Payments present
        $response->assertViewHas('recentPayments');
    }
}

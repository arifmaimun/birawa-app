<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_payment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $client = Client::factory()->create(['user_id' => $user->id]);
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        $visit = Visit::factory()->create([
            'user_id' => $user->id,
            'patient_id' => $patient->id
        ]);
        
        $invoice = Invoice::create([
            'visit_id' => $visit->id,
            'invoice_number' => 'INV-TEST',
            'total_amount' => 100000,
            'remaining_balance' => 100000,
            'payment_status' => 'unpaid'
        ]);

        $response = $this->post(route('invoices.payments.store', $invoice), [
            'amount' => 50000,
            'method' => 'cash',
            'notes' => 'Partial payment'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoice_payments', [
            'invoice_id' => $invoice->id,
            'amount' => 50000,
            'method' => 'cash'
        ]);

        $invoice->refresh();
        $this->assertEquals(50000, $invoice->remaining_balance);
        $this->assertEquals('partial', $invoice->payment_status);
    }

    public function test_full_payment_updates_status_to_paid()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $client = Client::factory()->create(['user_id' => $user->id]);
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        $visit = Visit::factory()->create([
            'user_id' => $user->id,
            'patient_id' => $patient->id
        ]);
        
        $invoice = Invoice::create([
            'visit_id' => $visit->id,
            'invoice_number' => 'INV-TEST',
            'total_amount' => 100000,
            'remaining_balance' => 100000,
            'payment_status' => 'unpaid'
        ]);

        $this->post(route('invoices.payments.store', $invoice), [
            'amount' => 100000,
            'method' => 'transfer'
        ]);

        $invoice->refresh();
        $this->assertEquals(0, $invoice->remaining_balance);
        $this->assertEquals('paid', $invoice->payment_status);
    }

    public function test_deposit_reduces_remaining_balance()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $client = Client::factory()->create(['user_id' => $user->id]);
        $patient = Patient::factory()->create(['client_id' => $client->id]);
        $visit = Visit::factory()->create([
            'user_id' => $user->id,
            'patient_id' => $patient->id
        ]);
        
        $invoice = Invoice::create([
            'visit_id' => $visit->id,
            'invoice_number' => 'INV-TEST',
            'total_amount' => 100000,
            'remaining_balance' => 100000,
            'payment_status' => 'unpaid'
        ]);

        $this->put(route('invoices.update', $invoice), [
            'deposit_amount' => 20000
        ]);

        $invoice->refresh();
        $this->assertEquals(20000, $invoice->deposit_amount);
        $this->assertEquals(80000, $invoice->remaining_balance);
        $this->assertEquals('partial', $invoice->payment_status);
    }
}

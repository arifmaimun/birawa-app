<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('deposit_amount', 12, 2)->default(0)->after('total_amount');
            $table->decimal('remaining_balance', 12, 2)->default(0)->after('deposit_amount');
            $table->date('due_date')->nullable()->after('payment_status');
        });

        // 2. Create invoice_payments table for split billing/history
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('method')->default('cash'); // cash, transfer, card, etc.
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['deposit_amount', 'remaining_balance', 'due_date']);
        });
    }
};

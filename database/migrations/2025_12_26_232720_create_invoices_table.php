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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Cashier
            $table->foreignId('visit_id')->nullable()->constrained()->onDelete('cascade'); // Can be null for Walk-in
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('set null'); // Optional patient
            $table->string('invoice_number')->unique()->nullable(); // Make nullable if generated later, or ensure logic generates it
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_status', ['unpaid', 'paid', 'partial'])->default('unpaid');
            $table->timestamps();
            
            // Note: visit_id was unique in original, but for nullable it might be better to remove unique constraint 
            // or use a partial index if we want 1 invoice per visit. 
            // For simplicity, we allow multiple invoices per visit or null.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

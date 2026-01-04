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
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique(); // e.g., TRF-20251228-001
            $table->foreignId('requester_id')->constrained('users');

            // Flexible Source/Target
            $table->string('source_type')->default('central'); // 'central', 'doctor'
            $table->unsignedBigInteger('source_id')->nullable(); // null for central, user_id for doctor

            $table->string('target_type')->default('doctor'); // 'central', 'doctor'
            $table->unsignedBigInteger('target_id')->nullable(); // user_id

            $table->string('status')->default('pending'); // pending, approved, rejected, completed
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transfers');
    }
};

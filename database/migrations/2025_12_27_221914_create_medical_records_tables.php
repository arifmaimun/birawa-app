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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan_treatment')->nullable(); // Visible to client
            $table->text('plan_recipe')->nullable(); // Hidden from client
            $table->boolean('is_locked')->default(true);
            $table->timestamps();
        });

        Schema::create('medical_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_inventory_id')->constrained('doctor_inventories')->onDelete('cascade');
            $table->decimal('quantity_used', 10, 2);
            $table->timestamps();
        });

        Schema::create('access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('target_medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('owner_doctor_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_requests');
        Schema::dropIfExists('medical_usage_logs');
        Schema::dropIfExists('medical_records');
    }
};

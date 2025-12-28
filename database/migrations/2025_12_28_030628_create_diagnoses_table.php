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
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ICD-10 or Custom Code
            $table->string('name');
            $table->string('category')->nullable(); // e.g., Skin, Digestive, etc.
            $table->timestamps();
        });

        // Pivot table for MedicalRecord <-> Diagnosis
        Schema::create('diagnosis_medical_record', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('diagnosis_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis_medical_record');
        Schema::dropIfExists('diagnoses');
    }
};

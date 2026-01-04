<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients_temp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable();
            $table->string('name');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->string('gender')->nullable(); // Allow null/unknown
            $table->date('dob')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_sterile')->nullable()->default(null); // Allow null/unknown
        });

        // Copy data
        DB::statement('INSERT INTO patients_temp (id, client_id, name, species, breed, gender, dob, created_at, updated_at, deleted_at, is_sterile) 
                       SELECT id, client_id, name, species, breed, gender, dob, created_at, updated_at, deleted_at, is_sterile FROM patients');

        Schema::drop('patients');
        Schema::rename('patients_temp', 'patients');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No strict revert needed for this adjustment
    }
};

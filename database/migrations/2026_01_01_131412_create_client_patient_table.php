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
        Schema::create('client_patient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['client_id', 'patient_id']);
        });

        // Migrate existing data
        $patients = \Illuminate\Support\Facades\DB::table('patients')->whereNotNull('client_id')->get();
        $records = [];
        foreach ($patients as $patient) {
            $records[] = [
                'client_id' => $patient->client_id,
                'patient_id' => $patient->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($records)) {
            \Illuminate\Support\Facades\DB::table('client_patient')->insert($records);
        }

        // Make client_id nullable on patients table as we are moving to pivot
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_patient');

        // We cannot strictly revert client_id to not null without data loss checks,
        // but generally we assume we'd reverse the process if needed.
    }
};

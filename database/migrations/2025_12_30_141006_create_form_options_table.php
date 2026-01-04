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
        Schema::create('form_options', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // ethnicity, religion, marital_status, location_type, parking_type
            $table->string('value');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure unique value per category
            $table->unique(['category', 'value']);
        });

        // Seed default values
        $defaults = [
            'religion' => ['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'],
            'marital_status' => ['Belum Menikah', 'Menikah', 'Janda/Duda'],
            'ethnicity' => ['Jawa', 'Sunda', 'Batak', 'Madura', 'Betawi', 'Minangkabau', 'Bugis', 'Melayu', 'Lainnya'],
            'location_type' => ['Rumah', 'Kantor', 'Apartemen', 'Kost', 'Lainnya'],
            'parking_type' => ['Carport', 'Garasi', 'Pinggir Jalan', 'Basement', 'Tidak Ada Parkir'],
        ];

        foreach ($defaults as $category => $values) {
            foreach ($values as $value) {
                DB::table('form_options')->insert([
                    'category' => $category,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_options');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $values = ['KTP', 'Passport', 'SIM', 'KITAS'];
        
        foreach ($values as $value) {
            // Check if exists first to avoid duplicates if re-run
            if (!DB::table('form_options')->where('category', 'id_type')->where('value', $value)->exists()) {
                DB::table('form_options')->insert([
                    'category' => 'id_type',
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
        DB::table('form_options')->where('category', 'id_type')->delete();
    }
};

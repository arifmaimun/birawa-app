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
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->decimal('transport_fee_per_km', 10, 2)->default(0)->after('base_transport_fee');
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->decimal('distance_km', 10, 2)->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn('distance_km');
        });

        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropColumn('transport_fee_per_km');
        });
    }
};

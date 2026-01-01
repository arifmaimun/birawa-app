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
        Schema::table('medical_usage_logs', function (Blueprint $table) {
            $table->foreignId('doctor_inventory_id')->nullable()->change();
            $table->foreignId('doctor_service_catalog_id')->nullable()->constrained('doctor_service_catalogs')->onDelete('cascade')->after('doctor_inventory_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_usage_logs', function (Blueprint $table) {
            $table->foreignId('doctor_inventory_id')->nullable(false)->change();
            $table->dropForeign(['doctor_service_catalog_id']);
            $table->dropColumn('doctor_service_catalog_id');
        });
    }
};

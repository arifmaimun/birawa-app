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
        // 1. Service Bundling Pivot Table
        Schema::create('service_inventory_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_service_catalog_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->nullable(); // e.g., 'pcs', 'ml'
            $table->timestamps();
        });

        // 2. Stock Reservation Logic
        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->decimal('reserved_qty', 10, 2)->default(0)->after('stock_qty');
        });

        // 3. Medical Intelligence (Product Validation)
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('min_dose_per_kg', 10, 4)->nullable()->after('price');
            $table->decimal('max_dose_per_kg', 10, 4)->nullable()->after('min_dose_per_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_inventory_materials');

        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->dropColumn('reserved_qty');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['min_dose_per_kg', 'max_dose_per_kg']);
        });
    }
};

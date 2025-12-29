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
        // 1. Add category to doctor_inventories
        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->string('category')->nullable()->after('item_name');
        });

        // 2. Enhance doctor_service_catalogs
        Schema::table('doctor_service_catalogs', function (Blueprint $table) {
            $table->integer('duration_minutes')->default(30)->after('price');
            $table->string('unit')->default('session')->after('duration_minutes'); // session, hour, pcs
        });

        // 3. Create doctor_inventory_batches
        Schema::create('doctor_inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_inventory_id')->constrained()->onDelete('cascade');
            $table->string('batch_number');
            $table->date('expiry_date')->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_inventory_batches');

        Schema::table('doctor_service_catalogs', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes', 'unit']);
        });

        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};

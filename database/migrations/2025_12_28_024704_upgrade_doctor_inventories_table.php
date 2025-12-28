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
        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->decimal('stock_qty', 10, 2)->change();
            $table->string('base_unit')->nullable()->after('unit'); // tablet, ml, etc.
            $table->string('purchase_unit')->nullable()->after('base_unit'); // box, bottle
            $table->integer('conversion_ratio')->default(1)->after('purchase_unit');
            $table->decimal('average_cost_price', 15, 2)->default(0)->after('conversion_ratio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->dropColumn(['base_unit', 'purchase_unit', 'conversion_ratio', 'average_cost_price']);
        });
    }
};

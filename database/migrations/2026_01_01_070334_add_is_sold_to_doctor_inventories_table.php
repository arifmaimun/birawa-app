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
            $table->boolean('is_sold')->default(true)->after('stock_qty')->comment('Determines if the item is sold to patients and appears on invoices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->dropColumn('is_sold');
        });
    }
};

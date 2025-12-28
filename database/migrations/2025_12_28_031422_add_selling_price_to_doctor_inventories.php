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
            $table->decimal('selling_price', 15, 2)->default(0)->after('average_cost_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->dropColumn('selling_price');
        });
    }
};

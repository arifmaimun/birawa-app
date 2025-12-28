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
        Schema::table('invoice_items', function (Blueprint $table) {
            // Drop foreign key first if it exists
            // We need to know the constraint name, usually invoice_items_product_id_foreign
            $table->dropForeign(['product_id']);
            
            // Make product_id nullable
            $table->unsignedBigInteger('product_id')->nullable()->change();
            
            // Add description
            $table->string('description')->after('id');
            
            // Remove unit_cost if not needed for invoice display, but maybe useful for profit calc. 
            // Keep it but make nullable?
            $table->decimal('unit_cost', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // It's hard to reverse strictly if data violated the FK
            $table->dropColumn('description');
        });
    }
};

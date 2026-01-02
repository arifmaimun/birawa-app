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
            $table->foreignId('doctor_inventory_id')->nullable()->constrained('doctor_inventories')->onDelete('set null')->after('product_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('stock_committed')->default(false)->after('payment_status');
            $table->string('status')->default('draft')->after('stock_committed'); // draft, issued, cancelled
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['doctor_inventory_id']);
            $table->dropColumn('doctor_inventory_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('stock_committed');
            $table->dropColumn('status');
        });
    }
};

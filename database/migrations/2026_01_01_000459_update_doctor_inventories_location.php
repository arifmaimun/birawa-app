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
        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->foreignId('storage_location_id')->nullable()->constrained()->onDelete('cascade')->after('user_id');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null')->after('storage_location_id');
        });

        // Migrate existing data: Create default warehouse for each user and assign inventories
        $userIds = DB::table('doctor_inventories')->distinct()->pluck('user_id');
        foreach ($userIds as $userId) {
            $locationId = DB::table('storage_locations')->insertGetId([
                'user_id' => $userId,
                'name' => 'Main Warehouse',
                'type' => 'warehouse',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('doctor_inventories')
                ->where('user_id', $userId)
                ->update(['storage_location_id' => $locationId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_inventories', function (Blueprint $table) {
            $table->dropForeign(['storage_location_id']);
            $table->dropColumn('storage_location_id');
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};

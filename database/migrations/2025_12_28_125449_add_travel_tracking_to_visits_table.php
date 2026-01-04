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
        Schema::table('visits', function (Blueprint $table) {
            $table->timestamp('departure_time')->nullable();
            $table->timestamp('arrival_time')->nullable();
            $table->integer('estimated_travel_minutes')->nullable();
            $table->integer('actual_travel_minutes')->nullable();
        });

        // Update message_templates type enum to include travel notifications
        // Note: Changing enum in SQLite/MySQL can be tricky, so we might just use 'other' or add a new column if needed.
        // For now, we'll assume the existing 'type' can handle custom types or we stick to 'other'.
        // But better to add a 'slug' or 'trigger' column for system events.

        Schema::table('message_templates', function (Blueprint $table) {
            $table->string('trigger_event')->nullable()->after('type'); // e.g., 'on_departure', 'on_arrival'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->dropColumn('trigger_event');
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'departure_time',
                'arrival_time',
                'estimated_travel_minutes',
                'actual_travel_minutes',
            ]);
        });
    }
};

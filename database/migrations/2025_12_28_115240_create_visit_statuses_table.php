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
        Schema::create('visit_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#6B7280'); // Gray default
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Seed default statuses
        $statuses = [
            [
                'name' => 'Scheduled',
                'slug' => 'scheduled',
                'color' => '#FBBF24', // Amber/Yellow
                'order' => 1,
            ],
            [
                'name' => 'On The Way',
                'slug' => 'otw',
                'color' => '#3B82F6', // Blue
                'order' => 2,
            ],
            [
                'name' => 'Arrived',
                'slug' => 'arrived',
                'color' => '#8B5CF6', // Purple
                'order' => 3,
            ],
            [
                'name' => 'Completed',
                'slug' => 'completed',
                'color' => '#10B981', // Emerald/Green
                'order' => 4,
            ],
            [
                'name' => 'Cancelled',
                'slug' => 'cancelled',
                'color' => '#EF4444', // Red
                'order' => 5,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('visit_statuses')->insert(array_merge($status, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_statuses');
    }
};

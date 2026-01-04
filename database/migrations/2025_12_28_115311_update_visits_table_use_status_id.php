<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Temp Table with new schema
        Schema::create('visits_temp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->datetime('scheduled_at');

            // New column
            $table->foreignId('visit_status_id')->nullable()->constrained('visit_statuses')->nullOnDelete();

            $table->text('complaint')->nullable();
            $table->decimal('transport_fee', 10, 2)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->timestamps();
        });

        // 2. Migrate Data
        $visits = DB::table('visits')->get();
        $statuses = DB::table('visit_statuses')->pluck('id', 'slug');

        foreach ($visits as $visit) {
            // Map old enum status to new status ID
            // Old enum: ['scheduled', 'otw', 'arrived', 'completed', 'cancelled']
            // New slugs match these.

            $statusSlug = $visit->status;
            // Handle potential mismatch if any, default to 'scheduled' or null
            $statusId = $statuses[$statusSlug] ?? $statuses['scheduled'] ?? null;

            DB::table('visits_temp')->insert([
                'id' => $visit->id,
                'patient_id' => $visit->patient_id,
                'user_id' => $visit->user_id,
                'scheduled_at' => $visit->scheduled_at,
                'visit_status_id' => $statusId,
                'complaint' => $visit->complaint,
                'transport_fee' => $visit->transport_fee,
                'latitude' => $visit->latitude,
                'longitude' => $visit->longitude,
                'distance_km' => $visit->distance_km,
                'created_at' => $visit->created_at,
                'updated_at' => $visit->updated_at,
            ]);
        }

        // 3. Drop old table and rename new one
        Schema::drop('visits');
        Schema::rename('visits_temp', 'visits');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert is complex, simplification: create table with enum and copy back
        Schema::create('visits_temp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->datetime('scheduled_at');
            $table->enum('status', ['scheduled', 'otw', 'arrived', 'completed', 'cancelled'])->default('scheduled');
            $table->text('complaint')->nullable();
            $table->decimal('transport_fee', 10, 2)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->timestamps();
        });

        $visits = DB::table('visits')->get();
        // Assuming slugs match enum values
        $statuses = DB::table('visit_statuses')->pluck('slug', 'id');

        foreach ($visits as $visit) {
            $slug = $statuses[$visit->visit_status_id] ?? 'scheduled';

            DB::table('visits_temp')->insert([
                'id' => $visit->id,
                'patient_id' => $visit->patient_id,
                'user_id' => $visit->user_id,
                'scheduled_at' => $visit->scheduled_at,
                'status' => $slug,
                'complaint' => $visit->complaint,
                'transport_fee' => $visit->transport_fee,
                'latitude' => $visit->latitude,
                'longitude' => $visit->longitude,
                'distance_km' => $visit->distance_km,
                'created_at' => $visit->created_at,
                'updated_at' => $visit->updated_at,
            ]);
        }

        Schema::drop('visits');
        Schema::rename('visits_temp', 'visits');
    }
};

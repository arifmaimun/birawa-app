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
        /*
        Schema::table('patients', function (Blueprint $table) {
            // Drop foreign key and column owner_id
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });
        */

        Schema::create('pet_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User acts as owner
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_owners');

        /*
        Schema::table('patients', function (Blueprint $table) {
             // We can't easily restore the data, but we can restore the column
             $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('cascade');
        });
        */
    }
};

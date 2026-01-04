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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            // Business Mode
            $table->boolean('is_business')->default(false);
            $table->string('business_name')->nullable();
            $table->string('contact_person')->nullable(); // For business mode

            // Background Info
            $table->string('id_type')->nullable(); // KTP, Passport, etc.
            $table->string('id_number')->nullable();
            $table->string('gender')->nullable();
            $table->string('occupation')->nullable();
            $table->date('dob')->nullable();
            $table->string('ethnicity')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status')->nullable();
        });

        Schema::create('client_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('street');
            $table->string('additional_info')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('parking_type')->nullable(); // Carport, Basement, etc.
            $table->string('address_type')->nullable(); // Home, Office, etc.
            $table->string('coordinates')->nullable(); // Lat,Long for Google Maps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_addresses');

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name',
                'is_business', 'business_name', 'contact_person',
                'id_type', 'id_number', 'gender', 'occupation',
                'dob', 'ethnicity', 'religion', 'marital_status',
            ]);
        });
    }
};

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
        Schema::table('message_templates', function (Blueprint $table) {
            // Drop the enum column and recreate it as string to allow more flexible types
            // SQLite doesn't support dropping columns easily or modifying enums directly in some versions,
            // but Laravel's schema builder usually handles it or we can just change it.
            // For safety in SQLite (often used in testing), we might need a raw statement or a specific approach.
            // However, let's try standard Laravel modification first.
            
            // If we can't easily modify enum, we can just make it a string.
            // But first, let's add the new column.
            if (!Schema::hasColumn('message_templates', 'trigger_event')) {
                $table->string('trigger_event')->nullable()->after('content_pattern');
            }
        });
        
        // Changing enum to string or expanding it. 
        // Since it's SQLite in tests, modifying columns can be tricky.
        // We will try to modify the type column.
        Schema::table('message_templates', function (Blueprint $table) {
            $table->string('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->dropColumn('trigger_event');
            // Reverting type to enum might be complex if data doesn't fit, so we skip strict revert for type.
        });
    }
};

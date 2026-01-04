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
        // 1. Rename owners to clients
        if (Schema::hasTable('owners') && ! Schema::hasTable('clients')) {
            Schema::rename('owners', 'clients');
        }

        // 2. Update clients table
        Schema::table('clients', function (Blueprint $table) {
            // Add user_id if it doesn't exist (to link to Auth)
            if (! Schema::hasColumn('clients', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
            // Add soft deletes
            if (! Schema::hasColumn('clients', 'deleted_at')) {
                $table->softDeletes();
            }
            // Add indexes
            $table->index(['name', 'phone']);
        });

        // 3. Update patients table
        Schema::table('patients', function (Blueprint $table) {
            // Add client_id (Renaming owner_id if it existed, but it was dropped, so we add it)
            if (! Schema::hasColumn('patients', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->onDelete('cascade');
            }

            // Add soft deletes
            if (! Schema::hasColumn('patients', 'deleted_at')) {
                $table->softDeletes();
            }

            // Add indexes
            $table->index(['name', 'client_id']);
        });

        // 4. Update visits table
        Schema::table('visits', function (Blueprint $table) {
            // Add soft deletes
            if (! Schema::hasColumn('visits', 'deleted_at')) {
                $table->softDeletes();
            }

            // Add indexes
            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['status', 'scheduled_at']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
            $table->dropIndex(['name', 'client_id']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropIndex(['name', 'phone']);
        });

        if (Schema::hasTable('clients') && ! Schema::hasTable('owners')) {
            Schema::rename('clients', 'owners');
        }
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            // Add index on locked column for faster filtering of locked users
            $table->index('locked', 'idx_users_locked');
            
            // Add index on usertype for admin filtering
            $table->index('usertype', 'idx_users_usertype');
            
            // Add composite index on locked and failed_attempts for unlock operations
            $table->index(['locked', 'failed_attempts'], 'idx_users_locked_attempts');
            
            // Add index on last_failed_attempt for cleanup operations
            $table->index('last_failed_attempt', 'idx_users_last_failed_attempt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the indexes in reverse order
            $table->dropIndex('idx_users_last_failed_attempt');
            $table->dropIndex('idx_users_locked_attempts');
            $table->dropIndex('idx_users_usertype');
            $table->dropIndex('idx_users_locked');
        });
    }
};

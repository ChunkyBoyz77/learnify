<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change enrollment_id foreign key from 'set null' to 'cascade' delete.
     * This will delete payments when their associated enrollment is deleted.
     */
    public function up(): void
    {
        // Drop the existing foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
        });

        // Re-add the foreign key with cascade delete
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('enrollment_id')
                  ->references('id')
                  ->on('enrollments')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Restore the original 'set null' behavior.
     */
    public function down(): void
    {
        // Drop the cascade foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
        });

        // Re-add the foreign key with set null (original behavior)
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('enrollment_id')
                  ->references('id')
                  ->on('enrollments')
                  ->onDelete('set null');
        });
    }
};

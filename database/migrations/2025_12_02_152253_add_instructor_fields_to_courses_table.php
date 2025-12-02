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
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_published')->default(false)->after('is_active');
            $table->integer('enrollment_count')->default(0)->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn(['instructor_id', 'is_published', 'enrollment_count']);
        });
    }
};

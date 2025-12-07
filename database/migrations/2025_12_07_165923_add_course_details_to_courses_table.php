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
            if (!Schema::hasColumn('courses', 'what_you_will_learn')) {
                $table->text('what_you_will_learn')->nullable()->after('description');
            }
            if (!Schema::hasColumn('courses', 'skills_gain')) {
                $table->text('skills_gain')->nullable()->after('what_you_will_learn');
            }
            if (!Schema::hasColumn('courses', 'assessment_info')) {
                $table->text('assessment_info')->nullable()->after('skills_gain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'what_you_will_learn')) {
                $table->dropColumn('what_you_will_learn');
            }
            if (Schema::hasColumn('courses', 'skills_gain')) {
                $table->dropColumn('skills_gain');
            }
            if (Schema::hasColumn('courses', 'assessment_info')) {
                $table->dropColumn('assessment_info');
            }
        });
    }
};

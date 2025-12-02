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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('time_limit_minutes')->nullable(); // Time limit for quiz
            $table->integer('total_questions')->default(0);
            $table->decimal('passing_score', 5, 2)->nullable(); // Percentage required to pass
            $table->integer('max_attempts')->default(1); // Maximum attempts allowed
            $table->boolean('is_published')->default(false);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->timestamps();
            
            $table->index(['course_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};

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
        Schema::create('quiz_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->json('answers'); // Store all answers: {question_id: answer}
            $table->decimal('score', 5, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('is_passed')->default(false);
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_taken_seconds')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
            $table->text('feedback')->nullable(); // Instructor feedback
            $table->timestamps();
            
            $table->index(['user_id', 'quiz_id']);
            $table->index(['quiz_id', 'user_id', 'attempt_number'], 'quiz_user_attempt_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_submissions');
    }
};

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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Student who gave feedback
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('cascade'); // Instructor being reviewed
            $table->text('comment'); // Feedback comment/suggestion
            $table->integer('rating')->nullable(); // Rating 1-5 (optional)
            $table->enum('feedback_type', ['lesson', 'instructor', 'course', 'general'])->default('general');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('instructor_response')->nullable(); // Instructor's response to feedback
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
            
            $table->index(['course_id', 'user_id']);
            $table->index(['instructor_id', 'status']);
            $table->index(['lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};

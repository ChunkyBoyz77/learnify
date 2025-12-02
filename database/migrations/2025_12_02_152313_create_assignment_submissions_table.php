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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->text('submission_text')->nullable(); // Text submission
            $table->json('submitted_files')->nullable(); // Array of file paths
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->integer('submission_number')->default(1);
            $table->enum('status', ['submitted', 'graded', 'returned', 'resubmitted'])->default('submitted');
            $table->text('feedback')->nullable(); // Instructor feedback
            $table->text('instructor_notes')->nullable();
            $table->boolean('is_late')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'assignment_id']);
            $table->index(['assignment_id', 'user_id', 'submission_number'], 'asgn_sub_user_subnum_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};

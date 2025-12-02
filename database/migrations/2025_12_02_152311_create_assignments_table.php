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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->decimal('total_points', 5, 2)->default(100.00);
            $table->timestamp('due_date')->nullable();
            $table->boolean('allows_late_submission')->default(false);
            $table->integer('max_submissions')->default(1);
            $table->enum('submission_type', ['file', 'text', 'both'])->default('file');
            $table->json('allowed_file_types')->nullable(); // ['pdf', 'doc', 'docx']
            $table->integer('max_file_size_mb')->nullable();
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
        Schema::dropIfExists('assignments');
    }
};

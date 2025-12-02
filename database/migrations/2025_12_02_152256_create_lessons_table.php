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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable(); // Lesson content/notes
            $table->string('video_url')->nullable(); // Video link if any
            $table->integer('order')->default(0); // Order within course
            $table->integer('duration_minutes')->nullable(); // Lesson duration
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            
            $table->index(['course_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};

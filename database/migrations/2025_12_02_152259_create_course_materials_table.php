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
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['file', 'video', 'link', 'document', 'other'])->default('file');
            $table->string('file_path')->nullable(); // Path to uploaded file
            $table->string('file_name')->nullable(); // Original file name
            $table->string('file_size')->nullable(); // File size in bytes
            $table->string('mime_type')->nullable(); // File MIME type
            $table->string('external_url')->nullable(); // For links
            $table->integer('order')->default(0);
            $table->boolean('is_downloadable')->default(true);
            $table->timestamps();
            
            $table->index(['course_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};

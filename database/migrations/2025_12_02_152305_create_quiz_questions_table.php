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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
            $table->json('options')->nullable(); // For multiple choice: ["option1", "option2", ...]
            $table->json('correct_answer')->nullable(); // Can be array for multiple correct answers
            $table->text('explanation')->nullable(); // Explanation shown after answering
            $table->decimal('points', 5, 2)->default(1.00); // Points for this question
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['quiz_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};

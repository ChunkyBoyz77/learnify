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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_type', ['enrollment', 'subscription', 'other'])->default('enrollment');
            $table->text('payment_details')->nullable(); // JSON field for payment gateway response
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

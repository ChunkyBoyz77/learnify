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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // unauthorized_access_attempt, encryption_verification_failed, non_secure_connection, suspicious_activity, etc.
            $table->enum('severity', ['info', 'low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('description');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            
            $table->index(['event_type', 'occurred_at']);
            $table->index(['severity', 'occurred_at']);
            $table->index('payment_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};

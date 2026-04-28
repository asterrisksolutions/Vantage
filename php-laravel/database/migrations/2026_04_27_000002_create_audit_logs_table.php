<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create audit_logs table
 *
 * Tracks all security-relevant events in the system including:
 * - Password reset requests and completions
 * - Password changes
 * - Login attempts (success/failure)
 * - Account lock/unlock events
 * - Admin actions
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates audit_logs table with:
     * - user_id: Link to user who performed the action (nullable for system events)
     * - target_user_id: Link to user affected by the action (nullable)
     * - event_type: Category of event (password_reset, password_change, login, etc.)
     * - description: Human-readable description of the event
     * - ip_address: Client IP where the request originated
     * - user_agent: Browser/client information
     * - metadata: JSON field for additional event-specific data
     * - created_at: When the event occurred
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('event_type', 50);  // password_reset_request, password_reset_complete, password_change, login_success, login_failed, account_locked, account_unlocked
            $table->string('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Index for efficient querying of audit logs
            $table->index(['user_id', 'created_at']);
            $table->index(['target_user_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
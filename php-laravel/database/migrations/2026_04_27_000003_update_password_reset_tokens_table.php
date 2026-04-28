<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Update password_reset_tokens table
 *
 * Adds additional columns to support both token and OTP methods
 * for password reset functionality.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Add method column if it doesn't exist
            if (!Schema::hasColumn('password_reset_tokens', 'method')) {
                $table->enum('method', ['token', 'otp'])->default('token')->after('token');
            }
            
            // Add expires_at column if it doesn't exist
            if (!Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->after('method');
            }
            
            // Add used_at column if it doesn't exist
            if (!Schema::hasColumn('password_reset_tokens', 'used_at')) {
                $table->timestamp('used_at')->nullable()->after('expires_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn(['method', 'expires_at', 'used_at']);
        });
    }
};
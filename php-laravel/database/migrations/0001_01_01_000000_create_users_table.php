<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create users, password_reset_tokens, and sessions tables
 *
 * This is the core user schema for the VANTAGE authentication system.
 * Includes security fields for tracking login attempts and account status.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates three tables:
     * 1. users           - Main user accounts with role-based access
     * 2. password_reset_tokens - Laravel's built-in password reset system
     * 3. sessions        - Laravel's database session storage
     */
    public function up(): void
    {
        // ============================================================
        // USERS TABLE
        // ============================================================
        // Stores all user accounts with authentication and security fields.
        // Links to the roles table via role_id foreign key.
        Schema::create('users', function (Blueprint $table) {
            $table->id();                          // Primary key (auto-increment)
            $table->string('name');                // Full display name
            $table->string('email')->unique();     // Login identifier, must be unique
            $table->string('password');            // Bcrypt hashed password

            // Foreign key linking to roles table
            // onDelete('cascade') removes users if their role is deleted
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');

            $table->string('profile_image')->nullable(); // Path to avatar image (optional)
            $table->integer('failed_attempts')->default(0); // Count of consecutive failed logins
            $table->boolean('is_locked')->default(false);   // Account lock status
            $table->timestamp('last_login_at')->nullable(); // Timestamp of last successful login
            $table->timestamps();                  // created_at and updated_at
        });

        // ============================================================
        // PASSWORD RESET TOKENS TABLE
        // ============================================================
        // Laravel's default table for password reset functionality.
        // Extended to support both token and OTP methods.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();    // Email address requesting reset
            $table->string('token');               // Secure random token or OTP
            $table->enum('method', ['token', 'otp'])->default('token'); // Reset method
            $table->timestamp('expires_at');       // Token/OTP expiration time
            $table->timestamp('used_at')->nullable(); // When token was used
            $table->timestamp('created_at')->nullable(); // Token creation time (for expiry)
        });

        // ============================================================
        // SESSIONS TABLE
        // ============================================================
        // Laravel's database session driver storage.
        // Tracks active browser sessions for authenticated users.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();       // Session ID (unique string)
            $table->foreignId('user_id')->nullable()->index(); // Linked user (nullable for guests)
            $table->string('ip_address', 45)->nullable();      // Client IP address
            $table->text('user_agent')->nullable();            // Browser user agent string
            $table->longText('payload');           // Serialized session data
            $table->integer('last_activity')->index(); // Unix timestamp of last activity
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops all three tables in reverse dependency order.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};


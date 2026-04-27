<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create the roles table
 *
 * Roles define permission levels in the VANTAGE system.
 * This table must be created BEFORE the users table
 * because users.reference role_id as a foreign key.
 *
 * Default roles seeded by DatabaseSeeder:
 *   - Admin: Full system access
 *   - Manager: Limited administrative access
 *   - User: Standard basic access
 */
return new class extends Migration
{
    /**
     * Run the migration - creates the roles table.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();                      // Auto-incrementing primary key
            $table->string('name')->unique();  // Role name: "Admin", "Manager", "User"
            $table->string('description')->nullable(); // Human-readable description
            $table->timestamps();              // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migration - drops the roles table.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};


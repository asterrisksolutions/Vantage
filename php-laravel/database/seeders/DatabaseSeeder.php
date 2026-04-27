<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * Seeds the database with default roles and test user accounts.
 *
 * Run this seeder with:
 *   php artisan db:seed
 * Or with a fresh migration:
 *   php artisan migrate:fresh --seed
 *
 * IMPORTANT: Do NOT wrap passwords in bcrypt() here.
 * The User model has a 'password' => 'hashed' cast
 * that automatically hashes passwords when saving to the database.
 * Manually calling bcrypt() would result in double-hashed passwords
 * and authentication would always fail.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Creates three roles and three corresponding test users.
     * Each user is assigned to a role via role_id foreign key.
     */
    public function run(): void
    {
        // ============================================================
        // STEP 1: Create the three default roles
        // ============================================================
        // Roles define permission levels in the VANTAGE system.
        // The role IDs auto-increment starting from 1.

        $adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator with full system access',
        ]);

        $managerRole = Role::create([
            'name' => 'Manager',
            'description' => 'Manager with limited administrative access',
        ]);

        $userRole = Role::create([
            'name' => 'User',
            'description' => 'Standard user account',
        ]);

        // ============================================================
        // STEP 2: Create test users with plain-text passwords
        // ============================================================
        // Passwords are passed as plain strings.
        // The User model's 'hashed' cast automatically applies bcrypt()
        // when the record is saved to the database.
        //
        // Login credentials for development:
        //   admin@example.com / AdminPassword123
        //   manager@example.com / ManagerPassword123
        //   user@example.com / UserPassword123

        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => 'AdminPassword123',   // Auto-hashed by User model cast
            'role_id' => $adminRole->id,         // Links to Admin role
            'profile_image' => null,
            'failed_attempts' => 0,
            'is_locked' => false,
        ]);

        User::create([
            'name' => 'Test Manager',
            'email' => 'manager@example.com',
            'password' => 'ManagerPassword123', // Auto-hashed by User model cast
            'role_id' => $managerRole->id,       // Links to Manager role
            'profile_image' => null,
            'failed_attempts' => 0,
            'is_locked' => false,
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'UserPassword123',     // Auto-hashed by User model cast
            'role_id' => $userRole->id,          // Links to User role
            'profile_image' => null,
            'failed_attempts' => 0,
            'is_locked' => false,
        ]);
    }
}


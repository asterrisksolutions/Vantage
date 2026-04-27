# Test Accounts Documentation

This document provides a list of all test accounts available in the application for development and testing purposes.

## Database Setup
- **Last Updated**: 2026-04-27
- **Database**: Fresh migration with seed data
- **Tables**: Users, Roles, Cache, Jobs, and related system tables

---

## Available Roles

| Role ID | Role Name | Description |
|---------|-----------|-------------|
| 1 | Admin | Administrator with full system access |
| 2 | Manager | Manager with limited administrative access |
| 3 | User | Standard user account |

---

## Test Accounts

### 1. Administrator Account
- **Email**: `admin@example.com`
- **Password**: `AdminPassword123`
- **Name**: Administrator
- **Role**: Admin (ID: 1)
- **Description**: Full system access, can manage users, roles, and all system configurations
- **Account Status**: Active (not locked)

### 2. Manager Account
- **Email**: `manager@example.com`
- **Password**: `ManagerPassword123`
- **Name**: Test Manager
- **Role**: Manager (ID: 2)
- **Description**: Limited administrative access, can manage team members and reports
- **Account Status**: Active (not locked)

### 3. Standard User Account
- **Email**: `user@example.com`
- **Password**: `UserPassword123`
- **Name**: Test User
- **Role**: User (ID: 3)
- **Description**: Standard user with basic application access
- **Account Status**: Active (not locked)

---

## User Table Columns Reference

Each user account includes the following fields:

| Column | Type | Description |
|--------|------|-------------|
| id | Integer | Primary key |
| name | String | User's full name |
| email | String | User's email (unique) |
| password | String | Hashed password |
| role_id | Integer | Foreign key to roles table |
| profile_image | String | Nullable path to profile image |
| failed_attempts | Integer | Login attempt counter (default: 0) |
| is_locked | Boolean | Account lock status (default: false) |
| last_login_at | Timestamp | Last successful login timestamp (nullable) |
| created_at | Timestamp | Account creation timestamp |
| updated_at | Timestamp | Last account update timestamp |

---

## Quick Reference

### Login Credentials

```
Admin:
  Email: admin@example.com
  Pass: AdminPassword123

Manager:
  Email: manager@example.com
  Pass: ManagerPassword123

User:
  Email: user@example.com
  Pass: UserPassword123
```

---

## Notes

- All passwords are hashed in the database using Laravel's bcrypt hashing
- Test accounts are created automatically via the `DatabaseSeeder` when running `php artisan db:seed`
- The `failed_attempts` field can be used to track failed login attempts
- The `is_locked` field can be used to lock/unlock accounts
- The `last_login_at` field is automatically updated on successful login
- To reset the database with fresh test data, run: `php artisan migrate:fresh --seed`

---

## Commands Reference

```bash
# Run fresh migration with seed data
php artisan migrate:fresh --seed

# Only run migrations
php artisan migrate:fresh

# Only seed database
php artisan db:seed

# Create a new test user manually (using Tinker)
php artisan tinker
>>> User::create(['name' => 'Test', 'email' => 'test@example.com', 'password' => bcrypt('password'), 'role_id' => 3])
```

---

**Created**: April 27, 2026  
**Environment**: Development  
**Status**: Ready for Testing

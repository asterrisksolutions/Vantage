<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordResetNotification;
use App\Notifications\PasswordResetOtp;

/**
 * User Model
 *
 * Represents an authenticated user in the VANTAGE system.
 * Extends Laravel's Authenticatable to provide built-in auth features.
 *
 * Each user belongs to a single Role (Admin, Manager, or User)
 * and tracks login security metrics like failed attempts and lock status.
 */
#[Fillable([
    // Fields that can be mass-assigned via User::create() or update()
    'name',
    'email',
    'password',
    'role_id',
    'profile_image',
    'failed_attempts',
    'is_locked',
    'last_login_at',
])]
#[Hidden(['password'])]
// Password is hidden from JSON/array serialization for security
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Send a password reset notification using token method.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $expiresIn = config('auth.passwords.users.expire', 15);
        $this->notify(new PasswordResetNotification($token, $expiresIn));
    }

    /**
     * Send a password reset notification using OTP method.
     *
     * @param string $otp
     * @return void
     */
    public function sendPasswordResetOtp(string $otp): void
    {
        $expiresIn = config('auth.passwords.users.expire', 15);
        $this->notify(new PasswordResetOtp($otp, $expiresIn));
    }

    /**
     * Define the relationship between User and Role.
     *
     * A user belongs to exactly one role.
     * This allows accessing the role like: $user->role->name
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Define data type casts for model attributes.
     *
     * - password: Automatically hashed via bcrypt on save
     * - is_locked: Cast to boolean (true/false)
     * - last_login_at: Cast to Carbon datetime instance
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',      // Laravel auto-hashes on assignment
            'is_locked' => 'boolean',    // Ensures strict boolean values
            'last_login_at' => 'datetime', // Parsed as Carbon date object
        ];
    }
}


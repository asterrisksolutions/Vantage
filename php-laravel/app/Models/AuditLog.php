<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AuditLog Model
 *
 * Tracks all security-relevant events in the system.
 * Used for compliance, security monitoring, and troubleshooting.
 *
 * Event types:
 * - password_reset_request: User requested password reset
 * - password_reset_complete: Password was successfully reset
 * - password_reset_failed: Password reset failed (invalid/expired token)
 * - password_change: User changed their password directly
 * - admin_password_reset: Admin reset a user's password
 * - login_success: Successful login
 * - login_failed: Failed login attempt
 * - account_locked: Account was locked due to failed attempts
 * - account_unlocked: Account was unlocked by admin or self-service
 */
class AuditLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'audit_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'target_user_id',
        'event_type',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Event type constants
     */
    public const EVENT_PASSWORD_RESET_REQUEST = 'password_reset_request';
    public const EVENT_PASSWORD_RESET_COMPLETE = 'password_reset_complete';
    public const EVENT_PASSWORD_RESET_FAILED = 'password_reset_failed';
    public const EVENT_PASSWORD_CHANGE = 'password_change';
    public const EVENT_ADMIN_PASSWORD_RESET = 'admin_password_reset';
    public const EVENT_LOGIN_SUCCESS = 'login_success';
    public const EVENT_LOGIN_FAILED = 'login_failed';
    public const EVENT_ACCOUNT_LOCKED = 'account_locked';
    public const EVENT_ACCOUNT_UNLOCKED = 'account_unlocked';

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the target user (if the action was performed on another user).
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Log an event to the audit log.
     *
     * @param string $eventType The type of event
     * @param string $description Human-readable description
     * @param int|null $userId The user who performed the action
     * @param int|null $targetUserId The user affected by the action
     * @param array|null $metadata Additional event-specific data
     * @return self
     */
    public static function log(
        string $eventType,
        string $description,
        ?int $userId = null,
        ?int $targetUserId = null,
        ?array $metadata = null
    ): self {
        $request = request();

        return self::create([
            'user_id' => $userId,
            'target_user_id' => $targetUserId,
            'event_type' => $eventType,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metadata' => $metadata,
        ]);
    }
}
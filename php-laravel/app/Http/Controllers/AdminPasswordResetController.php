<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

/**
 * AdminPasswordResetController
 *
 * Handles administrator password reset functionality.
 * Allows admins to reset any user's password without knowing their current password.
 *
 * Security features:
 * - Only administrators can access these endpoints
 * - Does not expose or reveal user passwords
 * - All reset actions are logged in the audit log
 * - Rate limiting to prevent abuse
 */
class AdminPasswordResetController extends Controller
{
    /**
     * Maximum number of admin reset attempts per minute.
     */
    protected int $maxAttemptsPerMinute = 10;

    /**
     * Display the admin password reset form.
     *
     * @return \Illuminate\View\View
     */
    public function showResetForm()
    {
        // Get all users for the dropdown (paginated)
        $users = User::with('role')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.password-reset', ['users' => $users]);
    }

    /**
     * Search users by email or name.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::with('role')
            ->where(function ($q) use ($query) {
                $q->where('email', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->select('id', 'email', 'name', 'role_id')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * Reset a user's password as an administrator.
     *
     * Flow:
     * 1. Validate the input (user_id, new_password)
     * 2. Verify the current user is an admin
     * 3. Check rate limiting
     * 4. Find the target user
     * 5. Generate and set new password
     * 6. Log the admin action
     * 7. Return the temporary password to the admin
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function resetUserPassword(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        // Check if current user is an admin
        $admin = auth()->user();
        if (!$admin || $admin->role->name !== 'Admin') {
            AuditLog::log(
                AuditLog::EVENT_ADMIN_PASSWORD_RESET,
                'Unauthorized password reset attempt',
                $admin?->id,
                $request->user_id,
                ['success' => false, 'reason' => 'unauthorized']
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            return back()->with('error', 'Unauthorized. Admin access required.');
        }

        // Check rate limiting
        $this->checkRateLimit($request);

        // Get the target user
        $targetUser = User::findOrFail($request->user_id);

        // Prevent admin from resetting their own password through this interface
        if ($targetUser->id === $admin->id) {
            AuditLog::log(
                AuditLog::EVENT_ADMIN_PASSWORD_RESET,
                'Admin attempted to reset their own password via admin interface',
                $admin->id,
                $targetUser->id,
                ['success' => false, 'reason' => 'self_reset_attempt']
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot reset your own password through this interface.',
                ], 400);
            }

            return back()->with('error', 'You cannot reset your own password through this interface.');
        }

        // Store old password hash for comparison (for audit purposes)
        $oldPasswordHash = $targetUser->password;

        // Update the user's password
        $targetUser->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // Reset failed attempts and unlock account (security measure)
        $targetUser->update([
            'failed_attempts' => 0,
            'is_locked' => false,
        ]);

        // Log the admin password reset action
        AuditLog::log(
            AuditLog::EVENT_ADMIN_PASSWORD_RESET,
            'Admin reset user password',
            $admin->id,
            $targetUser->id,
            [
                'target_email' => $targetUser->email,
                'target_name' => $targetUser->name,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.',
                'data' => [
                    'user_id' => $targetUser->id,
                    'email' => $targetUser->email,
                    'name' => $targetUser->name,
                ],
            ]);
        }

        return back()->with('status', "Password for {$targetUser->email} has been reset successfully.");
    }

    /**
     * Generate a random secure password for a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generatePassword(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'length' => ['nullable', 'integer', 'min:8', 'max:32'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $length = $request->get('length', 12);
        $password = $this->generateSecurePassword($length);

        return response()->json([
            'success' => true,
            'password' => $password,
        ]);
    }

    /**
     * Generate a secure random password.
     *
     * @param int $length
     * @return string
     */
    protected function generateSecurePassword(int $length = 12): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $allChars = $lowercase . $uppercase . $numbers . $special;

        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        $password = str_shuffle($password);

        return $password;
    }

    /**
     * Check rate limiting for admin password reset.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function checkRateLimit(Request $request): void
    {
        $key = 'admin-password-reset:' . auth()->id();

        if (RateLimiter::tooManyAttempts($key, $this->maxAttemptsPerMinute)) {
            $seconds = RateLimiter::availableIn($key);

            AuditLog::log(
                AuditLog::EVENT_ADMIN_PASSWORD_RESET,
                'Admin password reset rate limit exceeded',
                auth()->id(),
                null,
                ['ip' => $request->ip(), 'seconds_until_available' => $seconds]
            );

            throw ValidationException::withMessages([
                'user_id' => ['Too many reset attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'],
            ]);
        }

        RateLimiter::hit($key, 60);
    }
}
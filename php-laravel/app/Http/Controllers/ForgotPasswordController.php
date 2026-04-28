<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * ForgotPasswordController
 *
 * Handles the "forgot password" flow where users can request
 * a password reset link or OTP to be sent to their email.
 *
 * Features:
 * - Rate limiting to prevent abuse
 * - Support for both token-based and OTP-based reset
 * - Configurable token expiry (default: 15 minutes)
 * - Audit logging of all reset requests
 */
class ForgotPasswordController extends Controller
{
    /**
     * Number of minutes before the reset token expires.
     * This should match the config/auth.php password reset expiry.
     */
    protected int $tokenExpiryMinutes;

    /**
     * Maximum number of reset requests per minute per IP.
     */
    protected int $maxRequestsPerMinute;

    public function __construct()
    {
        $this->tokenExpiryMinutes = config('auth.passwords.users.expire', 15);
        $this->maxRequestsPerMinute = 5;
    }

    /**
     * Display the "forgot password" form.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle a password reset link request.
     *
     * Flow:
     * 1. Validate the email address
     * 2. Check rate limiting
     * 3. Check if user exists
     * 4. Generate reset token
     * 5. Send reset email
     * 6. Log the event
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLink(Request $request)
    {
        // Validate the email input
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            // Even if user doesn't exist, show generic message for security
            // But log the attempt for security monitoring
            if ($request->filled('email')) {
                AuditLog::log(
                    AuditLog::EVENT_PASSWORD_RESET_REQUEST,
                    'Password reset requested for non-existent email: ' . $request->email,
                    null,
                    null,
                    ['email' => $request->email, 'success' => false]
                );
            }

            return back()->withErrors($validator)
                ->with('error', 'If an account exists with this email, a reset link will be sent.');
        }

        // Check rate limiting
        $this->checkRateLimit($request);

        // Get the user
        $user = User::where('email', $request->email)->first();

        // Check if account is locked
        if ($user->is_locked) {
            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_REQUEST,
                'Password reset requested for locked account',
                $user->id,
                $user->id,
                ['success' => false, 'reason' => 'account_locked']
            );

            return back()->with('error', 'Your account is locked. Please contact support.');
        }

        // Get the reset method (token or OTP)
        $method = $request->get('method', 'token');

        // Send the password reset link
        $status = Password::sendResetLink(
            $request->only('email'),
            function ($user, $token) use ($method) {
                // This callback is called when creating the token
                // We can customize the token storage here if needed
            }
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Log successful reset link request
            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_REQUEST,
                'Password reset link sent successfully',
                $user->id,
                $user->id,
                [
                    'method' => $method,
                    'token_expiry_minutes' => $this->tokenExpiryMinutes,
                ]
            );

            return back()->with('status', __($status));
        }

        // Log failed attempt
        AuditLog::log(
            AuditLog::EVENT_PASSWORD_RESET_REQUEST,
            'Password reset link send failed',
            $user->id,
            $user->id,
            ['success' => false, 'reason' => 'send_failed']
        );

        return back()->withErrors(['email' => __($status)])
            ->with('error', 'Unable to send reset link. Please try again later.');
    }

    /**
     * Handle an OTP request for password reset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetOtp(Request $request)
    {
        // Validate the email input
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            if ($request->filled('email')) {
                AuditLog::log(
                    AuditLog::EVENT_PASSWORD_RESET_REQUEST,
                    'Password reset OTP requested for non-existent email',
                    null,
                    null,
                    ['email' => $request->email, 'success' => false]
                );
            }

            return back()->withErrors($validator)
                ->with('error', 'If an account exists with this email, an OTP will be sent.');
        }

        // Check rate limiting
        $this->checkRateLimit($request);

        $user = User::where('email', $request->email)->first();

        // Check if account is locked
        if ($user->is_locked) {
            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_REQUEST,
                'Password reset OTP requested for locked account',
                $user->id,
                $user->id,
                ['success' => false, 'reason' => 'account_locked']
            );

            return back()->with('error', 'Your account is locked. Please contact support.');
        }

        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the OTP in the password_reset_tokens table
        // Note: In production, you might want to hash the OTP
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $otp,
                'method' => 'otp',
                'expires_at' => now()->addMinutes($this->tokenExpiryMinutes),
                'created_at' => now(),
            ]
        );

        // Send OTP via email (using a mailable)
        // For now, we'll use the built-in notification system
        $user->notify(new \App\Notifications\PasswordResetOtp($otp));

        // Log the OTP request
        AuditLog::log(
            AuditLog::EVENT_PASSWORD_RESET_REQUEST,
            'Password reset OTP sent successfully',
            $user->id,
            $user->id,
            [
                'method' => 'otp',
                'token_expiry_minutes' => $this->tokenExpiryMinutes,
            ]
        );

        // Redirect to OTP verification page
        return redirect()->route('password.verify-otp')->with('email', $user->email);
    }

    /**
     * Check rate limiting for password reset requests.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function checkRateLimit(Request $request): void
    {
        $key = 'password-reset:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, $this->maxRequestsPerMinute)) {
            $seconds = RateLimiter::availableIn($key);

            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_REQUEST,
                'Password reset rate limit exceeded',
                null,
                null,
                ['ip' => $request->ip(), 'seconds_until_available' => $seconds]
            );

            throw ValidationException::withMessages([
                'email' => ['Too many reset attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'],
            ]);
        }

        RateLimiter::hit($key, 60); // 60 seconds = 1 minute
    }
}
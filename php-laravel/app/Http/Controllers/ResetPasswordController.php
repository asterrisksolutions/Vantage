<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

/**
 * ResetPasswordController
 *
 * Handles the password reset process after a user has received
 * a reset link or OTP via email.
 *
 * Features:
 * - Token-based and OTP-based reset support
 * - Password strength validation
 * - Rate limiting to prevent brute force
 * - Audit logging of all reset attempts
 */
class ResetPasswordController extends Controller
{
    /**
     * Number of minutes before the reset token expires.
     */
    protected int $tokenExpiryMinutes;

    /**
     * Maximum number of reset attempts per minute per IP.
     */
    protected int $maxAttemptsPerMinute;

    public function __construct()
    {
        $this->tokenExpiryMinutes = config('auth.passwords.users.expire', 15);
        $this->maxAttemptsPerMinute = 5;
    }

    /**
     * Display the password reset form.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        return view('auth.reset-password', [
            'email' => $email,
            'token' => $token,
        ]);
    }

    /**
     * Display the OTP verification form.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showVerifyOtpForm(Request $request)
    {
        $email = $request->query('email', session('email'));

        if (!$email) {
            return redirect()->route('password.request')
                ->with('error', 'Please provide your email address first.');
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    /**
     * Handle a password reset request using a token.
     *
     * Flow:
     * 1. Validate the input (email, token, password)
     * 2. Check rate limiting
     * 3. Find the user by email
     * 4. Verify the token is valid and not expired
     * 5. Update the user's password
     * 6. Log the successful reset
     * 7. Redirect to login with success message
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check rate limiting
        $this->checkResetRateLimit($request);

        // Get the user
        $user = User::where('email', $request->email)->first();

        // Attempt to reset the password
        $status = Password::reset(
            $request->only('token', 'email', 'password', 'password_confirmation'),
            function ($user) use ($request) {
                // Update the user's password
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();

                // Log the password change
                AuditLog::log(
                    AuditLog::EVENT_PASSWORD_RESET_COMPLETE,
                    'Password reset completed successfully via token',
                    $user->id,
                    $user->id,
                    ['method' => 'token']
                );
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Clear any rate limiting on success
            RateLimiter::clear($this->getRateLimitKey($request));

            return redirect('/')->with('status', 'Password has been reset successfully. Please log in with your new password.');
        }

        // Log failed reset attempt
        AuditLog::log(
            AuditLog::EVENT_PASSWORD_RESET_FAILED,
            'Password reset failed - invalid token or email',
            null,
            $user?->id,
            [
                'method' => 'token',
                'reason' => $status,
            ]
        );

        // Clear rate limiting on invalid token
        if ($status === Password::INVALID_TOKEN || $status === Password::INVALID_USER) {
            RateLimiter::clear($this->getRateLimitKey($request));
        }

        return back()->withErrors(['email' => __($status)])
            ->withInput();
    }

    /**
     * Handle OTP verification and password reset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtpAndReset(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check rate limiting
        $this->checkResetRateLimit($request);

        // Get the stored OTP record
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('method', 'otp')
            ->first();

        // Check if OTP record exists
        if (!$resetRecord) {
            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_FAILED,
                'Password reset OTP verification failed - no OTP record found',
                null,
                null,
                ['method' => 'otp', 'reason' => 'no_record']
            );

            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please request a new one.'])
                ->withInput();
        }

        // Check if OTP has already been used
        if ($resetRecord->used_at) {
            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_FAILED,
                'Password reset OTP verification failed - OTP already used',
                null,
                null,
                ['method' => 'otp', 'reason' => 'already_used']
            );

            return back()->withErrors(['otp' => 'This OTP has already been used. Please request a new one.'])
                ->withInput();
        }

        // Check if OTP is expired
        if (now()->greaterThan($resetRecord->expires_at)) {
            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_FAILED,
                'Password reset OTP verification failed - OTP expired',
                null,
                null,
                ['method' => 'otp', 'reason' => 'expired']
            );

            return back()->withErrors(['otp' => 'This OTP has expired. Please request a new one.'])
                ->withInput();
        }

        // Verify the OTP
        if ($resetRecord->token !== $request->otp) {
            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_FAILED,
                'Password reset OTP verification failed - incorrect OTP',
                null,
                null,
                ['method' => 'otp', 'reason' => 'incorrect_otp']
            );

            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.'])
                ->withInput();
        }

        // OTP is valid - get the user and update password
        $user = User::where('email', $request->email)->first();

        // Update the user's password
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // Mark OTP as used
        \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('method', 'otp')
            ->update(['used_at' => now()]);

        // Clear rate limiting
        RateLimiter::clear($this->getRateLimitKey($request));

        // Log the successful reset
        AuditLog::log(
            AuditLog::EVENT_PASSWORD_RESET_COMPLETE,
            'Password reset completed successfully via OTP',
            $user->id,
            $user->id,
            ['method' => 'otp']
        );

        return redirect('/')->with('status', 'Password has been reset successfully. Please log in with your new password.');
    }

    /**
     * Check rate limiting for password reset attempts.
     *
     * @param Request $request
     * @throws ValidationException
     */
    protected function checkResetRateLimit(Request $request): void
    {
        $key = $this->getRateLimitKey($request);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttemptsPerMinute)) {
            $seconds = RateLimiter::availableIn($key);

            AuditLog::log(
                AuditLog::EVENT_PASSWORD_RESET_FAILED,
                'Password reset rate limit exceeded',
                null,
                null,
                ['ip' => $request->ip(), 'seconds_until_available' => $seconds]
            );

            throw ValidationException::withMessages([
                'email' => ['Too many reset attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'],
            ]);
        }

        RateLimiter::hit($key, 60);
    }

    /**
     * Get the rate limit key for the request.
     *
     * @param Request $request
     * @return string
     */
    protected function getRateLimitKey(Request $request): string
    {
        return 'password-reset-attempt:' . ($request->ip() . ':' . $request->email);
    }
}
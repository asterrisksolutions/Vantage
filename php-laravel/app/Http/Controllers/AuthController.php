<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AuthController
 *
 * Handles all authentication-related operations:
 * - User login with email/password
 * - Role-based redirection after login
 * - Account lockout checking
 * - Failed login attempt tracking
 * - Session logout
 *
 * This controller bridges the login form (welcome.blade.php)
 * with Laravel's built-in authentication system.
 */
class AuthController extends Controller
{
    /**
     * Handle an incoming login request.
     *
     * Flow:
     * 1. Validate that username (email) and password are provided
     * 2. Look up user by email in the database
     * 3. Check if account is locked
     * 4. Attempt authentication with Laravel's Auth::attempt()
     * 5. On success: regenerate session, reset failed attempts, update last login, redirect by role
     * 6. On failure: increment failed attempts, return with error
     *
     * @param Request $request The HTTP request containing username (email) and password
     * @return \Illuminate\Http\RedirectResponse Redirects to appropriate dashboard or back with error
     */
    public function login(Request $request)
    {
        // Validate the incoming form data
        // username field expects a valid email address
        $credentials = $request->validate([
            'username' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Look up the user record by email before attempting auth
        // This lets us check account lock status and track failed attempts
        $user = User::where('email', $credentials['username'])->first();

        // Check if the account is locked before attempting authentication
        // Locked accounts cannot log in regardless of password correctness
        if ($user && $user->is_locked) {
            return back()->withErrors(['login' => 'Your account has been locked.']);
        }

        // Attempt to authenticate using Laravel's built-in Auth facade
        // Auth::attempt() compares the plain password against the bcrypt hash in the database
        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
            // Regenerate session ID to prevent session fixation attacks
            $request->session()->regenerate();
            
            // Set session timeout tracking
            $request->session()->put('last_activity_time', time());

            // Reset security metrics on successful login
            if ($user) {
                $user->update([
                    'failed_attempts' => 0,          // Clear failed attempts
                    'last_login_at' => now(),        // Record current timestamp
                ]);
            }

            // Determine redirect destination based on user's role
            // Admins go to admin dashboard, Managers to manager dashboard, all others to user landing
            $roleName = Auth::user()->role->name ?? null;

            return match ($roleName) {
                'Admin' => redirect()->route('admin.dashboard'),
                'Manager' => redirect()->route('manager.dashboard'),
                default => redirect()->route('user.landing'),
            };
        }

        // Authentication failed - increment failed attempt counter
        // This can be used to implement automatic lockout after N failed attempts
        if ($user) {
            $user->increment('failed_attempts');
        }

        // Return back to login page with a generic error message
        return back()->withErrors(['login' => 'User not existing or incorrect credentials.']);
    }

    /**
     * Log the user out and invalidate the session.
     *
     * Flow:
     * 1. Log out the authenticated user via Auth facade
     * 2. Invalidate the current session (removes all session data)
     * 3. Regenerate the CSRF token to prevent token reuse
     * 4. Redirect to the home/login page
     *
     * @param Request $request The HTTP request to access session
     * @return \Illuminate\Http\RedirectResponse Redirects to home page
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Handle registration requests.
     *
     * Currently a placeholder - redirects back to login.
     * Extend this method to implement user registration logic.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // wla pa akong linagay dito baka magulo backend
        // For now this just redirect back to login
        return redirect('/');
    }
}


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\AdminPasswordResetController;
use App\Http\Controllers\DocumentController;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

/**
 * Web Routes
 *
 * These routes are loaded by the RouteServiceProvider
 * and use the 'web' middleware group (enables sessions, CSRF, etc.)
 *
 * Authentication flow:
 * 1. GET /           - Shows login form (welcome.blade.php)
 * 2. POST /login     - Submits credentials to AuthController@login
 * 3. Auth middleware - Protects dashboard/profile routes
 * 4. GET /logout     - Destroys session via AuthController@logout
 */

// ============================================================
// PUBLIC ROUTES (no authentication required)
// ============================================================

// Home page / Login form
// This is the entry point for all unauthenticated users
Route::get('/', function () {
    return view('welcome');
});

// Displays the login page
Route::get('/login', function () {
    return view('welcome');
});

// Login form submission
// Validates credentials and redirects based on user role
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Logout - accessible without auth middleware so locked-out users can still log out
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration page (currently a placeholder)
Route::get('/register', function () {
    return view('register.register');
})->name('register');

// Registration form submission (currently redirects to home)
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// ============================================================
// PROTECTED ROUTES (authentication required)
// ============================================================
// The 'auth' middleware ensures only logged-in users can access these.
// Unauthenticated visitors are redirected to the login page.

Route::middleware(['auth', 'session.timeout'])->group(function () {

    // User landing page - default destination for standard User role
    Route::get('/user/landing', function () {
        $documents = Document::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user.landing', compact('documents'));
    })->name('user.landing');

    // Manager dashboard - destination for Manager role after login
    Route::get('/manager/dashboard', function () {
        return view('manager.manager');
    })->name('manager.dashboard');

    // Admin dashboard - destination for Admin role after login
    Route::get('/admin/dashboard', function () {
        return view('admin.admin');
    })->name('admin.dashboard');

    // Admin users management
    Route::get('/admin/users', function () {
        return view('admin.users');
    })->name('admin.users');

    // Admin audit logs
    Route::get('/admin/audit-logs', function () {
        return view('admin.audit-logs');
    })->name('admin.audit-logs');

    // User profile page - accessible to any authenticated user
    Route::get('/user/profile', function () {
        return view('user.profile');
    })->name('user.profile');

    // Document management routes
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::post('/documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
});

// ============================================================
// PASSWORD RESET ROUTES (public - no auth required)
// ============================================================

// Show "forgot password" form
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

// Send password reset link (token method)
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->name('password.email');

// Send password reset OTP
Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'sendResetOtp'])
    ->name('password.otp');

// Show OTP verification form
Route::get('/verify-otp', [ResetPasswordController::class, 'showVerifyOtpForm'])
    ->name('password.verify-otp');

// Verify OTP and reset password
Route::post('/verify-otp', [ResetPasswordController::class, 'verifyOtpAndReset'])
    ->name('password.verify-otp');

// Show password reset form (token method)
Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

// Handle password reset (token method)
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// ============================================================
// ADMIN PASSWORD RESET ROUTES (admin only)
// ============================================================

Route::middleware(['auth', 'session.timeout', 'role:Admin'])->group(function () {
    // Show admin password reset form
    Route::get('/admin/password-reset', [AdminPasswordResetController::class, 'showResetForm'])
        ->name('admin.password.reset.form');

    // Search users for admin password reset
    Route::get('/admin/password-reset/search', [AdminPasswordResetController::class, 'searchUsers'])
        ->name('admin.password.search');

    // Generate random password
    Route::post('/admin/password-reset/generate', [AdminPasswordResetController::class, 'generatePassword'])
        ->name('admin.password.generate');

    // Reset user password
    Route::post('/admin/password-reset', [AdminPasswordResetController::class, 'resetUserPassword'])
        ->name('admin.password.reset');
});


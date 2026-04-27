<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth')->group(function () {

    // User landing page - default destination for standard User role
    Route::get('/user/landing', function () {
        return view('user.landing');
    })->name('user.landing');

    // Manager dashboard - destination for Manager role after login
    Route::get('/manager/dashboard', function () {
        return view('manager.manager');
    })->name('manager.dashboard');

    // Admin dashboard - destination for Admin role after login
    Route::get('/admin/dashboard', function () {
        return view('admin.admin');
    })->name('admin.dashboard');

    // User profile page - accessible to any authenticated user
    Route::get('/user/profile', function () {
        return view('user.profile');
    })->name('user.profile');
});


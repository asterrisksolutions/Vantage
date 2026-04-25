<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return view('register.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::get('/user/landing', function () {
    return view('user.landing');
})->name('user.landing');

Route::get('/manager/dashboard', function () {
    return view('manager.manager');
})->name('manager.dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.admin');
})->name('admin.dashboard');

Route::get('/user/profile', function () {
    return view('user.profile');
})->name('user.profile');
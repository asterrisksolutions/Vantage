<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if ($username === 'Admin' && $password === 'admin1234') {
            return redirect()->route('admin.dashboard');
        } elseif ($username === 'Manager' && $password === 'manager123') {
            return redirect()->route('manager.dashboard');
        } elseif ($username === 'JohnDoe' && $password === 'john123') {
            return redirect()->route('user.landing');
        } else {
            return back()->withErrors(['login' => 'User not existing or incorrect credentials.']);
        }
    }

    public function logout()
    {
        return redirect('/');
    }

    public function register(Request $request)
{
    // wla pa akong linagay dito baka magulo backend
    // For now this just redirect back to login
    return redirect('/');
}
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminLoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/Login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login_identifier' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = [
            'login_identifier' => $request->login_identifier,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('message', 'Welcome back to Admin Dashboard');
        }

        return back()->withErrors('Invalid email or password.');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('message', 'You have been logged out successfully.');
    }
}

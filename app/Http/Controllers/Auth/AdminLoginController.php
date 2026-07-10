<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
            if (Auth::user()->role === 'admin') {
                $request->session()->regenerate();

                return redirect()->route('admin.batches.index')->with('success', 'Welcome back to Admin Dashboard');
            }

            Auth::logout();
        }

        throw ValidationException::withMessages([
            'error' => 'Invalid email or password.',
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }
}

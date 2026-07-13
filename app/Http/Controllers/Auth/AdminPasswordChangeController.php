<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AdminPasswordChangeController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/ChangePassword');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        DB::update(
            'UPDATE users SET password = ?, is_password_changed = ?, updated_at = ? WHERE id = ?',
            [Hash::make($validated['password']), true, now(), $user->id]
        );

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Your password has been changed successfully. Please log in again.');
    }
}

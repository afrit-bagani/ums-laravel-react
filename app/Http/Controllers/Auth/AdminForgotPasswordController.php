<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminForgotPasswordController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/ForgotPassword');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login_identifier' => ['required', 'string', 'email'],
        ]);

        $user = DB::table('users')
            ->where('role', 'admin')
            ->where('login_identifier', $request->login_identifier)
            ->first();

        if (! $user) {
            return back()->withErrors(['error' => 'No admin account found with this email address.']);
        }

        $temporaryPassword = Str::random(10);

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($temporaryPassword),
            'is_password_changed' => false,
            'updated_at' => now(),
        ]);

        Mail::to($user->login_identifier)->send(
            new ForgotPasswordEmail(
                $user->name,
                $user->login_identifier,
                $temporaryPassword,
                route('admin.login')
            )
        );

        return redirect()->route('admin.login')->with('success', 'A new temporary password has been sent to your email.');
    }
}

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

class StudentForgotPasswordController extends Controller
{
    public function create()
    {
        return Inertia::render('Student/ForgotPassword');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login_identifier' => ['required', 'string'],
        ]);

        $user = DB::table('users')
            ->where('role', 'student')
            ->where('login_identifier', $request->login_identifier)
            ->first();

        if (! $user) {
            return back()->withErrors(['error' => 'No student found with this registration number.']);
        }

        $studentProfile = DB::table('student_profiles')
            ->where('user_id', $user->id)
            ->first();

        if (! $studentProfile || empty($studentProfile->email)) {
            return back()->withErrors(['error' => 'No email address is associated with this student account.']);
        }

        $temporaryPassword = Str::random(10);

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($temporaryPassword),
            'is_password_changed' => false,
            'updated_at' => now(),
        ]);

        Mail::to($studentProfile->email)->send(
            new ForgotPasswordEmail(
                $studentProfile->full_name,
                $user->login_identifier,
                $temporaryPassword,
                route('student.login')
            )
        );

        return redirect()->route('student.login')->with('success', 'A new temporary password has been sent to your email.');
    }
}

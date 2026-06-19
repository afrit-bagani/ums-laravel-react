<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class StudentLoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Student/Login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login_identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'login_identifier' => $request->login_identifier,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $name = Auth::user()->name;

            return redirect()->route('student.dashboard')->with('message', "Welcome {$name}");
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

        return redirect()->route('student.login')->with('message', 'You have been loggout successfully');

    }
}

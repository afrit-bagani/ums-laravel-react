<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Student/Login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'registration_number' => ['required', 'string', 'unique:users,registration_number', 'exists:users,registration_number'],
            'password' => ['required', 'string'],
        ]);

        return Inertia::render('Student/Login');
    }
}

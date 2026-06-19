<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request for the welcome page.
     */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            if ($role === 'student') {
                return redirect()->route('student.dashboard')->with('message', 'Welcome to Student dashboard');
            } else {
                return redirect()->route('admin.dashboard');
            }
        }

        return Inertia::render('Welcome');
    }
}

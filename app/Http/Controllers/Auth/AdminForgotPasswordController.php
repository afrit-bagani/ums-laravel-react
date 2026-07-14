<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordEmail;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminForgotPasswordController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function create()
    {
        return Inertia::render('Admin/ForgotPassword');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login_identifier' => ['required', 'string', 'email'],
        ]);

        $user = $this->userRepo->findByRoleAndIdentifier('admin', $request->login_identifier);

        if (! $user) {
            return back()->withErrors(['error' => 'No admin account found with this email address.']);
        }

        $temporaryPassword = Str::random(10);

        $this->userRepo->updatePassword(
            $user->id,
            Hash::make($temporaryPassword),
            false
        );

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

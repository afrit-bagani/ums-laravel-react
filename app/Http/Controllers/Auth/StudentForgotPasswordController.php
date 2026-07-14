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

class StudentForgotPasswordController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function create()
    {
        return Inertia::render('Student/ForgotPassword');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login_identifier' => ['required', 'string'],
        ]);

        $user = $this->userRepo->findByRoleAndIdentifier('student', $request->login_identifier);

        if (! $user) {
            return back()->withErrors(['error' => 'No student found with this registration number.']);
        }

        $studentProfile = $this->userRepo->findStudentProfileByUserId($user->id);

        if (! $studentProfile || empty($studentProfile->email)) {
            return back()->withErrors(['error' => 'No email address is associated with this student account.']);
        }

        $temporaryPassword = Str::random(10);

        $this->userRepo->updatePassword(
            $user->id,
            Hash::make($temporaryPassword),
            false
        );

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

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordEmail;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
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

        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Save OTP with 15 minutes expiration
        $this->userRepo->saveResetOtp($user->id, $otp, now()->addMinutes(15));

        // Send Email to the student's personal email
        Mail::to($studentProfile->email)->send(
            new ForgotPasswordEmail(
                $studentProfile->full_name,
                $user->login_identifier,
                $otp,
                route('student.login')
            )
        );

        // Store identifier in session so we know who is verifying
        session(['student_reset_identifier' => $user->login_identifier]);

        return redirect()->route('student.password.verify')->with('success', 'A 6-digit OTP has been sent to your registered email.');
    }

    public function showVerifyOtp()
    {
        if (! session()->has('student_reset_identifier')) {
            return redirect()->route('student.password.request')->withErrors(['error' => 'Session expired. Please request a new OTP.']);
        }

        return Inertia::render('Student/VerifyOtp', [
            'login_identifier' => session('student_reset_identifier')
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $identifier = session('student_reset_identifier');
        if (! $identifier) {
            return redirect()->route('student.password.request')->withErrors(['error' => 'Session expired. Please request a new OTP.']);
        }

        $user = $this->userRepo->findByRoleAndIdentifier('student', $identifier);

        if (! $user || ! $user->reset_otp || $user->reset_otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (now()->greaterThan($user->reset_otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        // OTP is valid, clear it from DB
        $this->userRepo->clearResetOtp($user->id);

        // Grant access to reset password page
        session(['student_can_reset_password' => true]);

        return redirect()->route('student.password.reset')->with('success', 'OTP verified successfully. Please set your new password.');
    }

    public function showResetPassword()
    {
        if (! session('student_can_reset_password') || ! session()->has('student_reset_identifier')) {
            return redirect()->route('student.password.request')->withErrors(['error' => 'Unauthorized access. Please request a new OTP.']);
        }

        return Inertia::render('Student/ResetPassword');
    }

    public function resetPassword(Request $request)
    {
        if (! session('student_can_reset_password') || ! session()->has('student_reset_identifier')) {
            return redirect()->route('student.password.request')->withErrors(['error' => 'Session expired. Please restart the password reset process.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $this->userRepo->findByRoleAndIdentifier('student', session('student_reset_identifier'));

        if (! $user) {
            return redirect()->route('student.password.request')->withErrors(['error' => 'User not found.']);
        }

        // Update password and set is_password_changed to true
        $this->userRepo->updatePassword(
            $user->id,
            Hash::make($request->password),
            true
        );

        // Clear all session flags
        session()->forget(['student_reset_identifier', 'student_can_reset_password']);

        return redirect()->route('student.login')->with('success', 'Your password has been successfully reset. You may now login.');
    }
}

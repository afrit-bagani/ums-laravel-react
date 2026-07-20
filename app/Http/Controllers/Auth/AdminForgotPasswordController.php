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

        // Generate 6-digit OTP
        $otp = sprintf('%06d', mt_rand(1, 999999));

        // Save OTP with 15 minutes expiration
        $this->userRepo->saveResetOtp($user->id, $otp, now()->addMinutes(15));

        // Send Email
        Mail::to($user->login_identifier)->send(
            new ForgotPasswordEmail(
                $user->name,
                $user->login_identifier,
                $otp,
                route('admin.login')
            )
        );

        // Store identifier in session so we know who is verifying
        session(['reset_identifier' => $user->login_identifier]);

        return redirect()->route('admin.password.verify')->with('success', 'A 6-digit OTP has been sent to your email.');
    }

    public function showVerifyOtp()
    {
        if (! session()->has('reset_identifier')) {
            return redirect()->route('admin.password.request')->withErrors(['error' => 'Session expired. Please request a new OTP.']);
        }

        return Inertia::render('Admin/VerifyOtp', [
            'login_identifier' => session('reset_identifier'),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $identifier = session('reset_identifier');
        if (! $identifier) {
            return redirect()->route('admin.password.request')->withErrors(['error' => 'Session expired. Please request a new OTP.']);
        }

        $user = $this->userRepo->findByRoleAndIdentifier('admin', $identifier);

        if (! $user || ! $user->reset_otp || $user->reset_otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (now()->greaterThan($user->reset_otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        // OTP is valid, clear it from DB
        $this->userRepo->clearResetOtp($user->id);

        // Grant access to reset password page
        session(['can_reset_password' => true]);

        return redirect()->route('admin.password.reset')->with('success', 'OTP verified successfully. Please set your new password.');
    }

    public function showResetPassword()
    {
        if (! session('can_reset_password') || ! session()->has('reset_identifier')) {
            return redirect()->route('admin.password.request')->withErrors(['error' => 'Unauthorized access. Please request a new OTP.']);
        }

        return Inertia::render('Admin/ResetPassword');
    }

    public function resetPassword(Request $request)
    {
        if (! session('can_reset_password') || ! session()->has('reset_identifier')) {
            return redirect()->route('admin.password.request')->withErrors(['error' => 'Session expired. Please restart the password reset process.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $this->userRepo->findByRoleAndIdentifier('admin', session('reset_identifier'));

        if (! $user) {
            return redirect()->route('admin.password.request')->withErrors(['error' => 'User not found.']);
        }

        // Update password and set is_password_changed to true
        $this->userRepo->updatePassword(
            $user->id,
            Hash::make($request->password),
            true
        );

        // Clear all session flags
        session()->forget(['reset_identifier', 'can_reset_password']);

        return redirect()->route('admin.login')->with('success', 'Your password has been successfully reset. You may now login.');
    }
}

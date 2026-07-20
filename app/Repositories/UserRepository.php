<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserRepository
{
    /**
     * Find a user by their role and login identifier.
     */
    public function findByRoleAndIdentifier(string $role, string $identifier)
    {
        return DB::selectOne(
            'SELECT * FROM users WHERE role = ? AND login_identifier = ?',
            [$role, $identifier]
        );
    }

    /**
     * Find a student profile by their associated user ID.
     */
    public function findStudentProfileByUserId(int $userId)
    {
        return DB::selectOne(
            'SELECT * FROM student_profiles WHERE user_id = ?',
            [$userId]
        );
    }

    /**
     * Update a user's password.
     */
    public function updatePassword(int $userId, string $hashedPassword, bool $isPasswordChanged)
    {
        return DB::update(
            'UPDATE users SET password = ?, is_password_changed = ?, updated_at = ? WHERE id = ?',
            [$hashedPassword, (int)$isPasswordChanged, now(), $userId]
        );
    }

    /**
     * Save the password reset OTP for a user.
     */
    public function saveResetOtp(int $userId, string $otp, string $expiresAt)
    {
        return DB::update(
            'UPDATE users SET reset_otp = ?, reset_otp_expires_at = ?, updated_at = ? WHERE id = ?',
            [$otp, $expiresAt, now(), $userId]
        );
    }

    /**
     * Clear the password reset OTP for a user after successful verification.
     */
    public function clearResetOtp(int $userId)
    {
        return DB::update(
            'UPDATE users SET reset_otp = NULL, reset_otp_expires_at = NULL, updated_at = ? WHERE id = ?',
            [now(), $userId]
        );
    }
}

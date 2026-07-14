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
            [$hashedPassword, $isPasswordChanged, now(), $userId]
        );
    }
}

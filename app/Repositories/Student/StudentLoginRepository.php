<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class StudentLoginRepository
{
    public function login($registration_number, $password)
    {
        return DB::select('SELECT * FROM users WHERE registration_number = ? AND password = ?', [$registration_number, $password]);
    }
}

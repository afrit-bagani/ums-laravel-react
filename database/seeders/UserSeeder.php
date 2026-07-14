<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Afrit Bagani',
            'login_identifier' => 'afrit.bagani22@tnu.in',
            'role' => 'admin',
            'is_password_changed' => true,
        ]);
    }
}

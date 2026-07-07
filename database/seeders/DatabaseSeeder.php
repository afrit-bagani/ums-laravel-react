<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BatchSeeder::class,
            ProgrammeSeeder::class,
            CourseSeeder::class,
            SubjectSeeder::class,
            StudentProfileSeeder::class,
        ]);
    }
}

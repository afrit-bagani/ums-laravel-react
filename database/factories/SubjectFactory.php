<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
use Illuminate\Support\Facades\DB;

class SubjectFactory extends Factory
{
    public function definition(): array
    {
        $course = DB::table('course_master')->inRandomOrder()->first();

        return [
            'code' => $this->faker->unique()->bothify('SUB-####'),
            'name' => ucfirst($this->faker->words(3, true)),
            'status' => 'active',
            'internal_full_marks' => 30.00,
            'internal_pass_marks' => 12.00,
            'theory_full_marks' => 70.00,
            'theory_pass_marks' => 28.00,
            'practical_full_marks' => 0.00,
            'practical_pass_marks' => 0.00,
            'programme_id' => $course ? $course->programme_id : null,
            'course_id' => $course ? $course->course_id : null,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

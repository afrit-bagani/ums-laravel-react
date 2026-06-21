<?php

namespace Database\Factories;

use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Batch>
 */
class BatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 1. Calculate realistic academic years
        $startYear = $this->faker->numberBetween(2018, 2024);
        $duration = $this->faker->randomElement([2, 3, 4, 5]); // 2 to 5 year degrees
        $endYear = $startYear + $duration;
        $shortEndYear = substr($endYear, -2);

        return [
            'code' => strtoupper($this->faker->unique()->lexify('???')) . '-' . $this->faker->unique()->numerify('####'),
            'name' => "{$startYear}-{$shortEndYear}", // Generates "2022-26"
            'status' => $this->faker->randomElement(array_fill(0, 8, 'active') + array_fill(0, 2, 'inactive')),
            'created_by' => $this->faker->numberBetween(1, 5),
            'updated_by' => $this->faker->numberBetween(1, 5),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}

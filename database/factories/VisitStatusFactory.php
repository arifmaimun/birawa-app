<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisitStatus>
 */
class VisitStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word;

        return [
            'name' => ucfirst($name),
            'slug' => strtolower($name),
            'color' => $this->faker->hexColor,
            'description' => $this->faker->sentence,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Visit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = \App\Models\VisitStatus::inRandomOrder()->first();

        return [
            'patient_id' => Patient::factory(),
            'user_id' => User::factory(),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'visit_status_id' => $status ? $status->id : \App\Models\VisitStatus::factory(),
            'complaint' => $this->faker->sentence,
            'transport_fee' => $this->faker->numberBetween(10000, 50000),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}

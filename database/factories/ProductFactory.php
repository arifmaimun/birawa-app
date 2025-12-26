<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 10, 1000);
        $type = fake()->randomElement(['goods', 'service']);

        return [
            'sku' => fake()->unique()->ean13(),
            'name' => fake()->word(),
            'type' => $type,
            'cost' => $cost,
            'price' => $cost * 1.3,
            'stock' => $type === 'goods' ? fake()->numberBetween(1, 100) : null,
        ];
    }
}

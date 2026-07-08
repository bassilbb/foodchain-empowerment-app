<?php

namespace Database\Factories;

use App\Models\SavingsPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavingsPlanFactory extends Factory
{
    protected $model = SavingsPlan::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Bronze', 'Silver', 'Gold', 'Emerald']),
            'target_amount' => fake()->randomElement([100000, 150000, 200000, 250000]),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

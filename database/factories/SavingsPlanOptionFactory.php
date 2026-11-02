<?php

namespace Database\Factories;

use App\Models\SavingsPlan;
use App\Models\SavingsPlanOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavingsPlanOptionFactory extends Factory
{
    protected $model = SavingsPlanOption::class;

    public function definition(): array
    {
        return [
            'savings_plan_id' => SavingsPlan::factory(),
            'duration_weeks' => fake()->randomElement([12, 25, 50]),
            'weekly_contribution' => fake()->randomFloat(2, 1000, 10000),
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

<?php

namespace Database\Factories;

use App\Models\SavingsPlanOption;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAccountFactory extends Factory
{
    protected $model = UserAccount::class;

    public function definition(): array
    {
        return [
            'primary_user_id' => User::factory(),
            'account_name' => fake()->words(2, true),
            'savings_plan_option_id' => SavingsPlanOption::factory(),
            'weekly_contribution_amount' => fake()->randomFloat(2, 1000, 10000),
            'current_tariff_weeks' => 0,
            'status' => 'active',
        ];
    }
}

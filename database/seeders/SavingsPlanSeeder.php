<?php

namespace Database\Seeders;

use App\Models\SavingsPlan;
use Illuminate\Database\Seeder;

class SavingsPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Bronze',
                'target_amount' => 100000,
                'description' => 'Entry-level savings plan with flexible duration options. Achieve your savings goal of ₦100,000.',
                'is_active' => true,
                'options' => [
                    ['duration_weeks' => 12, 'weekly_contribution' => 8333.33],
                    ['duration_weeks' => 25, 'weekly_contribution' => 4000],
                    ['duration_weeks' => 50, 'weekly_contribution' => 2000],
                ],
            ],
            [
                'name' => 'Silver',
                'target_amount' => 150000,
                'description' => 'Mid-tier savings plan for bigger goals. Save ₦150,000 with flexible weekly contributions.',
                'is_active' => true,
                'options' => [
                    ['duration_weeks' => 25, 'weekly_contribution' => 6000],
                    ['duration_weeks' => 50, 'weekly_contribution' => 3000],
                ],
            ],
            [
                'name' => 'Gold',
                'target_amount' => 200000,
                'description' => 'Premium savings plan with a structured path to ₦200,000.',
                'is_active' => true,
                'options' => [
                    ['duration_weeks' => 50, 'weekly_contribution' => 4000],
                ],
            ],
            [
                'name' => 'Emerald',
                'target_amount' => 250000,
                'description' => 'Elite savings plan for maximum returns. Save ₦250,000 over 50 weeks.',
                'is_active' => true,
                'options' => [
                    ['duration_weeks' => 50, 'weekly_contribution' => 5000],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $options = $planData['options'];
            unset($planData['options']);

            $plan = SavingsPlan::create($planData);

            foreach ($options as $optionData) {
                $plan->options()->create($optionData);
            }
        }
    }
}

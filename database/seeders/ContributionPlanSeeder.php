<?php

namespace Database\Seeders;

use App\Models\ContributionPlan;
use Illuminate\Database\Seeder;

class ContributionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'weekly_amount' => 1000,
                'duration_weeks' => 52,
                'total_contribution' => 52000,
                'description' => 'Affordable weekly contributions. Save ₦1,000 per week for 52 weeks.',
                'is_active' => true,
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'weekly_amount' => 2500,
                'duration_weeks' => 52,
                'total_contribution' => 130000,
                'description' => 'Balanced weekly contributions. Save ₦2,500 per week for 52 weeks.',
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'weekly_amount' => 5000,
                'duration_weeks' => 26,
                'total_contribution' => 130000,
                'description' => 'Accelerated savings plan. Save ₦5,000 per week for 26 weeks.',
                'is_active' => true,
            ],
            [
                'name' => 'Executive',
                'slug' => 'executive',
                'weekly_amount' => 10000,
                'duration_weeks' => 26,
                'total_contribution' => 260000,
                'description' => 'Premium accelerated savings. Save ₦10,000 per week for 26 weeks.',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            ContributionPlan::create($plan);
        }
    }
}

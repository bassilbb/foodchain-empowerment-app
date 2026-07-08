<?php

namespace Database\Factories;

use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipTierFactory extends Factory
{
    protected $model = MembershipTier::class;

    public function definition(): array
    {
        $slug = 'tier-'.fake()->unique()->bothify('####??');

        return [
            'name' => fake()->words(2, true),
            'slug' => $slug,
            'annual_fee' => 5000,
            'instant_food_pack_price' => 20000,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}

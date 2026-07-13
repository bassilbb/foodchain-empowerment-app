<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use App\Models\FoodPackage;
use App\Models\MembershipTier;
use Illuminate\Database\Seeder;

class FoodPackageSeeder extends Seeder
{
    public function run(): void
    {
        if (FoodPackage::count() > 0) {
            return;
        }

        $tiers = MembershipTier::all()->keyBy('slug');
        $items = FoodItem::all()->keyBy('slug');

        $packages = [
            'bronze' => [
                'name' => 'Bronze Starter Pack',
                'description' => 'Essential food items for bronze members. Includes staple foods for a household.',
                'total_value' => 100000.00,
                'package_items' => [
                    ['food_item' => 'rice', 'quantity' => 25, 'unit_value' => 1000],
                    ['food_item' => 'beans', 'quantity' => 10, 'unit_value' => 800],
                    ['food_item' => 'garri', 'quantity' => 20, 'unit_value' => 500],
                    ['food_item' => 'cooking-oil', 'quantity' => 10, 'unit_value' => 1500],
                    ['food_item' => 'sugar', 'quantity' => 5, 'unit_value' => 800],
                    ['food_item' => 'spaghetti', 'quantity' => 10, 'unit_value' => 400],
                    ['food_item' => 'salt', 'quantity' => 5, 'unit_value' => 200],
                    ['food_item' => 'tomato-paste', 'quantity' => 10, 'unit_value' => 400],
                    ['food_item' => 'milk', 'quantity' => 5, 'unit_value' => 600],
                    ['food_item' => 'seasoning-cubes', 'quantity' => 10, 'unit_value' => 300],
                ],
            ],
            'silver' => [
                'name' => 'Silver Family Pack',
                'description' => 'Enhanced food package for silver members. More quantity and variety.',
                'total_value' => 150000.00,
                'package_items' => [
                    ['food_item' => 'rice', 'quantity' => 30, 'unit_value' => 1000],
                    ['food_item' => 'beans', 'quantity' => 15, 'unit_value' => 800],
                    ['food_item' => 'garri', 'quantity' => 25, 'unit_value' => 500],
                    ['food_item' => 'cooking-oil', 'quantity' => 15, 'unit_value' => 1500],
                    ['food_item' => 'sugar', 'quantity' => 10, 'unit_value' => 800],
                    ['food_item' => 'spaghetti', 'quantity' => 15, 'unit_value' => 400],
                    ['food_item' => 'wheat', 'quantity' => 10, 'unit_value' => 700],
                    ['food_item' => 'egg', 'quantity' => 5, 'unit_value' => 3500],
                    ['food_item' => 'salt', 'quantity' => 5, 'unit_value' => 200],
                    ['food_item' => 'tomato-paste', 'quantity' => 10, 'unit_value' => 400],
                    ['food_item' => 'milk', 'quantity' => 10, 'unit_value' => 600],
                    ['food_item' => 'seasoning-cubes', 'quantity' => 10, 'unit_value' => 300],
                    ['food_item' => 'beverages', 'quantity' => 5, 'unit_value' => 500],
                ],
            ],
            'gold' => [
                'name' => 'Gold Premium Pack',
                'description' => 'Premium food package for gold members. Bulk quantities with premium items.',
                'total_value' => 200000.00,
                'package_items' => [
                    ['food_item' => 'rice', 'quantity' => 40, 'unit_value' => 1000],
                    ['food_item' => 'beans', 'quantity' => 20, 'unit_value' => 800],
                    ['food_item' => 'garri', 'quantity' => 30, 'unit_value' => 500],
                    ['food_item' => 'cooking-oil', 'quantity' => 20, 'unit_value' => 1500],
                    ['food_item' => 'sugar', 'quantity' => 15, 'unit_value' => 800],
                    ['food_item' => 'spaghetti', 'quantity' => 20, 'unit_value' => 400],
                    ['food_item' => 'wheat', 'quantity' => 15, 'unit_value' => 700],
                    ['food_item' => 'egg', 'quantity' => 8, 'unit_value' => 3500],
                    ['food_item' => 'salt', 'quantity' => 10, 'unit_value' => 200],
                    ['food_item' => 'tomato-paste', 'quantity' => 15, 'unit_value' => 400],
                    ['food_item' => 'milk', 'quantity' => 15, 'unit_value' => 600],
                    ['food_item' => 'seasoning-cubes', 'quantity' => 15, 'unit_value' => 300],
                    ['food_item' => 'beverages', 'quantity' => 10, 'unit_value' => 500],
                    ['food_item' => 'spices', 'quantity' => 10, 'unit_value' => 250],
                ],
            ],
            'emerald' => [
                'name' => 'Emerald Executive Pack',
                'description' => 'Executive food package for emerald leaders. Maximum quantity, best value.',
                'total_value' => 250000.00,
                'package_items' => [
                    ['food_item' => 'rice', 'quantity' => 50, 'unit_value' => 1000],
                    ['food_item' => 'beans', 'quantity' => 25, 'unit_value' => 800],
                    ['food_item' => 'garri', 'quantity' => 40, 'unit_value' => 500],
                    ['food_item' => 'cooking-oil', 'quantity' => 25, 'unit_value' => 1500],
                    ['food_item' => 'sugar', 'quantity' => 20, 'unit_value' => 800],
                    ['food_item' => 'spaghetti', 'quantity' => 25, 'unit_value' => 400],
                    ['food_item' => 'wheat', 'quantity' => 20, 'unit_value' => 700],
                    ['food_item' => 'egg', 'quantity' => 10, 'unit_value' => 3500],
                    ['food_item' => 'salt', 'quantity' => 10, 'unit_value' => 200],
                    ['food_item' => 'tomato-paste', 'quantity' => 20, 'unit_value' => 400],
                    ['food_item' => 'milk', 'quantity' => 20, 'unit_value' => 600],
                    ['food_item' => 'seasoning-cubes', 'quantity' => 20, 'unit_value' => 300],
                    ['food_item' => 'beverages', 'quantity' => 15, 'unit_value' => 500],
                    ['food_item' => 'spices', 'quantity' => 15, 'unit_value' => 250],
                ],
            ],
        ];

        foreach ($packages as $slug => $data) {
            $tier = $tiers->get($slug);
            if (! $tier) {
                continue;
            }

            $package = FoodPackage::create([
                'tier_id' => $tier->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'total_value' => $data['total_value'],
                'is_active' => true,
                'is_default' => true,
            ]);

            foreach ($data['package_items'] as $itemData) {
                $foodItem = $items->get($itemData['food_item']);
                if (! $foodItem) {
                    continue;
                }

                $package->items()->create([
                    'food_item_id' => $foodItem->id,
                    'quantity' => $itemData['quantity'],
                    'unit_value' => $itemData['unit_value'],
                ]);
            }
        }
    }
}

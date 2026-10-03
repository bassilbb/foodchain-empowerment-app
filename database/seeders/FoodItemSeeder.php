<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodItemSeeder extends Seeder
{
    public function run(): void
    {
        $foodItems = [
            ['name' => 'Rice', 'slug' => 'rice', 'unit' => 'kg', 'unit_price' => 1000],
            ['name' => 'Beans', 'slug' => 'beans', 'unit' => 'kg', 'unit_price' => 800],
            ['name' => 'Egg', 'slug' => 'egg', 'unit' => 'crate', 'unit_price' => 3500],
            ['name' => 'Garri', 'slug' => 'garri', 'unit' => 'kg', 'unit_price' => 500],
            ['name' => 'Wheat', 'slug' => 'wheat', 'unit' => 'kg', 'unit_price' => 700],
            ['name' => 'Spaghetti', 'slug' => 'spaghetti', 'unit' => 'pack', 'unit_price' => 400],
            ['name' => 'Salt', 'slug' => 'salt', 'unit' => 'kg', 'unit_price' => 200],
            ['name' => 'Cooking Oil', 'slug' => 'cooking-oil', 'unit' => 'liters', 'unit_price' => 1500],
            ['name' => 'Sugar', 'slug' => 'sugar', 'unit' => 'kg', 'unit_price' => 800],
            ['name' => 'Milk', 'slug' => 'milk', 'unit' => 'tin', 'unit_price' => 600],
            ['name' => 'Tomato Paste', 'slug' => 'tomato-paste', 'unit' => 'tin', 'unit_price' => 400],
            ['name' => 'Beverages', 'slug' => 'beverages', 'unit' => 'pack', 'unit_price' => 500],
            ['name' => 'Seasoning Cubes', 'slug' => 'seasoning-cubes', 'unit' => 'pack', 'unit_price' => 300],
            ['name' => 'Spices', 'slug' => 'spices', 'unit' => 'pack', 'unit_price' => 250],
        ];

        foreach ($foodItems as $item) {
            DB::table('food_items')->insert([
                'name' => $item['name'],
                'slug' => $item['slug'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

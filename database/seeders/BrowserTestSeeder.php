<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Minimal, deterministic dataset for the Playwright browser tests.
 *
 * It is only loaded against the throwaway SQLite database created by
 * tests/Browser/serve.js, never against the real application database.
 */
class BrowserTestSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['email' => 'playwright-admin@foodchain.test'],
            [
                'name' => 'Playwright Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
            ],
        );

        // Storefront shopper used to prove that a bulk price change reaches the
        // listing, product page and cart.
        User::factory()->create([
            'first_name' => 'Playwright',
            'last_name' => 'Customer',
            'email' => 'playwright-customer@foodchain.test',
            'password' => Hash::make('password'),
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $category = Category::create([
            'name' => 'Groceries',
            'slug' => 'groceries',
            'is_active' => true,
        ]);

        $vendor = $this->makeVendor('Browser Supplier', 'browser-supplier');
        $other = $this->makeVendor('Foreign Supplier', 'foreign-supplier');

        $products = [
            ['name' => 'Browser Rice', 'sku' => 'PW-0001', 'price' => 100],
            ['name' => 'Browser Beans', 'sku' => 'PW-0002', 'price' => 200],
            ['name' => 'Browser Garri', 'sku' => 'PW-0003', 'price' => 300],
        ];

        foreach ($products as $product) {
            Product::create([
                'vendor_id' => $vendor->id,
                'category_id' => $category->id,
                'name' => $product['name'],
                'slug' => strtolower(str_replace(' ', '-', $product['name'])),
                'sku' => $product['sku'],
                'price' => $product['price'],
                'stock_quantity' => 50,
                'is_active' => true,
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]);
        }

        Product::create([
            'vendor_id' => $other->id,
            'category_id' => $category->id,
            'name' => 'Foreign Item',
            'slug' => 'foreign-item',
            'sku' => 'OTHER-0001',
            'price' => 500,
            'stock_quantity' => 10,
            'is_active' => true,
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    private function makeVendor(string $name, string $slug): Vendor
    {
        $user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        return Vendor::create([
            'user_id' => $user->id,
            'name' => $name,
            'slug' => $slug,
            'business_name' => $name,
            'status' => 'approved',
            'commission_rate' => 10,
        ]);
    }
}

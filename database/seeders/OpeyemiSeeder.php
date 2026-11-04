<?php

namespace Database\Seeders;

use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use App\Support\ProductPlaceholder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OpeyemiSeeder extends Seeder
{
    public function run(): void
    {
        $dataFile = base_path('database/seeders/opeyemi-products.json');
        if (! is_file($dataFile)) {
            $this->command?->warn('Opeyemi product data file not found: '.$dataFile);
            return;
        }

        $data = json_decode((string) file_get_contents($dataFile), true);
        if (! is_array($data) || ! isset($data['products']) || ! is_array($data['products'])) {
            $this->command?->warn('Invalid Opeyemi product data file.');
            return;
        }

        $products = $data['products'];

        $catBySlug = [];
        foreach (Category::all() as $c) {
            $catBySlug[$c->slug] = $c->id;
        }
        $fallbackCatId = reset($catBySlug) ?: null;
        if (! $fallbackCatId) {
            $fallbackCatId = Category::create([
                'name' => 'General',
                'slug' => 'general',
                'is_active' => true,
            ])->id;
            $catBySlug['general'] = $fallbackCatId;
        }

        $mapCategory = function (string $name) use ($catBySlug, $fallbackCatId): int {
            $n = strtolower($name);
            $map = [
                'oil' => 'oils-fats', 'soya' => 'oils-fats', 'vegetable' => 'oils-fats', 'palm' => 'oils-fats',
                'rice' => 'rice',
                'semolina' => 'flour-baking', 'semo' => 'flour-baking', 'flour' => 'flour-baking',
                'milk' => 'eggs-dairy', 'egg' => 'eggs-dairy',
                'tomato' => 'tomatoes-condiments',
                'pepper' => 'pepper-mixes',
                'spice' => 'seasonings', 'seasoning' => 'seasonings',
                'sardine' => 'fish-seafood', 'fish' => 'fish-seafood', 'seafood' => 'fish-seafood',
                'noodle' => 'noodles', 'spag' => 'spaghetti', 'pasta' => 'pasta', 'macaroni' => 'macaroni',
                'cereal' => 'cereals',
                'biscuit' => 'biscuits', 'shortbread' => 'biscuits',
                'water' => 'water',
                'juice' => 'juices',
                'malt' => 'malt-drinks',
                'tea' => 'tea-coffee', 'coffee' => 'tea-coffee',
                'sugar' => 'salt-sugar', 'salt' => 'salt-sugar',
                'garri' => 'garri-flour',
                'beans' => 'beans-legumes',
                'meat' => 'fresh-meat', 'chicken' => 'fresh-meat',
                'soap' => 'cleaning', 'detergent' => 'cleaning', 'cleaning' => 'cleaning',
                'baby' => 'baby-food', 'infant' => 'infant-cereal',
            ];
            foreach ($map as $kw => $slug) {
                if (strpos($n, $kw) !== false && isset($catBySlug[$slug])) {
                    return $catBySlug[$slug];
                }
            }
            return $fallbackCatId;
        };

        $vendorUser = User::updateOrCreate(
            ['email' => 'opeyemi@foodchain.ng'],
            [
                'first_name' => 'Opeyemi',
                'last_name' => 'Foods',
                'phone' => '08000000000',
                'password' => bcrypt('password'),
                'referral_code' => strtoupper(Str::random(8)),
                'is_active' => true,
            ]
        );

        $vendor = Vendor::updateOrCreate(
            ['email' => 'opeyemi@foodchain.ng'],
            [
                'user_id' => $vendorUser->id,
                'name' => 'Opeyemi Foods and Distribution Limited',
                'slug' => 'opeyemi-foods-and-distribution-limited',
                'business_name' => 'Opeyemi Foods and Distribution Limited',
                'phone' => '08000000000',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'description' => 'Opeyemi Foods and Distribution Limited registered vendor.',
                'status' => 'approved',
                'commission_rate' => 10.0,
                'wallet_balance' => 0,
            ]
        );

        $now = now()->toDateTimeString();
        $productIds = [];

        DB::transaction(function () use ($products, $vendor, $mapCategory, $fallbackCatId, $now, &$productIds) {
            foreach ($products as $entry) {
                $name = $entry['name'] ?? '';
                if ($name === '') {
                    continue;
                }
                $num = $entry['num'] ?? 0;
                $baseSlug = $entry['slug'] ?? Str::slug($name);
                if ($baseSlug === '') {
                    $baseSlug = 'product-'.$num;
                }
                $slug = $baseSlug;
                if (Product::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$num;
                }
                $i = 0;
                while (Product::where('slug', $slug)->exists()) {
                    $i++;
                    $slug = $baseSlug.'-'.$num.'-'.$i;
                }

                $categoryId = $mapCategory($name);
                $sku = $entry['sku'] ?? ('OPEY-'.str_pad((string) $num, 4, '0', STR_PAD_LEFT));

                $product = Product::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'vendor_id' => $vendor->id,
                        'category_id' => $categoryId,
                        'brand_id' => null,
                        'name' => $name,
                        'sku' => $sku,
                        'unit' => 'pack',
                        'price' => 1000.00,
                        'member_price' => 900.00,
                        'stock_quantity' => 50,
                        'low_stock_threshold' => 5,
                        'description' => $name,
                        'is_featured' => false,
                        'is_active' => true,
                        'approval_status' => 'approved',
                        'approved_at' => now(),
                    ]
                );
                $productIds[] = $product->id;

                DB::table('marketplace_product_images')->where('product_id', $product->id)->delete();

                $rows = [];
                $sort = 0;
                $img = $entry['img'] ?? null;
                if (! empty($img)) {
                    $sort++;
                    $rows[] = [
                        'product_id' => $product->id,
                        'image_path' => $img,
                        'is_primary' => 1,
                        'is_placeholder' => 0,
                        'sort_order' => $sort,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                } else {
                    $placeholderPath = ProductPlaceholder::store($slug, $name, Category::find($categoryId)?->slug ?? 'default');
                    $sort++;
                    $rows[] = [
                        'product_id' => $product->id,
                        'image_path' => $placeholderPath,
                        'is_primary' => 1,
                        'is_placeholder' => 1,
                        'sort_order' => $sort,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($rows) {
                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table('marketplace_product_images')->insert($chunk);
                    }
                }
            }
        });
        try {
            \Illuminate\Support\Facades\Artisan::call('marketplace:sync-state-pricing');
            $this->command?->info('State pricing synced.');
        } catch (\Throwable $e) {
            $this->command?->warn('sync skipped: '.$e->getMessage());
        }

        $this->command?->info('Opeyemi Foods and Distribution Limited seeded: '.count($productIds).' products (vendor id #'.$vendor->id.')');
    }
}


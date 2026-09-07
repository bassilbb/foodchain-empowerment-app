<?php

namespace Database\Seeders;

use App\Models\Marketplace\BulkOrder;
use App\Models\Marketplace\BulkOrderContribution;
use App\Models\Marketplace\BulkOrderItem;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Coupon;
use App\Models\Marketplace\DeliveryZone;
use App\Models\Marketplace\FlashSale;
use App\Models\Marketplace\FlashSaleItem;
use App\Models\Marketplace\InventoryMovement;
use App\Models\Marketplace\LoyaltyPoint;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\OrderStatusHistory;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductImage;
use App\Models\Marketplace\Review;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\WalletTransaction;
use App\Models\MembershipTier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    /** @var array<string,int> category id lookup by slug */
    private array $catIds = [];

    /** @var array<string,int> brand id lookup by slug */
    private array $brandIds = [];

    /** @var \Illuminate\Support\Collection<int,Vendor> */
    private $vendors;

    /** @var \Illuminate\Support\Collection<int,User> */
    private $customers;

    /** @var \Illuminate\Support\Collection<int,Coupon> */
    private $coupons;

    /** @var \Illuminate\Support\Collection<int,DeliveryZone> */
    private $zones;

    private array $palette = [
        'grains' => '#b45309', 'cooking-ingredients' => '#b91c1c', 'seasonings' => '#a16207',
        'proteins' => '#9d174d', 'beverages' => '#0369a1', 'breakfast' => '#7c3aed',
        'pasta' => '#d97706', 'snacks' => '#c2410c', 'baby-food' => '#db2777',
        'household-essentials' => '#0f766e',
    ];

    public function run(): void
    {
        $this->command?->info('Seeding marketplace...');

        $this->createDeliveryZones();
        $this->createCategories();
        $this->createBrands();
        $this->createVendors();
        $this->createCustomers();
        $this->createProducts();
        $this->ensureStatePricing();
        $this->createCoupons();
        $this->createFlashSales();
        $this->createBulkOrders();
        $this->createOrdersAndReviews();

        $this->command?->info('Marketplace seeded successfully.');
    }

    // ------------------------------------------------------------------ helpers

    private function slug(string $name): string
    {
        return Str::slug($name).'-'.strtolower(Str::random(4));
    }

    private function placeholder(string $label, string $category): string
    {
        $bg = $this->palette[$category] ?? '#059669';
        $text = htmlspecialchars(mb_strtoupper(mb_substr($label, 0, 1)), ENT_QUOTES);
        $name = htmlspecialchars(Str::limit($label, 24), ENT_QUOTES);
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400">'
            .'<rect width="100%" height="100%" fill="'.$bg.'"/>'
            .'<circle cx="200" cy="200" r="140" fill="rgba(255,255,255,0.12)"/>'
            .'<text x="50%" y="45%" font-family="Arial, sans-serif" font-size="150" font-weight="bold" fill="rgba(255,255,255,0.95)" text-anchor="middle" dominant-baseline="central">'.$text.'</text>'
            .'<text x="50%" y="72%" font-family="Arial, sans-serif" font-size="22" fill="rgba(255,255,255,0.85)" text-anchor="middle">'.$name.'</text>'
            .'</svg>';

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }

    private function randomPastDate(int $daysAgo = 365): string
    {
        return now()->subDays(random_int(30, $daysAgo))->toDateString();
    }

    private function randomFutureExpiry(): ?string
    {
        if (random_int(0, 100) < 85) {
            return now()->addDays(random_int(20, 420))->toDateString();
        }

        return null;
    }

    private function uniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    // ------------------------------------------------------------------ delivery zones

    private function createDeliveryZones(): void
    {
        $zones = [
            ['Lagos Island', 'Lagos', 'Lagos Island', 2000, null, 1],
            ['Lagos Mainland', 'Lagos', 'Mainland LGAs', 1800, null, 1],
            ['Abuja FCT', 'FCT Abuja', null, 2200, null, 1],
            ['Port Harcourt', 'Rivers', 'Port Harcourt', 2500, null, 2],
            ['Ibadan', 'Oyo', 'Ibadan', 2000, null, 2],
            ['Kano Metro', 'Kano', 'Kano Municipal', 2800, null, 2],
            ['Kaduna Metro', 'Kaduna', 'Kaduna North', 2600, null, 2],
            ['Enugu Urban', 'Enugu', 'Enugu North', 2400, null, 2],
            ['Aba / Abia', 'Abia', 'Aba North', 2500, null, 2],
            ['Owerri', 'Imo', 'Owerri Municipal', 2400, null, 2],
            ['Benin City', 'Edo', 'Oredo', 2500, null, 2],
            ['Warri / Delta', 'Delta', 'Warri South', 2600, null, 2],
            ['Calabar', 'Cross River', 'Calabar Municipal', 2700, null, 2],
            ['Uyo', 'Akwa Ibom', 'Uyo', 2600, null, 2],
            ['Jos', 'Plateau', 'Jos North', 2600, null, 2],
            ['Maiduguri', 'Borno', 'Maiduguri', 3200, null, 3],
            ['Sokoto', 'Sokoto', 'Sokoto North', 3200, null, 3],
            ['Ilorin', 'Kwara', 'Ilorin West', 2600, null, 2],
            ['Abeokuta', 'Ogun', 'Abeokuta North', 2200, null, 2],
            ['Akure', 'Ondo', 'Akure South', 2400, null, 2],
            ['Asaba', 'Delta', 'Oshimili South', 2400, null, 2],
            ['Osogbo', 'Osun', 'Osogbo', 2300, null, 2],
            ['Ekiti', 'Ekiti', 'Ado-Ekiti', 2400, null, 2],
            ['Lokoja', 'Kogi', 'Lokoja', 2800, null, 3],
            ['Minna', 'Niger', 'Minna', 2800, null, 3],
            ['Makurdi', 'Benue', 'Makurdi', 2800, null, 3],
            ['Yola', 'Adamawa', 'Yola North', 3200, null, 3],
            ['Bauchi', 'Bauchi', 'Bauchi', 3000, null, 3],
            ['Gombe', 'Gombe', 'Gombe', 3000, null, 3],
            ['Yobe', 'Yobe', 'Damaturu', 3400, null, 3],
            ['Taraba', 'Taraba', 'Jalingo', 3400, null, 3],
            ['Kebbi', 'Kebbi', 'Birnin Kebbi', 3200, null, 3],
            ['Zamfara', 'Zamfara', 'Gusau', 3200, null, 3],
            ['Katsina', 'Katsina', 'Katsina', 3200, null, 3],
            ['Jigawa', 'Jigawa', 'Dutse', 3200, null, 3],
            ['Nasarawa', 'Nasarawa', 'Lafia', 2800, null, 3],
            ['Ebonyi', 'Ebonyi', 'Abakaliki', 2600, null, 2],
            ['Anambra', 'Anambra', 'Awka', 2500, null, 2],
            ['Bayelsa', 'Bayelsa', 'Yenagoa', 3000, null, 3],
            ['Kogi North', 'Kogi', 'Okene', 2900, null, 3],
        ];

        foreach ($zones as [$name, $state, $lga, $fee, $min, $days]) {
            DeliveryZone::updateOrCreate(
                ['name' => $name],
                [
                    'state' => $state,
                    'lga' => $lga,
                    'delivery_fee' => $fee,
                    'min_order_amount' => $min,
                    'estimated_days' => $days,
                    'is_active' => true,
                ]
            );
        }

        $this->zones = DeliveryZone::active()->get();
    }

    // ------------------------------------------------------------------ categories

    private function createCategories(): void
    {
        $top = [
            ['Grains', 'grains', '🌾', 'Rice, beans, tubers, garri and cereal staples'],
            ['Cooking Ingredients', 'cooking-ingredients', '🫒', 'Oils, salt, sugar, flour and cooking basics'],
            ['Seasonings', 'seasonings', '🧂', 'Cubes, spices, herbs, pepper mixes and crayfish'],
            ['Proteins', 'proteins', '🍗', 'Fresh & frozen meat, fish, eggs and dried proteins'],
            ['Beverages', 'beverages', '🥤', 'Soft drinks, juices, water, malt and tea & coffee'],
            ['Breakfast', 'breakfast', '🍞', 'Cereals, spreads, oats, pancakes and bakery'],
            ['Pasta & Noodles', 'pasta', '🍝', 'Spaghetti, macaroni, noodles and other pasta'],
            ['Snacks', 'snacks', '🍪', 'Biscuits, chips, nuts and sweets'],
            ['Baby Food', 'baby-food', '🍼', 'Infant cereal, formula, baby snacks and juice'],
            ['Household Essentials', 'household-essentials', '🧼', 'Cleaning, laundry, kitchen and paper products'],
        ];

        $children = [
            'grains' => [
                ['Rice', 'rice'], ['Beans & Legumes', 'beans-legumes'], ['Tubers', 'tubers'],
                ['Garri & Flour', 'garri-flour'], ['Other Grains & Cereals', 'grains-cereals'],
            ],
            'cooking-ingredients' => [
                ['Oils & Fats', 'oils-fats'], ['Salt & Sugar', 'salt-sugar'],
                ['Flour & Baking', 'flour-baking'], ['Tomatoes & Condiments', 'tomatoes-condiments'],
            ],
            'seasonings' => [
                ['Cubes & Bouillon', 'cubes-bouillon'], ['Spices & Herbs', 'spices-herbs'],
                ['Pepper Mixes', 'pepper-mixes'], ['Crayfish & Dried', 'crayfish-dried'],
            ],
            'proteins' => [
                ['Fresh Meat', 'fresh-meat'], ['Frozen Meat', 'frozen-meat'],
                ['Fish & Seafood', 'fish-seafood'], ['Eggs & Dairy', 'eggs-dairy'],
                ['Dried Proteins', 'dried-proteins'],
            ],
            'beverages' => [
                ['Soft Drinks', 'soft-drinks'], ['Juices', 'juices'], ['Water', 'water'],
                ['Malt Drinks', 'malt-drinks'], ['Tea & Coffee', 'tea-coffee'],
            ],
            'breakfast' => [
                ['Cereals', 'cereals'], ['Spreads', 'spreads'],
                ['Oats & Pancakes', 'oats-pancakes'], ['Bread & Bakery', 'bread-bakery'],
            ],
            'pasta' => [
                ['Spaghetti', 'spaghetti'], ['Macaroni', 'macaroni'],
                ['Noodles', 'noodles'], ['Other Pasta', 'other-pasta'],
            ],
            'snacks' => [
                ['Biscuits', 'biscuits'], ['Chips & Crisps', 'chips-crisps'],
                ['Nuts & Dried', 'nuts-dried'], ['Sweets & Chocolates', 'sweets-chocolates'],
            ],
            'baby-food' => [
                ['Infant Cereal', 'infant-cereal'], ['Formula', 'formula'],
                ['Baby Snacks', 'baby-snacks'], ['Baby Juice', 'baby-juice'],
            ],
            'household-essentials' => [
                ['Cleaning', 'cleaning'], ['Laundry', 'laundry'],
                ['Kitchen Items', 'kitchen-items'], ['Paper Products', 'paper-products'],
            ],
        ];

        DB::transaction(function () use ($top, $children) {
            foreach ($top as [$name, $slug, $icon, $desc]) {
                $parent = Category::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'icon' => $icon,
                        'description' => $desc,
                        'is_active' => true,
                        'sort_order' => count($this->catIds) + 1,
                    ]
                );
                $this->catIds[$slug] = $parent->id;

                foreach ($children[$slug] as $i => [$childName, $childSlug]) {
                    $child = Category::updateOrCreate(
                        ['slug' => $childSlug],
                        [
                            'name' => $childName,
                            'parent_id' => $parent->id,
                            'is_active' => true,
                            'sort_order' => $i + 1,
                        ]
                    );
                    $this->catIds[$childSlug] = $child->id;
                }
            }
        });
    }

    // ------------------------------------------------------------------ brands

    private function createBrands(): void
    {
        $brands = [
            ['Golden Penny', 'golden-penny', 'Nigeria', 'Flour, sugar, pasta and semovita'],
            ['Dangote', 'dangote', 'Nigeria', 'Salt, sugar, flour, pasta and noodles'],
            ['Indomie', 'indomie', 'Nigeria', 'Instant noodles by Dufil Prima Foods'],
            ['Nestlé', 'nestle', 'Nigeria', 'Milo, Cerelac, Maggi and Nescafé'],
            ['BUA', 'bua', 'Nigeria', 'Sugar, flour, rice and pasta'],
            ['Olam', 'olam', 'Nigeria', 'Premium rice and food commodities'],
            ['Honeywell', 'honeywell', 'Nigeria', 'Flour, semovita and wheat products'],
            ['Fan Milk', 'fan-milk', 'Nigeria', 'Frozen treats and dairy drinks'],
            ['Chivita', 'chivita', 'Nigeria', 'Fruit juices and drinks'],
            ['Grand Cereals', 'grand-cereals', 'Nigeria', 'Cereals, custard and animal feeds'],
            ['Saca', 'saca', 'Nigeria', 'Biscuits and bakery snacks'],
            ['Cadbury', 'cadbury', 'Nigeria', 'Bournvita, Tom Tom and biscuits'],
            ['Unilever', 'unilever', 'Nigeria', 'Knorr, Lipton and Royco'],
            ['Peak', 'peak', 'Nigeria', 'Dairy products by FrieslandCampina'],
            ['NASCO', 'nasco', 'Nigeria', 'Biscuits, cornflakes and juices'],
            ['JOF Ideal', 'jof-ideal', 'Nigeria', 'Parboiled rice and grains'],
            ['Rite Foods', 'rite-foods', 'Nigeria', 'Bigi drinks, Fearless energy drink'],
            ['Power Oil', 'power-oil', 'Nigeria', 'Vegetable oils and fats'],
            ['Mamador', 'mamador', 'Nigeria', 'Oils, margarine and cooking essentials'],
            ['Olam Rice', 'olam-rice', 'Nigeria', 'Premium rice brands'],
        ];

        foreach ($brands as [$name, $slug, $country, $desc]) {
            \App\Models\Marketplace\Brand::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'country' => $country,
                    'description' => $desc,
                    'is_active' => true,
                ]
            );
        }

        $this->brandIds = \App\Models\Marketplace\Brand::pluck('id', 'slug')->toArray();
    }

    // ------------------------------------------------------------------ vendors

    private function createVendors(): void
    {
        $data = [
            ['Prime Foods NG', 'Farmgate Grocers', 'Lagos', 'Lagos', 'Specialist in rice, grains and staples'],
            ['Green Harvest Foods', 'Green Harvest', 'Ibadan', 'Oyo', 'Fresh produce and tubers'],
            ['Ocean Fresh', 'Ocean Fresh Seafood', 'Lagos', 'Lagos', 'Frozen fish, seafood and protein'],
            ['Naija Protein Hub', 'Naija Protein Hub', 'Port Harcourt', 'Rivers', 'Fresh and frozen meats'],
            ['Sweet Mornings', 'Sweet Mornings Bakery', 'Abeokuta', 'Ogun', 'Bakery, cereals and breakfast'],
            ['Savour Seasoning Co', 'Savour Seasonings', 'Kano', 'Kano', 'Spices, cubes and seasonings'],
            ['Golden Oils', 'Golden Oils', 'Kaduna', 'Kaduna', 'Vegetable oils and fats'],
            ['Pasta Villa', 'Pasta Villa', 'Kano', 'Kano', 'Pasta, spaghetti and noodles'],
            ['Crunch & Munch', 'Crunch & Munch Snacks', 'Lagos', 'Lagos', 'Biscuits, chips and snacks'],
            ['Little Bites', 'Little Bites Baby', 'Enugu', 'Enugu', 'Baby food and infant nutrition'],
            ['Fresh & Clean', 'Fresh & Clean Home', 'Ikeja', 'Lagos', 'Household cleaning essentials'],
            ['Daily Bread', 'Daily Bread Foods', 'Abuja', 'FCT Abuja', 'Grains and household staples'],
            ['Dairy Delight', 'Dairy Delight', 'Ilorin', 'Kwara', 'Dairy, eggs and beverages'],
            ['Savannah Mills', 'Savannah Mills', 'Jos', 'Plateau', 'Flour, semovita and baking'],
            ['Market Fresh', 'Market Fresh Produce', 'Onitsha', 'Anambra', 'Fresh vegetables and produce'],
        ];

        $vendorUser = User::updateOrCreate(
            ['email' => 'vendor@foodchain.ng'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Vendor',
                'phone' => '08001112222',
                'password' => bcrypt('password'),
                'referral_code' => $this->uniqueReferralCode(),
                'is_active' => true,
            ]
        );

        foreach ($data as $i => [$name, $business, $city, $state, $desc]) {
            $vendorEmail = strtolower(preg_replace('/[^a-z0-9]/', '', Str::slug($name))).'@foodchain.ng';
            $vendor = Vendor::updateOrCreate(
                ['email' => $vendorEmail],
                [
                    'user_id' => $i === 0 ? $vendorUser->id : null,
                    'name' => $name,
                    'slug' => $this->slug($name),
                    'business_name' => $business,
                    'phone' => '080'.random_int(10000000, 99999999),
                    'city' => $city,
                    'state' => $state,
                    'description' => $desc,
                    'status' => 'approved',
                    'commission_rate' => 10.0,
                    'wallet_balance' => random_int(50000, 1500000),
                ]
            );
            $vendors[] = $vendor;
        }

        $this->vendors = collect($vendors);
    }

    // ------------------------------------------------------------------ customers

    private function createCustomers(): void
    {
        $firstNames = ['Chioma', 'Adebayo', 'Fatima', 'Emeka', 'Ngozi', 'Tunde', 'Aisha', 'Kunle', 'Halima', 'Chinedu', 'Blessing', 'Ibrahim', 'Yetunde', 'Chuka', 'Zainab', 'Oluwaseun', 'Ifeoma', 'Sadiq', 'Amara', 'Femi', 'Nneka', 'Musa', 'Folake', 'Kehinde', 'Rahmat', 'Segun', 'Adaeze', 'Umar', 'Bimpe', 'Chidi', 'Hauwa', 'Dayo', 'Kemi', 'Sani', 'Funke', 'Obinna', 'Maryam', 'Rotimi', 'Uche', 'Bashir'];
        $lastNames = ['Okafor', 'Adeyemi', 'Mohammed', 'Nwosu', 'Balogun', 'Abubakar', 'Obi', 'Afolabi', 'Yusuf', 'Eze', 'Okon', 'Lawal', 'Ogundipe', 'Okafor', 'Bello', 'Adebayo', 'Nwachukwu', 'Danjuma', 'Oyelaran', 'Kalu', 'Umeh', 'Garba', 'Sowande', 'Olayemi', 'Abdullahi', 'Osunlana', 'Ekwueme', 'Ibrahim', 'Ogunleye', 'Nwankwo', 'Adamu', 'Ogunbiyi', 'Adepoju', 'Rufai', 'Adeola', 'Onyema', 'Suleiman', 'Oyelaran', 'Chukwu', 'Bamgbose'];

        $tierIds = MembershipTier::pluck('id', 'slug')->toArray();
        $tierSlugs = array_keys($tierIds);
        $allUsers = [];

        $demo = User::updateOrCreate(
            ['email' => 'customer@foodchain.ng'],
            [
                'first_name' => 'Chioma',
                'last_name' => 'Okafor',
                'phone' => '08011223344',
                'password' => bcrypt('password'),
                'referral_code' => $this->uniqueReferralCode(),
                'current_tier_id' => $tierIds['gold'] ?? null,
                'wallet_balance' => 150000,
                'loyalty_points_balance' => 1200,
                'membership_expires_at' => now()->addYear(),
                'membership_started_at' => now()->subMonths(8),
                'address' => '12 Admiralty Way, Lekki Phase 1',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'country' => 'Nigeria',
                'is_active' => true,
            ]
        );
        $allUsers[] = $demo;

        $usedEmails = collect([$demo->email]);

        for ($i = 0; $i < 49; $i++) {
            $first = $firstNames[$i % count($firstNames)];
            $last = $lastNames[($i * 7 + 3) % count($lastNames)];
            $email = strtolower($first.'.'.$last.$i).'@foodchain.ng';

            if ($usedEmails->contains($email)) {
                continue;
            }
            $usedEmails->push($email);

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'first_name' => $first,
                    'last_name' => $last,
                    'phone' => '080'.random_int(10000000, 99999999),
                    'password' => bcrypt('password'),
                    'referral_code' => $this->uniqueReferralCode(),
                    'current_tier_id' => $tierIds[$tierSlugs[array_rand($tierSlugs)]] ?? null,
                    'wallet_balance' => random_int(20000, 400000),
                    'loyalty_points_balance' => random_int(0, 2000),
                    'membership_expires_at' => now()->addDays(random_int(60, 400)),
                    'membership_started_at' => now()->subMonths(random_int(1, 12)),
                    'address' => random_int(1, 200).' '.$first.' Street',
                    'city' => ['Lagos', 'Abuja', 'Ibadan', 'Port Harcourt', 'Enugu', 'Kano'][array_rand(['Lagos', 'Abuja', 'Ibadan', 'Port Harcourt', 'Enugu', 'Kano'])],
                    'state' => 'Lagos',
                    'country' => 'Nigeria',
                    'is_active' => true,
                ]
            );

            $allUsers[] = $user;
        }

        $this->customers = collect($allUsers);
    }

    // ------------------------------------------------------------------ products

    private function createProducts(): void
    {
        $products = $this->productCatalog();

        $this->vendors = $this->vendors->isEmpty() ? Vendor::approved()->get() : $this->vendors;

        DB::transaction(function () use ($products) {
            $vendorCount = $this->vendors->count();

            foreach ($products as $idx => [$name, $cat, $brand, $price, $unit, $weight, $weightUnit, $stock, $discountPct, $featured]) {
                $catId = $this->catIds[$cat] ?? null;
                if (! $catId) {
                    continue;
                }

                $discountPrice = $discountPct ? round($price * (1 - $discountPct / 100), 2) : null;
                $memberPrice = $price * 0.9;

                $product = Product::updateOrCreate(
                    ['name' => $name],
                    [
                        'vendor_id' => $this->vendors[$idx % $vendorCount]->id,
                        'category_id' => $catId,
                        'brand_id' => $this->brandIds[$brand] ?? null,
                        'slug' => $this->slug($name),
                        'sku' => Product::generateSku(),
                        'unit' => $unit,
                        'weight' => $weight,
                        'weight_unit' => $weightUnit,
                        'price' => $price,
                        'discount_price' => $discountPrice,
                        'member_price' => $memberPrice,
                        'stock_quantity' => $stock,
                        'low_stock_threshold' => 10,
                        'description' => 'Premium '.$name.' — a trusted '.$unit.' choice for your home, sourced fresh for the FoodChain marketplace.',
                        'ingredients' => $this->ingredientsFor($cat),
                        'nutritional_info' => null,
                        'storage_instructions' => 'Store in a cool, dry place away from direct sunlight.',
                        'expiry_date' => $this->randomFutureExpiry(),
                        'manufacturing_date' => $this->randomPastDate(),
                        'country_of_origin' => 'Nigeria',
                        'is_featured' => $featured,
                        'is_active' => true,
                    ]
                );

                if (! $product->images()->exists()) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $this->placeholder($name, $cat),
                        'is_primary' => true,
                        'sort_order' => 0,
                    ]);
                }

                if ($featured && $product->images()->where('is_primary', false)->doesntExist()) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $this->placeholder($name, $cat),
                        'is_primary' => false,
                        'sort_order' => 1,
                    ]);
                }

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'vendor_id' => $product->vendor_id,
                    'type' => 'stock_in',
                    'quantity' => $stock,
                    'batch_number' => 'BATCH-'.strtoupper(substr(md5($product->id), 0, 6)),
                    'reason' => 'Initial stock from seeder',
                ]);
            }
        });
    }

    private function ingredientsFor(string $cat): ?string
    {
        return match ($cat) {
            'grains', 'rice', 'beans-legumes', 'garri-flour', 'grains-cereals', 'tubers' => '100% natural grain / produce. No artificial preservatives.',
            'cooking-ingredients', 'oils-fats', 'salt-sugar', 'flour-baking' => 'Quality controlled food ingredients, produced to standard.',
            'seasonings', 'cubes-bouillon', 'spices-herbs', 'pepper-mixes', 'crayfish-dried' => 'A blend of selected natural spices and seasonings.',
            'proteins', 'fresh-meat', 'frozen-meat', 'fish-seafood', 'eggs-dairy', 'dried-proteins' => 'Premium protein source, hygienically handled and packaged.',
            'beverages', 'soft-drinks', 'juices', 'water', 'malt-drinks', 'tea-coffee' => 'Purified ingredients, sealed for freshness.',
            'baby-food', 'infant-cereal', 'formula', 'baby-snacks', 'baby-juice' => 'Specially formulated for infants. Follow preparation instructions.',
            default => null,
        };
    }

    private function productCatalog(): array
    {
        // [name, category-slug, brand-slug, price, unit, weight, weight-unit, stock, discount%, featured]
        return [
            // ---------------- Grains ----------------
            ['Royal Stallion Rice 50kg', 'rice', 'olam-rice', 98000, 'bag', 50, 'kg', 40, null, true],
            ['Mama Gold Rice 25kg', 'rice', 'olam-rice', 50000, 'bag', 25, 'kg', 60, null, false],
            ['Caprice Rice 10kg', 'rice', 'olam-rice', 21000, 'bag', 10, 'kg', 80, null, false],
            ['Ofada Rice 5kg', 'rice', 'jof-ideal', 12000, 'bag', 5, 'kg', 50, null, false],
            ['Golden Penny Rice 1kg', 'rice', 'golden-penny', 2500, 'pack', 1, 'kg', 200, null, false],
            ['Uncle Sam Rice 5kg', 'rice', 'jof-ideal', 12500, 'bag', 5, 'kg', 90, 10, false],
            ['Big Bull Rice 25kg', 'rice', 'jof-ideal', 48500, 'bag', 25, 'kg', 45, null, false],
            ['Local Ofada Rice 2kg', 'rice', 'jof-ideal', 5200, 'pack', 2, 'kg', 70, null, false],
            ['BUA Parboiled Rice 10kg', 'rice', 'bua', 21500, 'bag', 10, 'kg', 65, null, false],
            ['Abakaliki Local Rice 3kg', 'rice', 'jof-ideal', 7800, 'pack', 3, 'kg', 55, null, false],
            ['Brown Beans 5kg', 'beans-legumes', 'dangote', 11500, 'bag', 5, 'kg', 60, null, false],
            ['White Beans (Ewa) 1kg', 'beans-legumes', 'dangote', 2400, 'pack', 1, 'kg', 150, null, false],
            ['Red Beans 2kg', 'beans-legumes', 'dangote', 5200, 'pack', 2, 'kg', 90, null, false],
            ['Black-Eyed Peas 1kg', 'beans-legumes', 'dangote', 2600, 'pack', 1, 'kg', 120, null, false],
            ['Honey Beans 5kg', 'beans-legumes', 'dangote', 13000, 'bag', 5, 'kg', 55, null, false],
            ['Soybeans 1kg', 'beans-legumes', 'olam', 1900, 'pack', 1, 'kg', 100, null, false],
            ['Bambara Groundnut 1kg', 'beans-legumes', 'olam', 2800, 'pack', 1, 'kg', 40, null, false],
            ['Nigerian Yam (Tuber)', 'tubers', 'olam', 3200, 'tuber', 3, 'kg', 120, null, false],
            ['Yam 5kg Bag', 'tubers', 'olam', 15000, 'bag', 5, 'kg', 60, null, false],
            ['Irish Potatoes 1kg', 'tubers', 'olam', 2200, 'kg', 1, 'kg', 130, null, false],
            ['Sweet Potatoes 2kg', 'tubers', 'olam', 3000, 'bag', 2, 'kg', 80, null, false],
            ['Cocoyam 1kg', 'tubers', 'olam', 1500, 'kg', 1, 'kg', 90, null, false],
            ['Water Yam 1kg', 'tubers', 'olam', 1300, 'kg', 1, 'kg', 60, null, false],
            ['Plantain Bunch (4-6 pcs)', 'tubers', 'olam', 3800, 'bunch', 5, 'kg', 70, null, true],
            ['Eba (Yellow Garri) 25kg', 'garri-flour', 'golden-penny', 36000, 'bag', 25, 'kg', 50, null, false],
            ['White Garri 10kg', 'garri-flour', 'golden-penny', 15000, 'bag', 10, 'kg', 80, null, false],
            ['Garri 1kg', 'garri-flour', 'golden-penny', 1600, 'pack', 1, 'kg', 200, null, true],
            ['Semovita 10kg', 'garri-flour', 'golden-penny', 26000, 'bag', 10, 'kg', 55, null, false],
            ['Golden Penny Semovita 1kg', 'garri-flour', 'golden-penny', 2800, 'pack', 1, 'kg', 120, null, false],
            ['Golden Penny Wheat Flour 2kg', 'garri-flour', 'golden-penny', 4500, 'pack', 2, 'kg', 110, null, false],
            ['Corn Flour 1kg', 'garri-flour', 'honeywell', 2200, 'pack', 1, 'kg', 100, null, false],
            ['Pounded Yam Flour 2kg', 'garri-flour', 'honeywell', 7000, 'pack', 2, 'kg', 90, null, false],
            ['Maize (Corn) 5kg', 'grains-cereals', 'olam', 8500, 'bag', 5, 'kg', 60, null, false],
            ['Guinea Corn (Sorghum) 2kg', 'grains-cereals', 'olam', 3000, 'pack', 2, 'kg', 80, null, false],
            ['Millet 2kg', 'grains-cereals', 'olam', 3200, 'pack', 2, 'kg', 70, null, false],
            ['Acha (Fonio) 1kg', 'grains-cereals', 'olam', 3500, 'pack', 1, 'kg', 60, null, false],
            ['Quinoa 500g', 'grains-cereals', 'nestle', 5500, 'pack', 0.5, 'kg', 40, null, false],
            ['Brown Rice 2kg', 'grains-cereals', 'jof-ideal', 6500, 'pack', 2, 'kg', 90, null, false],
            ['Oats (Whole) 1kg', 'grains-cereals', 'nestle', 4500, 'pack', 1, 'kg', 85, null, false],

            // ---------------- Cooking Ingredients ----------------
            ['Power Oil Vegetable Oil 5L', 'oils-fats', 'power-oil', 21000, 'bottle', 5, 'L', 60, null, false],
            ['Mamador Vegetable Oil 1L', 'oils-fats', 'mamador', 4800, 'bottle', 1, 'L', 120, null, false],
            ['Dangote Vegetable Oil 1L', 'oils-fats', 'dangote', 4500, 'bottle', 1, 'L', 140, null, true],
            ['Kings Vegetable Oil 3L', 'oils-fats', 'power-oil', 13000, 'bottle', 3, 'L', 80, null, false],
            ['Devon Palm Oil 1L', 'oils-fats', 'power-oil', 3900, 'bottle', 1, 'L', 130, null, false],
            ['Aya Palm Oil 5L', 'oils-fats', 'power-oil', 18000, 'bottle', 5, 'L', 55, null, false],
            ['Olive Oil 500ml', 'oils-fats', 'mamador', 8500, 'bottle', 0.5, 'L', 45, null, false],
            ['Groundnut Oil 1L', 'oils-fats', 'mamador', 5000, 'bottle', 1, 'L', 90, null, false],
            ['Dangote Salt 1kg', 'salt-sugar', 'dangote', 1000, 'pack', 1, 'kg', 250, null, false],
            ['Dangote Sugar 5kg', 'salt-sugar', 'dangote', 11500, 'bag', 5, 'kg', 70, null, true],
            ['Dangote Sugar 1kg', 'salt-sugar', 'dangote', 2400, 'pack', 1, 'kg', 180, null, false],
            ['Golden Penny Sugar 50kg', 'salt-sugar', 'golden-penny', 105000, 'bag', 50, 'kg', 25, null, false],
            ['Dangote Salt 500g', 'salt-sugar', 'dangote', 550, 'pack', 0.5, 'kg', 300, null, false],
            ['Raw Sugar 1kg', 'salt-sugar', 'bua', 2200, 'pack', 1, 'kg', 90, null, false],
            ['Sea Salt 500g', 'salt-sugar', 'dangote', 2500, 'pack', 0.5, 'kg', 60, null, false],
            ['Dangote Sugar 500g', 'salt-sugar', 'dangote', 1300, 'pack', 0.5, 'kg', 200, null, false],
            ['BUA Refined Sugar 5kg', 'salt-sugar', 'bua', 11200, 'bag', 5, 'kg', 65, null, false],
            ['Golden Penny Flour 5kg', 'flour-baking', 'golden-penny', 10500, 'bag', 5, 'kg', 70, null, false],
            ['Golden Penny Flour 1kg', 'flour-baking', 'golden-penny', 2300, 'pack', 1, 'kg', 160, null, true],
            ['Bakers Flour 10kg', 'flour-baking', 'honeywell', 21000, 'bag', 10, 'kg', 50, null, false],
            ['Cake Flour 1kg', 'flour-baking', 'honeywell', 3200, 'pack', 1, 'kg', 90, null, false],
            ['Baking Powder 100g', 'flour-baking', 'honeywell', 700, 'pack', 0.1, 'kg', 220, null, false],
            ['Instant Yeast 500g', 'flour-baking', 'honeywell', 6500, 'pack', 0.5, 'kg', 55, null, false],
            ['Custard Powder 500g', 'flour-baking', 'grand-cereals', 3000, 'pack', 0.5, 'kg', 110, null, false],
            ['Gino Tomato Paste 400g', 'tomatoes-condiments', 'nestle', 2800, 'tin', 0.4, 'kg', 150, null, false],
            ['Sonnet Tomato Stew 1kg', 'tomatoes-condiments', 'nestle', 3500, 'pack', 1, 'kg', 120, null, true],
            ['De Rica Tomato Paste 400g', 'tomatoes-condiments', 'nestle', 3000, 'tin', 0.4, 'kg', 140, null, false],
            ['Heinz Ketchup 400g', 'tomatoes-condiments', 'nestle', 3200, 'bottle', 0.4, 'kg', 90, null, false],
            ['Fresh Tomato Puree 500g', 'tomatoes-condiments', 'nestle', 2500, 'pack', 0.5, 'kg', 100, null, false],
            ['Peak Mayonnaise 400g', 'tomatoes-condiments', 'peak', 4500, 'jar', 0.4, 'kg', 70, null, false],
            ['Peak Salad Cream 400g', 'tomatoes-condiments', 'peak', 3800, 'jar', 0.4, 'kg', 65, null, false],

            // ---------------- Seasonings ----------------
            ['Maggi Cubes (4 x 8 packs)', 'cubes-bouillon', 'nestle', 2800, 'pack', 0.5, 'kg', 180, null, true],
            ['Knorr Cubes (4 x 8 packs)', 'cubes-bouillon', 'unilever', 2900, 'pack', 0.5, 'kg', 170, null, false],
            ['Maggi Cubes 6pcs', 'cubes-bouillon', 'nestle', 500, 'pack', 0.06, 'kg', 400, null, false],
            ['Royco Cubes', 'cubes-bouillon', 'unilever', 480, 'pack', 0.06, 'kg', 380, null, false],
            ['Oyster Sauce 500ml', 'cubes-bouillon', 'nestle', 2500, 'bottle', 0.5, 'L', 60, null, false],
            ['Soy Sauce 500ml', 'cubes-bouillon', 'nestle', 2200, 'bottle', 0.5, 'L', 70, null, false],
            ['Maggi Jumbo 1kg', 'cubes-bouillon', 'nestle', 3500, 'pack', 1, 'kg', 85, null, false],
            ['Curry Powder 250g', 'spices-herbs', 'savour-seasoning', 900, 'pack', 0.25, 'kg', 180, null, false],
            ['Thyme 200g', 'spices-herbs', 'savour-seasoning', 800, 'pack', 0.2, 'kg', 200, null, false],
            ['Nutmeg 100g', 'spices-herbs', 'savour-seasoning', 1600, 'pack', 0.1, 'kg', 120, null, false],
            ['Ginger Powder 200g', 'spices-herbs', 'savour-seasoning', 1200, 'pack', 0.2, 'kg', 150, null, false],
            ['Garlic Powder 200g', 'spices-herbs', 'savour-seasoning', 1300, 'pack', 0.2, 'kg', 150, null, false],
            ['Onion Powder 200g', 'spices-herbs', 'savour-seasoning', 1400, 'pack', 0.2, 'kg', 140, null, false],
            ['Black Pepper 200g', 'spices-herbs', 'savour-seasoning', 1500, 'pack', 0.2, 'kg', 130, null, false],
            ['Paprika 200g', 'spices-herbs', 'savour-seasoning', 1600, 'pack', 0.2, 'kg', 110, null, false],
            ['Fresh Pepper Mix 1kg', 'pepper-mixes', 'savour-seasoning', 3000, 'pack', 1, 'kg', 160, null, true],
            ['Ata Rodo (Scotch Bonnet) 500g', 'pepper-mixes', 'savour-seasoning', 1800, 'pack', 0.5, 'kg', 150, null, false],
            ['Dry Pepper 500g', 'pepper-mixes', 'savour-seasoning', 2500, 'pack', 0.5, 'kg', 90, null, false],
            ['Shombo 500g', 'pepper-mixes', 'savour-seasoning', 1500, 'pack', 0.5, 'kg', 130, null, false],
            ['Chili Flakes 200g', 'pepper-mixes', 'savour-seasoning', 1800, 'pack', 0.2, 'kg', 80, null, false],
            ['Tatashe (Bell Pepper) 1kg', 'pepper-mixes', 'savour-seasoning', 3200, 'pack', 1, 'kg', 60, null, false],
            ['Crayfish 500g', 'crayfish-dried', 'savour-seasoning', 3800, 'pack', 0.5, 'kg', 140, null, true],
            ['Crayfish Powder 250g', 'crayfish-dried', 'savour-seasoning', 2200, 'pack', 0.25, 'kg', 160, null, false],
            ['Smoked Panla 500g', 'crayfish-dried', 'savour-seasoning', 4500, 'pack', 0.5, 'kg', 70, null, false],
            ['Stockfish 500g', 'crayfish-dried', 'savour-seasoning', 8000, 'pack', 0.5, 'kg', 45, null, false],
            ['Dried Catfish 500g', 'crayfish-dried', 'savour-seasoning', 5500, 'pack', 0.5, 'kg', 50, null, false],

            // ---------------- Proteins ----------------
            ['Fresh Beef 1kg', 'fresh-meat', 'naija-protein-hub', 6500, 'kg', 1, 'kg', 80, null, false],
            ['Goat Meat 1kg', 'fresh-meat', 'naija-protein-hub', 7500, 'kg', 1, 'kg', 60, null, false],
            ['Whole Fresh Chicken 1kg', 'fresh-meat', 'naija-protein-hub', 7000, 'kg', 1, 'kg', 90, null, false],
            ['Cow Liver 1kg', 'fresh-meat', 'naija-protein-hub', 5500, 'kg', 1, 'kg', 50, null, false],
            ['Cow Tail 1kg', 'fresh-meat', 'naija-protein-hub', 7000, 'kg', 1, 'kg', 40, null, false],
            ['Fresh Chicken Feet 1kg', 'fresh-meat', 'naija-protein-hub', 3500, 'kg', 1, 'kg', 55, null, false],
            ['Frozen Whole Chicken 1.2kg', 'frozen-meat', 'naija-protein-hub', 9500, 'pack', 1.2, 'kg', 120, null, true],
            ['Frozen Chicken Wings 1kg', 'frozen-meat', 'naija-protein-hub', 6500, 'pack', 1, 'kg', 100, null, false],
            ['Frozen Drumsticks 1kg', 'frozen-meat', 'naija-protein-hub', 6800, 'pack', 1, 'kg', 90, null, false],
            ['Frozen Turkey 2kg', 'frozen-meat', 'naija-protein-hub', 16000, 'pack', 2, 'kg', 60, null, false],
            ['Frozen Beef Offal 1kg', 'frozen-meat', 'naija-protein-hub', 4500, 'pack', 1, 'kg', 70, null, false],
            ['Frozen Chicken Gizzard 1kg', 'frozen-meat', 'naija-protein-hub', 5500, 'pack', 1, 'kg', 75, null, false],
            ['Frozen Titus Fish 1kg', 'fish-seafood', 'ocean-fresh', 3800, 'kg', 1, 'kg', 130, null, true],
            ['Frozen Mackerel 1kg', 'fish-seafood', 'ocean-fresh', 3400, 'kg', 1, 'kg', 120, null, false],
            ['Frozen Croaker 1kg', 'fish-seafood', 'ocean-fresh', 4200, 'kg', 1, 'kg', 90, null, false],
            ['Fresh Catfish 1kg', 'fish-seafood', 'ocean-fresh', 4800, 'kg', 1, 'kg', 70, null, false],
            ['Frozen Prawns 1kg', 'fish-seafood', 'ocean-fresh', 12000, 'kg', 1, 'kg', 40, null, false],
            ['Smoked Fish 500g', 'fish-seafood', 'ocean-fresh', 4800, 'pack', 0.5, 'kg', 60, null, false],
            ['Canned Sardines (3 tins)', 'fish-seafood', 'ocean-fresh', 2400, 'pack', 0.9, 'kg', 110, null, false],
            ['Fresh Eggs (Crate of 30)', 'eggs-dairy', 'dairy-delight', 5500, 'crate', 2, 'kg', 150, null, true],
            ['Eggs (12 pcs)', 'eggs-dairy', 'dairy-delight', 2400, 'pack', 0.8, 'kg', 200, null, false],
            ['Peak Evaporated Milk 170g', 'eggs-dairy', 'peak', 1300, 'tin', 0.17, 'kg', 250, null, false],
            ['Peak Powdered Milk 400g', 'eggs-dairy', 'peak', 9500, 'pack', 0.4, 'kg', 120, null, false],
            ['Nido Milk 400g', 'eggs-dairy', 'nestle', 8500, 'pack', 0.4, 'kg', 110, null, false],
            ['Butter 200g', 'eggs-dairy', 'peak', 3200, 'pack', 0.2, 'kg', 80, null, false],
            ['Raw Groundnut 1kg', 'dried-proteins', 'olam', 2400, 'kg', 1, 'kg', 100, null, false],
            ['Suya-Spiced Beef 500g', 'dried-proteins', 'naija-protein-hub', 4500, 'pack', 0.5, 'kg', 60, null, false],
            ['Kpomo (Cow Skin) 1kg', 'dried-proteins', 'naija-protein-hub', 3000, 'kg', 1, 'kg', 80, null, false],
            ['Dry Offal 500g', 'dried-proteins', 'naija-protein-hub', 3500, 'pack', 0.5, 'kg', 55, null, false],
            ['Washed Meat 500g', 'dried-proteins', 'naija-protein-hub', 3800, 'pack', 0.5, 'kg', 50, null, false],

            // ---------------- Beverages ----------------
            ['Coca-Cola 50cl', 'soft-drinks', 'rite-foods', 350, 'bottle', 0.5, 'L', 400, null, false],
            ['Fanta 50cl', 'soft-drinks', 'rite-foods', 350, 'bottle', 0.5, 'L', 400, null, false],
            ['Sprite 50cl', 'soft-drinks', 'rite-foods', 350, 'bottle', 0.5, 'L', 400, null, false],
            ['Coca-Cola 1.5L', 'soft-drinks', 'rite-foods', 1200, 'bottle', 1.5, 'L', 150, null, false],
            ['Schweppes 50cl', 'soft-drinks', 'rite-foods', 350, 'bottle', 0.5, 'L', 200, null, false],
            ['Assorted Soft Drink Crate', 'soft-drinks', 'rite-foods', 9000, 'crate', 12, 'L', 40, null, false],
            ['Chivita 100% 1L', 'juices', 'chivita', 1500, 'pack', 1, 'L', 180, null, true],
            ['Chi Exotic 1L', 'juices', 'chivita', 1800, 'pack', 1, 'L', 160, null, false],
            ['Ribena 1L', 'juices', 'chivita', 3200, 'bottle', 1, 'L', 90, null, false],
            ['Five Alive 1L', 'juices', 'chivita', 2800, 'bottle', 1, 'L', 80, null, false],
            ['Happy Hour 1L', 'juices', 'chivita', 1500, 'pack', 1, 'L', 130, null, false],
            ['Viju Milk 1L', 'juices', 'fan-milk', 2200, 'bottle', 1, 'L', 100, null, false],
            ['Fresh Smoothie 500ml', 'juices', 'chivita', 2500, 'bottle', 0.5, 'L', 60, null, false],
            ['Eva Water 75cl', 'water', 'nestle', 500, 'bottle', 0.75, 'L', 300, null, false],
            ['Table Water 1.5L', 'water', 'rite-foods', 700, 'bottle', 1.5, 'L', 200, null, false],
            ['Sachet Water (Bag of 20)', 'water', 'rite-foods', 650, 'bag', 6, 'L', 180, null, false],
            ['Dasani 50cl', 'water', 'rite-foods', 450, 'bottle', 0.5, 'L', 250, null, false],
            ['Nestle Pure Life 1L', 'water', 'nestle', 600, 'bottle', 1, 'L', 220, null, false],
            ['Maltina 50cl', 'malt-drinks', 'rite-foods', 600, 'bottle', 0.5, 'L', 180, null, false],
            ['Hi-Malt 50cl', 'malt-drinks', 'rite-foods', 600, 'bottle', 0.5, 'L', 170, null, false],
            ['Guinness Malta 50cl', 'malt-drinks', 'rite-foods', 700, 'bottle', 0.5, 'L', 160, null, false],
            ['Amstel Malta 50cl', 'malt-drinks', 'rite-foods', 650, 'bottle', 0.5, 'L', 150, null, false],
            ['Lipton Tea 25 bags', 'tea-coffee', 'unilever', 1200, 'pack', 0.05, 'kg', 220, null, true],
            ['Milo 500g', 'tea-coffee', 'nestle', 6000, 'pack', 0.5, 'kg', 140, null, false],
            ['Bournvita 500g', 'tea-coffee', 'cadbury', 6500, 'pack', 0.5, 'kg', 130, null, false],
            ['Nescafe 100g', 'tea-coffee', 'nestle', 2800, 'jar', 0.1, 'kg', 120, null, false],
            ['Green Tea 20 bags', 'tea-coffee', 'unilever', 1800, 'pack', 0.04, 'kg', 90, null, false],
            ['Lipton Yellow Label 100g', 'tea-coffee', 'unilever', 2500, 'pack', 0.1, 'kg', 100, null, false],

            // ---------------- Breakfast ----------------
            ['Golden Morn 500g', 'cereals', 'nestle', 4500, 'pack', 0.5, 'kg', 140, null, false],
            ['Cornflakes 380g', 'cereals', 'nasco', 3800, 'pack', 0.38, 'kg', 150, null, true],
            ['Kellogg’s Cornflakes 500g', 'cereals', 'nasco', 6500, 'pack', 0.5, 'kg', 80, null, false],
            ['Quaker Oatmeal 500g', 'cereals', 'nestle', 4500, 'pack', 0.5, 'kg', 90, null, false],
            ['Grand Cereals Custard 500g', 'cereals', 'grand-cereals', 2800, 'pack', 0.5, 'kg', 120, null, false],
            ['Rice Krispies 300g', 'cereals', 'nasco', 4200, 'pack', 0.3, 'kg', 70, null, false],
            ['Mamador Margarine 400g', 'spreads', 'mamador', 3500, 'tub', 0.4, 'kg', 100, null, false],
            ['Peanut Butter 500g', 'spreads', 'mamador', 4200, 'jar', 0.5, 'kg', 90, null, false],
            ['Natural Honey 500g', 'spreads', 'mamador', 5000, 'jar', 0.5, 'kg', 110, null, true],
            ['Strawberry Jam 400g', 'spreads', 'mamador', 3200, 'jar', 0.4, 'kg', 85, null, false],
            ['Chocolate Spread 400g', 'spreads', 'mamador', 4500, 'jar', 0.4, 'kg', 70, null, false],
            ['Pancake Mix 500g', 'oats-pancakes', 'honeywell', 2800, 'pack', 0.5, 'kg', 90, null, false],
            ['Golden Penny Oats 500g', 'oats-pancakes', 'golden-penny', 2800, 'pack', 0.5, 'kg', 100, null, false],
            ['Milled Oats 1kg', 'oats-pancakes', 'nestle', 4200, 'pack', 1, 'kg', 80, null, false],
            ['Akara Mix (Bean Flour) 1kg', 'oats-pancakes', 'honeywell', 3000, 'pack', 1, 'kg', 75, null, false],
            ['Sliced Bread (Large) 600g', 'bread-bakery', 'sweet-mornings', 1600, 'loaf', 0.6, 'kg', 120, null, false],
            ['Agege Bread (Medium)', 'bread-bakery', 'sweet-mornings', 2200, 'loaf', 1, 'kg', 90, null, false],
            ['Hamburger Buns (4 pcs)', 'bread-bakery', 'sweet-mornings', 1500, 'pack', 0.4, 'kg', 80, null, false],
            ['Croissants (2 pcs)', 'bread-bakery', 'sweet-mornings', 1800, 'pack', 0.2, 'kg', 60, null, false],
            ['Doughnuts (6 pcs)', 'bread-bakery', 'sweet-mornings', 2200, 'pack', 0.5, 'kg', 70, null, false],

            // ---------------- Pasta ----------------
            ['Golden Penny Spaghetti 500g', 'spaghetti', 'golden-penny', 2000, 'pack', 0.5, 'kg', 180, null, true],
            ['Dangote Spaghetti 500g', 'spaghetti', 'dangote', 1900, 'pack', 0.5, 'kg', 170, null, false],
            ['Indomie Spaghetti 700g', 'spaghetti', 'indomie', 2600, 'pack', 0.7, 'kg', 150, null, false],
            ['Dangote Spaghetti 1kg', 'spaghetti', 'dangote', 3800, 'pack', 1, 'kg', 100, null, false],
            ['Whole Wheat Spaghetti 500g', 'spaghetti', 'golden-penny', 2400, 'pack', 0.5, 'kg', 80, null, false],
            ['Golden Penny Macaroni 500g', 'macaroni', 'golden-penny', 2000, 'pack', 0.5, 'kg', 160, null, false],
            ['Dangote Macaroni 500g', 'macaroni', 'dangote', 1900, 'pack', 0.5, 'kg', 150, null, false],
            ['Elbow Pasta 500g', 'macaroni', 'golden-penny', 2100, 'pack', 0.5, 'kg', 110, null, false],
            ['Penne Pasta 500g', 'macaroni', 'golden-penny', 2300, 'pack', 0.5, 'kg', 90, null, false],
            ['Indomie Chicken 70g', 'noodles', 'indomie', 350, 'pack', 0.07, 'kg', 600, null, false],
            ['Indomie Carton (40 packs)', 'noodles', 'indomie', 13500, 'carton', 2.8, 'kg', 120, null, true],
            ['Indomie Jumbo 1kg', 'noodles', 'indomie', 2800, 'pack', 1, 'kg', 140, null, false],
            ['Minimie Noodles 70g', 'noodles', 'indomie', 300, 'pack', 0.07, 'kg', 500, null, false],
            ['Indomie Onion Flavour 70g', 'noodles', 'indomie', 350, 'pack', 0.07, 'kg', 400, null, false],
            ['Lasagna Sheets 500g', 'other-pasta', 'golden-penny', 3200, 'pack', 0.5, 'kg', 60, null, false],
            ['Couscous 500g', 'other-pasta', 'golden-penny', 2800, 'pack', 0.5, 'kg', 70, null, false],
            ['Fettuccine 500g', 'other-pasta', 'golden-penny', 2600, 'pack', 0.5, 'kg', 65, null, false],
            ['Dangote Noodles Carton (38 pcs)', 'other-pasta', 'dangote', 12000, 'carton', 2.6, 'kg', 90, null, false],

            // ---------------- Snacks ----------------
            ['McVities Digestive Biscuits', 'biscuits', 'saca', 1200, 'pack', 0.25, 'kg', 200, null, false],
            ['Cabin Biscuits', 'biscuits', 'saca', 500, 'pack', 0.2, 'kg', 300, null, false],
            ['Saca Biscuits', 'biscuits', 'saca', 300, 'pack', 0.1, 'kg', 400, null, true],
            ['Gala Sausage Roll', 'biscuits', 'saca', 250, 'pack', 0.1, 'kg', 350, null, false],
            ['Choco Biscuits', 'biscuits', 'saca', 600, 'pack', 0.15, 'kg', 250, null, false],
            ['Marietta Biscuits', 'biscuits', 'saca', 700, 'pack', 0.2, 'kg', 220, null, false],
            ['Crown Biscuits', 'biscuits', 'saca', 600, 'pack', 0.15, 'kg', 230, null, false],
            ['Pringles Original', 'chips-crisps', 'saca', 2800, 'tube', 0.15, 'kg', 120, null, false],
            ['Plantain Chips 200g', 'chips-crisps', 'saca', 1500, 'pack', 0.2, 'kg', 150, null, false],
            ['Sweet Potato Chips 150g', 'chips-crisps', 'saca', 1400, 'pack', 0.15, 'kg', 130, null, false],
            ['Doritos 140g', 'chips-crisps', 'saca', 2000, 'pack', 0.14, 'kg', 110, null, false],
            ['Lays Classic 90g', 'chips-crisps', 'saca', 1500, 'pack', 0.09, 'kg', 140, null, false],
            ['Roasted Cashew Nuts 250g', 'nuts-dried', 'saca', 3500, 'pack', 0.25, 'kg', 100, null, true],
            ['Roasted Groundnuts 500g', 'nuts-dried', 'saca', 1500, 'pack', 0.5, 'kg', 160, null, false],
            ['Dried Dates 500g', 'nuts-dried', 'saca', 2800, 'pack', 0.5, 'kg', 90, null, false],
            ['Raisins 250g', 'nuts-dried', 'saca', 2500, 'pack', 0.25, 'kg', 80, null, false],
            ['Tiger Nuts 500g', 'nuts-dried', 'saca', 1800, 'pack', 0.5, 'kg', 120, null, false],
            ['Bournvita Balls 200g', 'sweets-chocolates', 'cadbury', 2000, 'pack', 0.2, 'kg', 130, null, false],
            ['Dairy Milk Chocolate 180g', 'sweets-chocolates', 'cadbury', 3500, 'bar', 0.18, 'kg', 100, null, false],
            ['Gummy Bears 250g', 'sweets-chocolates', 'cadbury', 2800, 'pack', 0.25, 'kg', 90, null, false],
            ['Tom Tom Mints (24 pcs)', 'sweets-chocolates', 'cadbury', 500, 'pack', 0.1, 'kg', 300, null, false],
            ['Lollipops (20 pcs)', 'sweets-chocolates', 'cadbury', 1200, 'pack', 0.2, 'kg', 150, null, false],

            // ---------------- Baby Food ----------------
            ['Cerelac (Rice) 1kg', 'infant-cereal', 'nestle', 6500, 'pack', 1, 'kg', 130, null, true],
            ['Cerelac (Maize) 1kg', 'infant-cereal', 'nestle', 6500, 'pack', 1, 'kg', 120, null, false],
            ['Cerelac (Wheat) 1kg', 'infant-cereal', 'nestle', 6500, 'pack', 1, 'kg', 120, null, false],
            ['Nutribom 400g', 'infant-cereal', 'nestle', 4200, 'pack', 0.4, 'kg', 90, null, false],
            ['Baby Custard 500g', 'infant-cereal', 'nestle', 3800, 'pack', 0.5, 'kg', 80, null, false],
            ['SMA Gold 400g', 'formula', 'nestle', 9800, 'tin', 0.4, 'kg', 80, null, true],
            ['NAN Optipro 400g', 'formula', 'nestle', 10500, 'tin', 0.4, 'kg', 70, null, false],
            ['Similac 400g', 'formula', 'nestle', 11000, 'tin', 0.4, 'kg', 60, null, false],
            ['Nutribom Milk 400g', 'formula', 'nestle', 6500, 'pack', 0.4, 'kg', 75, null, false],
            ['Frisolac 400g', 'formula', 'nestle', 9800, 'tin', 0.4, 'kg', 55, null, false],
            ['Teething Baby Biscuits 100g', 'baby-snacks', 'nestle', 1500, 'pack', 0.1, 'kg', 100, null, false],
            ['Baby Rice Puffs 50g', 'baby-snacks', 'nestle', 1800, 'pack', 0.05, 'kg', 90, null, false],
            ['Baby Fruit Pouches (4 x 100g)', 'baby-snacks', 'nestle', 4800, 'pack', 0.4, 'kg', 60, null, false],
            ['Baby Cereal Bars (6 pcs)', 'baby-snacks', 'nestle', 3200, 'pack', 0.2, 'kg', 70, null, false],
            ['Baby Juice (Apple) 1L', 'baby-juice', 'nestle', 2800, 'pack', 1, 'L', 70, null, false],
            ['Baby Juice (Mixed Fruit) 1L', 'baby-juice', 'nestle', 2800, 'pack', 1, 'L', 70, null, false],
            ['Baby Water 1L', 'baby-juice', 'nestle', 1500, 'pack', 1, 'L', 60, null, false],

            // ---------------- Household Essentials ----------------
            ['Bleach (Hypo) 1L', 'cleaning', 'fresh-and-clean', 1200, 'bottle', 1, 'L', 200, null, false],
            ['Dishwashing Liquid 500ml', 'cleaning', 'fresh-and-clean', 1500, 'bottle', 0.5, 'L', 180, null, true],
            ['Floor Cleaner 1L', 'cleaning', 'fresh-and-clean', 1800, 'bottle', 1, 'L', 150, null, false],
            ['Glass Cleaner 500ml', 'cleaning', 'fresh-and-clean', 1400, 'bottle', 0.5, 'L', 120, null, false],
            ['Multi-Purpose Cleaner 1L', 'cleaning', 'fresh-and-clean', 2200, 'bottle', 1, 'L', 110, null, false],
            ['Disinfectant Spray 400ml', 'cleaning', 'fresh-and-clean', 2800, 'bottle', 0.4, 'L', 90, null, false],
            ['Omo Washing Powder 1kg', 'laundry', 'fresh-and-clean', 3500, 'pack', 1, 'kg', 150, null, true],
            ['Liquid Detergent 1L', 'laundry', 'fresh-and-clean', 2800, 'bottle', 1, 'L', 130, null, false],
            ['Laundry Bar Soap 200g', 'laundry', 'fresh-and-clean', 500, 'pack', 0.2, 'kg', 300, null, false],
            ['Fabric Softener 1L', 'laundry', 'fresh-and-clean', 2500, 'bottle', 1, 'L', 100, null, false],
            ['Stain Remover 500ml', 'laundry', 'fresh-and-clean', 2200, 'bottle', 0.5, 'L', 90, null, false],
            ['Washing Powder 500g', 'laundry', 'fresh-and-clean', 1800, 'pack', 0.5, 'kg', 140, null, false],
            ['Dish Sponge (5 pcs)', 'kitchen-items', 'fresh-and-clean', 800, 'pack', 0.1, 'kg', 250, null, false],
            ['Kitchen Towel', 'kitchen-items', 'fresh-and-clean', 1200, 'pack', 0.2, 'kg', 180, null, false],
            ['Garbage Bags (10 pcs)', 'kitchen-items', 'fresh-and-clean', 1500, 'pack', 0.3, 'kg', 160, null, false],
            ['Aluminium Foil 30m', 'kitchen-items', 'fresh-and-clean', 2800, 'roll', 0.4, 'kg', 90, null, false],
            ['Cling Film 50m', 'kitchen-items', 'fresh-and-clean', 2200, 'roll', 0.3, 'kg', 85, null, false],
            ['Toilet Paper (12 rolls)', 'paper-products', 'fresh-and-clean', 3500, 'pack', 1.2, 'kg', 130, null, false],
            ['Kitchen Roll (3 pcs)', 'paper-products', 'fresh-and-clean', 2800, 'pack', 0.8, 'kg', 100, null, false],
            ['Tissues (Box) 100 pcs', 'paper-products', 'fresh-and-clean', 1500, 'box', 0.5, 'kg', 140, null, false],
            ['Paper Napkins 100 pcs', 'paper-products', 'fresh-and-clean', 1200, 'pack', 0.4, 'kg', 120, null, false],
        ];
    }

    // ------------------------------------------------------------------ coupons

    private function createCoupons(): void
    {
        $data = [
            ['WELCOME10', 'First order welcome discount', 'percentage', 10, 10000, 5000, null, 1, now()->subDays(5), now()->addDays(90), false],
            ['FRESH5', 'Fresh groceries fixed discount', 'fixed', 3000, 20000, null, 200, 1, null, now()->addDays(60), false],
            ['MEMBER20', 'Members exclusive 20% off', 'percentage', 20, 15000, 10000, 150, 2, null, now()->addDays(120), true],
            ['WEEKEND15', 'Weekend shopper discount', 'percentage', 15, 15000, 8000, 500, 2, now()->subDays(1), now()->addDays(30), false],
            ['BULK10', 'Bulk purchase saver', 'percentage', 10, 50000, 15000, 100, 1, null, now()->addDays(75), false],
            ['PAYDAY', 'Payday special treat', 'fixed', 5000, 40000, null, 250, 1, now()->subDays(2), now()->addDays(20), false],
        ];

        foreach ($data as [$code, $name, $type, $value, $min, $max, $usage, $perUser, $starts, $expires, $memberOnly]) {
            Coupon::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => $name,
                    'type' => $type,
                    'value' => $value,
                    'min_order_amount' => $min,
                    'max_discount_amount' => $max,
                    'usage_limit' => $usage,
                    'per_user_limit' => $perUser,
                    'starts_at' => $starts,
                    'expires_at' => $expires,
                    'is_member_only' => $memberOnly,
                    'is_active' => true,
                ]
            );
        }

        $this->coupons = Coupon::active()->get();
    }

    // ------------------------------------------------------------------ flash sales

    private function createFlashSales(): void
    {
        $products = Product::active()->inStock()->get();

        $sales = [
            ['Weekend Mega Deal', 'Up to 40% off kitchen staples all weekend.', now()->subHours(3), now()->addHours(40)],
            ['Payday Special', 'Big savings on groceries for payday week.', now()->subDay(), now()->addDays(2)],
            ['Back to School Snacks', 'Snacks and breakfast essentials for the term.', now()->addDays(3), now()->addDays(5)],
        ];

        foreach ($sales as $i => [$name, $desc, $starts, $ends]) {
            $sale = FlashSale::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => $this->slug($name),
                    'description' => $desc,
                    'starts_at' => $starts,
                    'ends_at' => $ends,
                    'is_active' => true,
                ]
            );

            if (! $sale->wasRecentlyCreated) {
                continue;
            }

            $selected = $products->random(min(12, $products->count()));
            foreach ($selected as $product) {
                $price = (float) $product->price;
                $salePrice = round($price * (0.6 + (random_int(0, 15) / 100)), 2);

                FlashSaleItem::create([
                    'flash_sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'sale_price' => $salePrice,
                    'quantity' => random_int(20, 100),
                    'sold' => random_int(0, 15),
                ]);
            }
        }
    }

    // ------------------------------------------------------------------ bulk orders

    private function createBulkOrders(): void
    {
        $customers = $this->customers->isEmpty() ? User::all() : $this->customers;
        $productPool = Product::active()->inStock()->get();

        $campaigns = [
            ['Community Rice & Beans Pool', 'Let us buy rice and beans in bulk for the whole community and share the savings.', 10, ['rice', 'beans-legumes']],
            ['Monthly Protein Pool', 'Bulk frozen chicken and fish for the community monthly purchase.', 12, ['frozen-meat', 'fish-seafood']],
            ['Household Essentials Collective', 'Community bulk order for cleaning and kitchen essentials.', 8, ['cleaning', 'laundry', 'kitchen-items']],
        ];

        foreach ($campaigns as $i => [$title, $desc, $discount, $cats]) {
            $bulk = BulkOrder::firstOrCreate(
                ['title' => $title],
                [
                    'created_by' => $customers->random()->id,
                    'description' => $desc,
                    'status' => 'open',
                    'target_date' => now()->addDays(random_int(10, 30)),
                    'delivery_type' => 'community_collection',
                    'delivery_location' => 'FoodChain Community Centre, '.$customers->random()->city,
                    'discount_percentage' => $discount,
                ]
            );

            if (! $bulk->wasRecentlyCreated) {
                continue;
            }

            $catIds = collect($cats)->map(fn ($c) => $this->catIds[$c] ?? null)->filter()->values();
            $pool = $productPool->whereIn('category_id', $catIds)->shuffle()->take(3);

            foreach ($pool as $product) {
                $required = random_int(40, 200);
                $contributed = random_int(0, (int) floor($required * 0.8));
                BulkOrderItem::create([
                    'bulk_order_id' => $bulk->id,
                    'product_id' => $product->id,
                    'required_quantity' => $required,
                    'contributed_quantity' => $contributed,
                    'unit_price' => (float) $product->selling_price,
                ]);

                for ($c = 0; $c < random_int(2, 5) && $contributed > 0; $c++) {
                    $contributor = $customers->random();
                    $qty = max(1, (int) floor($contributed / 4));
                    $amount = round((float) $product->selling_price * $qty * (1 - $discount / 100), 2);
                    $bulkOrderItem = $bulk->items()->where('product_id', $product->id)->first();

                    BulkOrderContribution::create([
                        'bulk_order_id' => $bulk->id,
                        'bulk_order_item_id' => $bulkOrderItem->id,
                        'user_id' => $contributor->id,
                        'quantity' => $qty,
                        'amount' => $amount,
                        'status' => 'confirmed',
                        'payment_method' => 'wallet',
                        'payment_reference' => 'BULK-'.strtoupper(Str::random(10)),
                    ]);
                }
            }

            $bulk->recalculateTotals();
        }
    }

    // ------------------------------------------------------------------ orders & reviews

    private function createOrdersAndReviews(): void
    {
        $customers = $this->customers->isEmpty() ? User::all() : $this->customers;
        $products = Product::active()->get();
        $zones = $this->zones->isEmpty() ? DeliveryZone::active()->get() : $this->zones;
        $coupons = $this->coupons->isEmpty() ? Coupon::active()->get() : $this->coupons;

        if ($products->count() < 5) {
            return;
        }

        for ($i = 0; $i < 110; $i++) {
            $customer = $customers->random();
            $pick = $products->random(random_int(1, 4));
            $createdAt = now()->subDays(random_int(0, 29))->subHours(random_int(0, 23))->subMinutes(random_int(0, 59));

            $items = [];
            $subtotal = 0.0;

            foreach ($pick as $product) {
                $qty = random_int(1, 4);
                $price = (float) $product->selling_price;
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;
                $items[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'price' => $price,
                    'discount' => max(0, ((float) $product->price - $price) * $qty),
                ];
            }

            $zone = $zones->random();
            $shipping = ((float) $zone->delivery_fee);

            $coupon = (random_int(0, 100) < 25) ? $coupons->random() : null;
            $discountAmount = 0.0;
            $couponDiscount = 0.0;
            if ($coupon && $coupon->isValid((float) $subtotal, $customer->id, $customer->hasActiveMembership())) {
                $couponDiscount = $coupon->calculateDiscount((float) $subtotal);
                $discountAmount = $couponDiscount;
            }

            $total = max(0, $subtotal - $discountAmount) + $shipping;

            // Weighted status selection
            $status = $this->weightedStatus();
            $paymentMethod = $this->paymentForStatus($status);

            $walletAmount = 0.0;
            $cashAmount = 0.0;
            $paymentStatus = 'paid';
            $paidAt = null;

            if (in_array($paymentMethod, ['wallet', 'wallet_cash', 'wallet_paystack'])) {
                $balance = (float) $customer->wallet_balance;
                if ($balance >= $total) {
                    $walletAmount = $total;
                } elseif ($balance > 0) {
                    $walletAmount = $balance;
                    $cashAmount = round($total - $balance, 2);
                    $paymentStatus = 'partially_paid';
                }
            } elseif ($paymentMethod === 'cash') {
                $cashAmount = $total;
                $paymentStatus = in_array($status, ['cancelled', 'refunded']) ? 'unpaid' : 'pending';
            } elseif (in_array($paymentMethod, ['paystack', 'flutterwave', 'bank_transfer'])) {
                if (in_array($status, ['cancelled', 'refunded'])) {
                    $paymentStatus = 'unpaid';
                } elseif ($status === 'pending') {
                    $paymentStatus = 'pending';
                }
            }

            if ($paymentStatus === 'paid' && $walletAmount > 0) {
                $paidAt = $createdAt->addHours(1);
            }

            DB::transaction(function () use ($customer, $items, $zone, $coupon, $couponDiscount, $subtotal, $discountAmount, $shipping, $total, $status, $paymentMethod, $walletAmount, $cashAmount, $paymentStatus, $paidAt, $createdAt, $i) {
                $order = Order::firstOrCreate(
                    ['order_number' => 'FC-SEED-'.sprintf('%06d', $i + 1)],
                    [
                        'user_id' => $customer->id,
                        'status' => $status,
                        'subtotal' => round($subtotal, 2),
                        'discount_amount' => round($discountAmount, 2),
                        'shipping_fee' => round($shipping, 2),
                        'total' => round($total, 2),
                        'coupon_id' => $coupon?->id,
                        'coupon_code' => $coupon?->code,
                        'coupon_discount' => round($couponDiscount, 2),
                        'payment_method' => $paymentMethod,
                        'payment_status' => $paymentStatus,
                        'wallet_amount' => round($walletAmount, 2),
                        'cash_amount' => round($cashAmount, 2),
                        'delivery_type' => 'home_delivery',
                        'delivery_address' => $customer->address,
                        'delivery_city' => $customer->city,
                        'delivery_state' => $zone->state,
                        'delivery_zone_id' => $zone->id,
                        'estimated_delivery_date' => now()->addDays($zone->estimated_days),
                        'payment_gateway' => in_array($paymentMethod, ['paystack', 'flutterwave']) ? $paymentMethod : null,
                        'payment_reference' => in_array($paymentMethod, ['paystack', 'flutterwave']) ? 'PSK-'.strtoupper(Str::random(12)) : null,
                        'paid_at' => $paidAt,
                        'delivered_at' => $status === 'delivered' ? $createdAt->addDays($zone->estimated_days) : null,
                        'cancelled_at' => $status === 'cancelled' ? $createdAt->addHours(random_int(2, 20)) : null,
                        'refunded_at' => $status === 'refunded' ? $createdAt->addDays(2) : null,
                        'loyalty_points_earned' => 0,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );

                if (! $order->wasRecentlyCreated) {
                    return;
                }

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'vendor_id' => $item['product']->vendor_id,
                        'product_name' => $item['product']->name,
                        'product_sku' => $item['product']->sku,
                        'unit_price' => $item['price'],
                        'quantity' => $item['qty'],
                        'discount_amount' => round($item['discount'], 2),
                        'total_price' => round($item['price'] * $item['qty'], 2),
                        'status' => in_array($status, ['delivered']) ? 'delivered' : 'pending',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    if (in_array($status, ['confirmed', 'processing', 'packed', 'shipped', 'delivered'])) {
                        $item['product']->decrementStock($item['qty']);
                    }
                }

                // Wallet debit
                if ($walletAmount > 0 && (float) $customer->wallet_balance >= $walletAmount) {
                    $customer->deductFromWallet($walletAmount);
                    WalletTransaction::create([
                        'user_id' => $customer->id,
                        'amount' => round($walletAmount, 2),
                        'type' => 'debit',
                        'reference' => 'WT-'.strtoupper(Str::random(12)),
                        'description' => 'Payment for order '.$order->order_number,
                        'order_id' => $order->id,
                        'balance_after' => round((float) $customer->wallet_balance, 2),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }

                // Coupon usage
                if ($coupon) {
                    $coupon->increment('used_count');
                }

                // Loyalty points for completed orders
                if (! in_array($status, ['cancelled', 'refunded', 'pending'])) {
                    $points = (int) floor($total / 100);
                    if ($points > 0) {
                        $newBalance = (int) $customer->loyalty_points_balance + $points;
                        LoyaltyPoint::create([
                            'user_id' => $customer->id,
                            'points' => $points,
                            'type' => 'earn',
                            'description' => 'Points earned from order '.$order->order_number,
                            'order_id' => $order->id,
                            'balance_after' => $newBalance,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]);
                        $customer->update(['loyalty_points_balance' => $newBalance]);
                        $order->update(['loyalty_points_earned' => $points]);
                    }
                }

                // Status history
                $historyStatuses = array_unique(array_merge(['pending'], [$status]));
                foreach ($historyStatuses as $hs) {
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'status' => $hs,
                        'note' => $hs === 'pending' ? 'Order placed by customer' : 'Order updated to '.$hs,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }

                // Refunded orders refund wallet
                if ($status === 'refunded' && $walletAmount > 0) {
                    $customer->addToWallet($walletAmount);
                    WalletTransaction::create([
                        'user_id' => $customer->id,
                        'amount' => round($walletAmount, 2),
                        'type' => 'credit',
                        'reference' => 'RF-'.strtoupper(Str::random(12)),
                        'description' => 'Refund for order '.$order->order_number,
                        'order_id' => $order->id,
                        'balance_after' => round((float) $customer->wallet_balance, 2),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
            });
        }

        $this->createReviews();
    }

    private function weightedStatus(): string
    {
        $roll = random_int(1, 100);

        return match (true) {
            $roll <= 35 => 'delivered',
            $roll <= 45 => 'shipped',
            $roll <= 55 => 'confirmed',
            $roll <= 62 => 'processing',
            $roll <= 68 => 'packed',
            $roll <= 75 => 'paid',
            $roll <= 90 => 'pending',
            $roll <= 95 => 'cancelled',
            default => 'refunded',
        };
    }

    private function paymentForStatus(string $status): string
    {
        return match ($status) {
            'delivered' => (['wallet', 'cash', 'paystack', 'bank_transfer', 'wallet_cash'])[random_int(0, 4)],
            'shipped', 'processing', 'packed', 'confirmed', 'paid' => (['wallet', 'paystack', 'wallet_paystack'])[random_int(0, 2)],
            'pending' => (['cash', 'bank_transfer'])[random_int(0, 1)],
            'cancelled', 'refunded' => (['wallet', 'cash'])[random_int(0, 1)],
            default => 'wallet',
        };
    }

    private function createReviews(): void
    {
        $deliveredItems = OrderItem::with('order.user', 'product')
            ->whereHas('order', fn ($q) => $q->where('status', 'delivered'))
            ->orderBy('id')
            ->get();

        foreach ($deliveredItems as $item) {
            if ($item->review_id) {
                continue;
            }

            if (($item->id % 10) > 3) {
                continue;
            }

            $rating = random_int(3, 5);
            $review = Review::firstOrCreate(
                ['order_item_id' => $item->id],
                [
                    'product_id' => $item->product_id,
                    'user_id' => $item->order->user_id,
                    'order_id' => $item->order_id,
                    'rating' => $rating,
                    'title' => $this->reviewTitle($rating),
                    'comment' => $this->reviewComment($rating),
                    'is_approved' => random_int(0, 100) < 90,
                    'is_featured' => $rating === 5 && random_int(0, 100) < 30,
                ]
            );

            $item->update(['review_id' => $review->id]);
            $item->product->updateRatingStats();
        }
    }

    private function reviewTitle(int $rating): string
    {
        return match ($rating) {
            5 => 'Excellent quality!',
            4 => 'Very good product',
            default => 'Good value for money',
        };
    }

    private function reviewComment(int $rating): string
    {
        return match ($rating) {
            5 => 'Delivered fresh and exactly as described. The whole family is happy with the quality.',
            4 => 'Really good product at a fair price. Delivery was fast.',
            default => 'The product is decent. I would recommend it.',
        };
    }

    // ------------------------------------------------------------------ state pricing

    private function ensureStatePricing(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('nigerian_states')
            || ! \Illuminate\Support\Facades\Schema::hasTable('product_state_prices')) {
            return;
        }

        $stateIds = \App\Models\NigerianState::where('is_active', true)->orderBy('id')->pluck('id');

        if ($stateIds->isEmpty()) {
            return;
        }

        Product::with(['flashSaleItems.flashSale'])->chunkById(100, function ($products) use ($stateIds) {
            foreach ($products as $product) {
                $base = (float) $product->selling_price;
                if ($base <= 0) {
                    $base = (float) $product->price;
                }

                $rows = $stateIds->map(fn ($stateId) => [
                    'product_id' => $product->id,
                    'state_id' => $stateId,
                    'price' => $base,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();

                \App\Models\Marketplace\ProductStatePrice::query()->upsert(
                    $rows,
                    ['product_id', 'state_id'],
                    ['price', 'is_active', 'updated_at']
                );
            }
        });

        $this->command?->info('State-based pricing rows synced for all products.');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MembershipTierSeeder::class,
            FoodItemSeeder::class,
            FoodPackageSeeder::class,
            AdminPermissionSeeder::class,
            AdminUserSeeder::class,
            BankAccountSeeder::class,
            PickupLocationSeeder::class,
            SettingsSeeder::class,
            NigerianStateSeeder::class,
            MarketplaceSeeder::class,
            FumiStoreSeeder::class,
            OpeyemiSeeder::class,
            BlogPostSeeder::class,
        ]);
    }
}

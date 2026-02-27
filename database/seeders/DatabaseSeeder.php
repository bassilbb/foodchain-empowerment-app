<?php

namespace Database\Seeders;

use App\Models\User;
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
            AdminPermissionSeeder::class,
            PickupLocationSeeder::class,
            SettingsSeeder::class,
        ]);

        // Create default super admin
        DB::table('admin_users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@foodchain.ng',
            'password' => bcrypt('password'), // Change in production!
            'role' => 'super_admin',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

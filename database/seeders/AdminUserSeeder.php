<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the default administrator login used to access the admin panel
 * (admin guard).
 *
 * Idempotent: safe to re-run on production. Existing admin records are
 * updated by email, so this will not create duplicates.
 *
 * Login credentials:
 *   Email:    admin@foodchain.ng
 *   Password: password   (change after first production login)
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = AdminUser::updateOrCreate(
            ['email' => 'admin@foodchain.ng'],
            [
                'name' => 'Super Admin',
                'phone' => '08000000000',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('Admin login seeded: admin@foodchain.ng / password (id #'.$admin->id.', role: super_admin).');
    }
}

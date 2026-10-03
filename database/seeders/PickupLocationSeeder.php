<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PickupLocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pickup_locations')->insert([
            'name' => 'Head Office',
            'address' => 'Doorstep Farms Limited, [Complete Address]',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'phone' => '+234 816 801 8123',
            'email' => 'foodempower@gmail.com',
            'is_active' => true,
            'is_headquarters' => true,
            'operating_hours' => 'Monday - Friday: 9:00 AM - 5:00 PM',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

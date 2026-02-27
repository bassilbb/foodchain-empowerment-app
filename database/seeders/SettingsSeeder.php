<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'app_name', 'value' => 'FoodChain Community Empowerment Initiative', 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_name', 'value' => 'Doorstep Farms Limited', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'foodempower@gmail.com', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+234 816 801 8123', 'type' => 'string', 'group' => 'general'],
            
            // Payment Settings
            ['key' => 'paystack_public_key', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'paystack_secret_key', 'value' => '', 'type' => 'string', 'group' => 'payment'],
            ['key' => 'membership_fee', 'value' => '5000', 'type' => 'decimal', 'group' => 'payment'],
            ['key' => 'starter_pack_price', 'value' => '20000', 'type' => 'decimal', 'group' => 'payment'],
            
            // Distribution Settings
            ['key' => 'silver_distribution_frequency', 'value' => 'annual', 'type' => 'string', 'group' => 'distribution'],
            ['key' => 'gold_distribution_frequency', 'value' => 'annual', 'type' => 'string', 'group' => 'distribution'],
            ['key' => 'emerald_distribution_frequency', 'value' => 'quarterly', 'type' => 'string', 'group' => 'distribution'],
            ['key' => 'min_referrals_for_discretionary', 'value' => '5', 'type' => 'integer', 'group' => 'distribution'],
            ['key' => 'max_discretionary_value', 'value' => '10000', 'type' => 'decimal', 'group' => 'distribution'],
            
            // Notification Settings
            ['key' => 'enable_email_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'enable_sms_notifications', 'value' => 'false', 'type' => 'boolean', 'group' => 'notifications'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}


<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'app_name', 'value' => 'FoodChain Community Empowerment Initiative', 'type' => 'string', 'group' => 'general', 'description' => 'Application name'],
            ['key' => 'company_name', 'value' => 'Doorstep Farms Limited', 'type' => 'string', 'group' => 'general', 'description' => 'Company name'],
            ['key' => 'contact_email', 'value' => 'foodempower@gmail.com', 'type' => 'string', 'group' => 'general', 'description' => 'Contact email address'],
            ['key' => 'contact_phone', 'value' => '+234 816 801 8123', 'type' => 'string', 'group' => 'general', 'description' => 'Contact phone number'],
            ['key' => 'currency_symbol', 'value' => '₦', 'type' => 'string', 'group' => 'general', 'description' => 'Currency symbol'],
            ['key' => 'timezone', 'value' => 'Africa/Lagos', 'type' => 'string', 'group' => 'general', 'description' => 'Application timezone'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string', 'group' => 'general', 'description' => 'Date format'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'general', 'description' => 'Enable maintenance mode'],
            ['key' => 'require_kyc', 'value' => 'true', 'type' => 'boolean', 'group' => 'general', 'description' => 'Require KYC verification for users'],
            ['key' => 'session_timeout', 'value' => '60', 'type' => 'integer', 'group' => 'general', 'description' => 'Session timeout in minutes'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'general', 'description' => 'Maximum login attempts before lockout'],
            ['key' => 'site_favicon', 'value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Site favicon'],

            // Payment Settings
            ['key' => 'paystack_public_key', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Paystack public key'],
            ['key' => 'paystack_secret_key', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Paystack secret key'],
            ['key' => 'paystack_test_mode', 'value' => 'true', 'type' => 'boolean', 'group' => 'payment', 'description' => 'Enable Paystack test mode'],
            ['key' => 'membership_fee', 'value' => '5000', 'type' => 'decimal', 'group' => 'payment', 'description' => 'Membership registration fee'],
            ['key' => 'starter_pack_price', 'value' => '20000', 'type' => 'decimal', 'group' => 'payment', 'description' => 'Starter pack price'],
            ['key' => 'enable_auto_renewal', 'value' => 'true', 'type' => 'boolean', 'group' => 'payment', 'description' => 'Enable automatic membership renewal'],
            ['key' => 'renewal_reminder_days', 'value' => '7', 'type' => 'integer', 'group' => 'payment', 'description' => 'Days before expiry to send renewal reminder'],

            // Distribution Settings
            ['key' => 'silver_distribution_frequency', 'value' => 'annual', 'type' => 'string', 'group' => 'distribution', 'description' => 'Silver tier distribution frequency'],
            ['key' => 'gold_distribution_frequency', 'value' => 'annual', 'type' => 'string', 'group' => 'distribution', 'description' => 'Gold tier distribution frequency'],
            ['key' => 'emerald_distribution_frequency', 'value' => 'quarterly', 'type' => 'string', 'group' => 'distribution', 'description' => 'Emerald tier distribution frequency'],
            ['key' => 'min_referrals_for_discretionary', 'value' => '5', 'type' => 'integer', 'group' => 'distribution', 'description' => 'Minimum referrals for discretionary distribution'],
            ['key' => 'max_discretionary_value', 'value' => '10000', 'type' => 'decimal', 'group' => 'distribution', 'description' => 'Maximum discretionary distribution value'],
            ['key' => 'enable_quarterly_bonus', 'value' => 'true', 'type' => 'boolean', 'group' => 'distribution', 'description' => 'Enable quarterly bonus distribution'],
            ['key' => 'distribution_notice_days', 'value' => '3', 'type' => 'integer', 'group' => 'distribution', 'description' => 'Days to notify before distribution'],

            // Notification Settings
            ['key' => 'enable_email_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Enable email notifications'],
            ['key' => 'enable_sms_notifications', 'value' => 'false', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Enable SMS notifications'],
            ['key' => 'require_email_verification', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Require email verification for new users'],
            ['key' => 'notify_on_payment', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Send notification on successful payment'],
            ['key' => 'notify_on_distribution', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Send notification on distribution'],
            ['key' => 'notify_on_referral', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Send notification on new referral'],

            // System Settings
            ['key' => 'referral_bonus_amount', 'value' => '1000', 'type' => 'decimal', 'group' => 'general', 'description' => 'Referral bonus amount'],
            ['key' => 'min_referral_for_bonus', 'value' => '3', 'type' => 'integer', 'group' => 'general', 'description' => 'Minimum referrals to earn bonus'],

            // Voucher Settings
            ['key' => 'voucher_expiry_days', 'value' => '30', 'type' => 'integer', 'group' => 'general', 'description' => 'Voucher expiry days'],
            ['key' => 'min_voucher_amount', 'value' => '1000', 'type' => 'decimal', 'group' => 'general', 'description' => 'Minimum voucher amount'],
            ['key' => 'max_voucher_amount', 'value' => '100000', 'type' => 'decimal', 'group' => 'general', 'description' => 'Maximum voucher amount'],

            // Deduction Schedule Settings
            ['key' => 'deduction_schedule_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'general', 'description' => 'Enable weekly scheduled deductions'],
            ['key' => 'deduction_day', 'value' => '6', 'type' => 'integer', 'group' => 'general', 'description' => 'Weekly deduction day (0=Sun, 6=Sat)'],
            ['key' => 'deduction_time', 'value' => '02:00', 'type' => 'string', 'group' => 'general', 'description' => 'Weekly deduction time'],
            ['key' => 'deduction_retry_day', 'value' => '0', 'type' => 'integer', 'group' => 'general', 'description' => 'Retry deduction day'],
            ['key' => 'deduction_retry_time', 'value' => '02:00', 'type' => 'string', 'group' => 'general', 'description' => 'Retry deduction time'],
            ['key' => 'voucher_validity_hours', 'value' => '72', 'type' => 'integer', 'group' => 'general', 'description' => 'Hours until voucher expires after issuance'],

            // Module Toggles (user-facing feature flags)
            ['key' => 'module_referrals', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable referral system for users'],
            ['key' => 'module_distributions', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable food distributions for users'],
            ['key' => 'module_food_assistance', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable food assistance plans'],
            ['key' => 'module_food_support', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable food support plans'],
            ['key' => 'module_food_loans', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable food loans for users'],
            ['key' => 'module_unions', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable unions/cooperatives'],
            ['key' => 'module_quarterly_benefits', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable quarterly benefits claims'],
            ['key' => 'module_incidents', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable incident reporting'],
            ['key' => 'module_savings_accounts', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable savings plan accounts'],
            ['key' => 'module_manual_payments', 'value' => 'true', 'type' => 'boolean', 'group' => 'modules', 'description' => 'Enable manual payment submissions'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}

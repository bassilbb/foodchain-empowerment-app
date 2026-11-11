<?php

use App\Models\Marketplace\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed DB-driven payment-method availability.
 *
 * Bank Transfer starts enabled; every other method starts disabled. Values
 * live in the existing `settings` table so admins can flip them at runtime.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach (array_keys(Order::PAYMENT_METHODS) as $method) {
            $key = 'payment_methods.'.$method;

            if (DB::table('settings')->where('key', $key)->exists()) {
                continue;
            }

            DB::table('settings')->insert([
                'key' => $key,
                'value' => $method === 'bank_transfer' ? '1' : '0',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => $method === 'bank_transfer'
                    ? 'Customer transfers the exact order amount to the configured company bank account.'
                    : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'like', 'payment_methods.%')->delete();
    }
};

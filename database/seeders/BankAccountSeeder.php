<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'bank_name' => 'First Bank',
                'account_name' => 'FoodChain Global Services Ltd',
                'account_number' => '1234567890',
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'bank_name' => 'Guaranty Trust Bank (GTBank)',
                'account_name' => 'FoodChain Global Services Ltd',
                'account_number' => '0987654321',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'bank_name' => 'Zenith Bank',
                'account_name' => 'FoodChain Global Services Ltd',
                'account_number' => '5678901234',
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($accounts as $account) {
            BankAccount::create($account);
        }
    }
}

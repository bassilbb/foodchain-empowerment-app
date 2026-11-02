<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\ClearanceRequest;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getBalance(User $user): float
    {
        return (float) $user->wallet_balance;
    }

    public function topUp(User $user, float $amount, string $description = 'Wallet top-up'): AccountTransaction
    {
        $user->addToWallet($amount);

        return AccountTransaction::create([
            'primary_user_id' => $user->id,
            'user_account_id' => null,
            'amount' => $amount,
            'type' => 'top_up',
            'description' => $description,
        ]);
    }

    public function deductForAccount(User $user, UserAccount $account, float $amount, string $description = 'Weekly contribution'): bool
    {
        if (! $user->hasSufficientWalletBalance($amount)) {
            return false;
        }

        return DB::transaction(function () use ($user, $account, $amount, $description) {
            $user->deductFromWallet($amount);

            $account->increment('total_contributions', $amount);
            $account->increment('available_balance', $amount);

            AccountTransaction::create([
                'primary_user_id' => $user->id,
                'user_account_id' => $account->id,
                'amount' => $amount,
                'type' => 'contribution',
                'description' => $description,
            ]);

            $account->increment('current_tariff_weeks');
            $account->update(['last_deduction_at' => now()]);

            if ($account->isMatured()) {
                ClearanceRequest::firstOrCreate(
                    ['user_account_id' => $account->id],
                    [
                        'user_id' => $account->primary_user_id,
                        'status' => 'pending_payment',
                        'fee_amount' => 3000,
                    ]
                );
            }

            return true;
        });
    }

    public function processWeeklyDeductions(): array
    {
        $processed = 0;
        $defaulted = 0;
        $accounts = UserAccount::where('status', 'active')
            ->with('primaryUser', 'savingsPlanOption')
            ->get()
            ->filter(fn ($a) => ! $a->isMatured());

        foreach ($accounts as $account) {
            $user = $account->primaryUser;
            $amount = (float) $account->weekly_contribution_amount;

            if ($this->deductForAccount($user, $account, $amount)) {
                $processed++;
            } else {
                DB::transaction(function () use ($account) {
                    $account->increment('defaults');
                    $account->update(['last_deduction_at' => now()]);
                });
                $defaulted++;
            }
        }

        return ['processed' => $processed, 'defaulted' => $defaulted];
    }

    public function retryDefaultedDeductions(): array
    {
        $processed = 0;
        $stillDefaulted = 0;

        $userIds = UserAccount::where('status', 'active')
            ->where('defaults', '>', 0)
            ->distinct()
            ->pluck('primary_user_id');

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        foreach ($userIds as $userId) {
            $user = $users->get($userId);
            if (! $user) {
                continue;
            }

            $accounts = UserAccount::where('primary_user_id', $userId)
                ->where('status', 'active')
                ->get()
                ->filter(fn ($a) => ! $a->isMatured());

            foreach ($accounts as $account) {
                if ($account->last_deduction_at && $account->last_deduction_at->isToday()) {
                    continue;
                }

                $amount = (float) $account->weekly_contribution_amount;

                if ($this->deductForAccount($user, $account, $amount)) {
                    if ($account->defaults > 0) {
                        $account->decrement('defaults');
                    }
                    $processed++;
                } else {
                    $account->update(['last_deduction_at' => now()]);
                    $stillDefaulted++;
                }
            }
        }

        return ['processed' => $processed, 'still_defaulted' => $stillDefaulted];
    }
}

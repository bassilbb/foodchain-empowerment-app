<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\Order;
use App\Models\Marketplace\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MarketplaceWalletService
{
    public function getBalance(User $user): float
    {
        return (float) $user->wallet_balance;
    }

    public function payWithWallet(User $user, Order $order, float $amount): bool
    {
        if ($amount <= 0) {
            return true;
        }

        if (! $user->hasSufficientWalletBalance($amount)) {
            throw new \RuntimeException('Insufficient wallet balance.');
        }

        return DB::transaction(function () use ($user, $order, $amount) {
            $user->deductFromWallet($amount);

            $order->increment('wallet_amount', $amount);

            $this->logTransaction($user, $order, $amount, 'debit', 'Payment for order '.$order->order_number);

            if (($order->wallet_amount + 0) >= $order->total - 0.01) {
                $order->update([
                    'payment_status' => 'paid',
                ]);
                $order->updateStatus('paid', 'Payment received via wallet.');
            }

            return true;
        });
    }

    public function credit(User $user, Order $order, float $amount, string $description): bool
    {
        return DB::transaction(function () use ($user, $order, $amount, $description) {
            $user->addToWallet($amount);

            $this->logTransaction($user, $order, $amount, 'credit', $description);

            return true;
        });
    }

    public function refund(User $user, Order $order, float $amount, string $description): bool
    {
        $this->credit($user, $order, $amount, $description);

        $order->update([
            'refund_amount' => $order->refund_amount + $amount,
            'payment_status' => 'refunded',
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
        $order->updateStatus('refunded', $description);

        return true;
    }

    public function logTransaction(User $user, ?Order $order, float $amount, string $type, string $description): WalletTransaction
    {
        return WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => $type,
            'reference' => 'WLT-'.strtoupper(uniqid()),
            'description' => $description,
            'order_id' => $order?->id,
            'balance_after' => $this->getBalance($user),
        ]);
    }

    public function getTransactions(User $user, int $limit = 50): \Illuminate\Support\Collection
    {
        return WalletTransaction::with('order')
            ->where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }
}

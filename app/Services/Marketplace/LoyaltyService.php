<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\LoyaltyPoint;
use App\Models\Marketplace\Order;
use App\Models\User;

class LoyaltyService
{
    private const POINTS_PER_NAIRA = 100;

    public function getBalance(User $user): int
    {
        return (int) $user->loyalty_points_balance;
    }

    public function pointsForAmount(float $amount): int
    {
        return (int) floor($amount / self::POINTS_PER_NAIRA);
    }

    public function awardForOrder(Order $order): int
    {
        $user = $order->user;
        $points = $this->pointsForAmount((float) $order->total);

        if ($points <= 0) {
            return 0;
        }

        $newBalance = $this->getBalance($user) + $points;

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => 'earn',
            'description' => 'Points earned from order '.$order->order_number,
            'order_id' => $order->id,
            'balance_after' => $newBalance,
        ]);

        $user->update(['loyalty_points_balance' => $newBalance]);
        $order->update(['loyalty_points_earned' => $points]);

        return $points;
    }

    public function redeem(User $user, int $points): float
    {
        $balance = $this->getBalance($user);

        if ($points <= 0 || $points > $balance) {
            throw new \InvalidArgumentException('Insufficient loyalty points.');
        }

        $value = $points; // 1 point = ₦1 wallet credit

        $newBalance = $balance - $points;

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => -$points,
            'type' => 'spend',
            'description' => 'Redeemed for wallet credit',
            'balance_after' => $newBalance,
        ]);

        $user->update(['loyalty_points_balance' => $newBalance]);

        $user->addToWallet($value);

        return $value;
    }

    public function getHistory(User $user): \Illuminate\Support\Collection
    {
        return LoyaltyPoint::where('user_id', $user->id)->latest()->limit(50)->get();
    }
}

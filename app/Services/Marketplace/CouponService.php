<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\Coupon;
use App\Models\User;

class CouponService
{
    public function validate(string $code, ?float $subtotal, ?User $user): array
    {
        $coupon = Coupon::active()->where('code', strtoupper(trim($code)))->first();

        if (! $coupon) {
            return ['valid' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $isMember = $user ? $user->hasActiveMembership() : false;

        if (! $coupon->isValid($subtotal, $user?->id, $isMember)) {
            return ['valid' => false, 'message' => 'This coupon cannot be applied to your order.'];
        }

        $discount = $coupon->calculateDiscount((float) $subtotal);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Coupon applied successfully.',
        ];
    }

    public function applyToOrder(string $code, float $subtotal, User $user): array
    {
        $result = $this->validate($code, $subtotal, $user);

        if (! $result['valid']) {
            return $result;
        }

        return [
            'valid' => true,
            'coupon_id' => $result['coupon']->id,
            'coupon_code' => $result['coupon']->code,
            'coupon_discount' => $result['discount'],
        ];
    }

    public function markUsed(Coupon $coupon): void
    {
        $coupon->markUsed();
    }
}

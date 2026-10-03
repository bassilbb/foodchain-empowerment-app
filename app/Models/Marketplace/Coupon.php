<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'marketplace_coupons';

    protected $fillable = [
        'code', 'name', 'description', 'type', 'value', 'min_order_amount',
        'max_discount_amount', 'usage_limit', 'used_count', 'per_user_limit',
        'starts_at', 'expires_at', 'is_active', 'is_member_only',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_member_only' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function isValid(?float $subtotal = null, ?int $userId = null, ?bool $isMember = false): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && now()->gte($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->is_member_only && ! $isMember) {
            return false;
        }

        if ($subtotal !== null && $this->min_order_amount !== null && $subtotal < $this->min_order_amount) {
            return false;
        }

        if ($userId !== null && $this->per_user_limit > 0) {
            $userUsage = \App\Models\Marketplace\Order::where('user_id', $userId)
                ->where('coupon_id', $this->id)
                ->count();

            if ($userUsage >= $this->per_user_limit) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'fixed') {
            $discount = min($this->value, $subtotal);
        } else {
            $discount = $subtotal * ($this->value / 100);
        }

        if ($this->max_discount_amount !== null) {
            $discount = min($discount, $this->max_discount_amount);
        }

        return max(0, round($discount, 2));
    }

    public function markUsed(): void
    {
        $this->increment('used_count');
    }
}

<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'marketplace_orders';

    protected $fillable = [
        'user_id', 'order_number', 'status', 'subtotal', 'discount_amount',
        'shipping_fee', 'total', 'coupon_id', 'coupon_code', 'coupon_discount',
        'payment_method', 'payment_status', 'wallet_amount', 'cash_amount',
        'delivery_type', 'delivery_address', 'delivery_city', 'delivery_state',
        'delivery_lga', 'delivery_zone_id', 'estimated_delivery_date',
        'pickup_location', 'delivery_notes', 'payment_reference', 'payment_id',
        'bulk_order_id', 'refund_amount', 'loyalty_points_earned', 'note',
        'meta', 'cancelled_at', 'refunded_at', 'delivered_at',
        'payment_gateway', 'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'wallet_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'estimated_delivery_date' => 'date',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
        'delivered_at' => 'datetime',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'packed' => 'Packed',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ];

    public const PAYMENT_METHODS = [
        'wallet' => 'FoodChain Wallet',
        'cash' => 'Cash on Delivery',
        'paystack' => 'Paystack',
        'flutterwave' => 'Flutterwave',
        'bank_transfer' => 'Bank Transfer',
        'wallet_cash' => 'Wallet + Cash',
        'wallet_paystack' => 'Wallet + Card',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id')->orderBy('created_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['paid', 'partially_paid']);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'paid', 'confirmed']);
    }

    public function isReturnable(): bool
    {
        return in_array($this->status, ['delivered']);
    }

    public function updateStatus(string $status, ?string $note = null, $changedBy = null): void
    {
        $this->update(['status' => $status]);

        if ($status === 'delivered') {
            $this->update(['delivered_at' => now()]);
        }

        if ($status === 'cancelled') {
            $this->update(['cancelled_at' => now()]);
        }

        if ($status === 'refunded') {
            $this->update(['refunded_at' => now()]);
        }

        OrderStatusHistory::create([
            'order_id' => $this->id,
            'status' => $status,
            'note' => $note,
            'changed_by_type' => $changedBy ? get_class($changedBy) : null,
            'changed_by_id' => $changedBy?->id,
        ]);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'FC-'.date('ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));
        } while (self::where('order_number', $number)->exists());

        return $number;
    }
}

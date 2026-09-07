<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'marketplace_order_items';

    protected $fillable = [
        'order_id', 'product_id', 'vendor_id', 'product_name', 'product_sku',
        'unit_price', 'quantity', 'discount_amount', 'total_price', 'status',
        'review_id',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class, 'review_id');
    }
}

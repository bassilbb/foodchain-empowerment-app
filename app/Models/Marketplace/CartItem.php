<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $table = 'marketplace_cart_items';

    protected $fillable = [
        'user_id', 'product_id', 'quantity', 'saved_for_later',
    ];

    protected $casts = [
        'saved_for_later' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getLineTotalAttribute(): float
    {
        return $this->product->selling_price * $this->quantity;
    }
}

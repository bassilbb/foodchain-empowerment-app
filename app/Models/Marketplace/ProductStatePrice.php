<?php

namespace App\Models\Marketplace;

use App\Models\NigerianState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStatePrice extends Model
{
    protected $table = 'product_state_prices';

    protected $fillable = [
        'product_id', 'state_id', 'price', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(NigerianState::class, 'state_id');
    }
}

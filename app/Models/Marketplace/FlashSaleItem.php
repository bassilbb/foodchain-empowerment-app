<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    protected $table = 'marketplace_flash_sale_items';

    protected $fillable = [
        'flash_sale_id', 'product_id', 'sale_price', 'quantity', 'sold',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
    ];

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class, 'flash_sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

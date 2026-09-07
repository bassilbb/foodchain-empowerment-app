<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkOrderItem extends Model
{
    protected $table = 'marketplace_bulk_order_items';

    protected $fillable = [
        'bulk_order_id', 'product_id', 'required_quantity', 'contributed_quantity', 'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function bulkOrder(): BelongsTo
    {
        return $this->belongsTo(BulkOrder::class, 'bulk_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

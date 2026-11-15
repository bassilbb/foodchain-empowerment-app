<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceImportMissingProduct extends Model
{
    protected $table = 'marketplace_price_import_missing_products';

    public const ACTION_PENDING = 'pending';
    public const ACTION_KEPT = 'kept';
    public const ACTION_DEACTIVATED = 'deactivated';

    protected $fillable = [
        'batch_id', 'product_id', 'product_name', 'sku', 'current_price', 'action',
    ];

    protected $casts = [
        'current_price' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PriceImportBatch::class, 'batch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

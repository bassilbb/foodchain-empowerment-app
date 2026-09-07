<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $table = 'marketplace_inventory_movements';

    protected $fillable = [
        'product_id', 'vendor_id', 'type', 'quantity', 'batch_number',
        'reason', 'changed_by_type', 'changed_by_id',
    ];

    public const TYPES = [
        'stock_in' => 'Stock In',
        'stock_out' => 'Stock Out',
        'adjustment' => 'Adjustment',
        'damaged' => 'Damaged',
        'returned' => 'Returned',
        'sale' => 'Sale',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}

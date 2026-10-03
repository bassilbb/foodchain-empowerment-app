<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkOrderContribution extends Model
{
    protected $table = 'marketplace_bulk_order_contributions';

    protected $fillable = [
        'bulk_order_id', 'bulk_order_item_id', 'user_id', 'quantity',
        'amount', 'status', 'payment_method', 'payment_reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function bulkOrder(): BelongsTo
    {
        return $this->belongsTo(BulkOrder::class, 'bulk_order_id');
    }

    public function bulkOrderItem(): BelongsTo
    {
        return $this->belongsTo(BulkOrderItem::class, 'bulk_order_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

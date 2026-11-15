<?php

namespace App\Models\Marketplace;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    protected $table = 'marketplace_product_price_histories';

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_BULK_IMPORT = 'bulk_import';
    public const SOURCE_ROLLBACK = 'rollback';

    public const STATUS_APPLIED = 'applied';
    public const STATUS_CREATED = 'created';
    public const STATUS_ROLLED_BACK = 'rolled_back';

    protected $fillable = [
        'product_id', 'vendor_id', 'batch_id', 'admin_user_id',
        'product_name', 'sku', 'source',
        'old_price', 'new_price', 'difference', 'percent_change',
        'status', 'note', 'rolled_back_at',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'difference' => 'decimal:2',
        'percent_change' => 'decimal:2',
        'rolled_back_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PriceImportBatch::class, 'batch_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {
            self::SOURCE_MANUAL => 'Manual edit',
            self::SOURCE_BULK_IMPORT => 'Bulk upload',
            self::SOURCE_ROLLBACK => 'Rollback',
            default => ucfirst((string) $this->source),
        };
    }
}

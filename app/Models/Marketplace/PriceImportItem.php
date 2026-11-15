<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceImportItem extends Model
{
    protected $table = 'marketplace_price_import_items';

    public const STATUS_MATCHED = 'matched';
    public const STATUS_NEW = 'new';
    public const STATUS_INVALID = 'invalid';
    public const STATUS_AMBIGUOUS = 'ambiguous';

    public const STATUSES = [
        self::STATUS_MATCHED => 'Matched',
        self::STATUS_NEW => 'New product',
        self::STATUS_INVALID => 'Invalid record',
        self::STATUS_AMBIGUOUS => 'Matching required',
    ];

    public const CHANGE_INCREASE = 'increase';
    public const CHANGE_DECREASE = 'decrease';
    public const CHANGE_UNCHANGED = 'unchanged';
    public const CHANGE_NEW = 'new';
    public const CHANGE_INVALID = 'invalid';
    public const CHANGE_AMBIGUOUS = 'ambiguous';

    public const CHANGE_LABELS = [
        self::CHANGE_INCREASE => 'Price Increased',
        self::CHANGE_DECREASE => 'Price Reduced',
        self::CHANGE_UNCHANGED => 'Unchanged',
        self::CHANGE_NEW => 'New Product',
        self::CHANGE_INVALID => 'Invalid Record',
        self::CHANGE_AMBIGUOUS => 'Matching Required',
    ];

    protected $fillable = [
        'batch_id', 'row_number', 'product_id', 'sku', 'supplier_product_code', 'barcode',
        'product_name', 'category_name', 'unit', 'price', 'current_price', 'new_price',
        'difference', 'percent_change', 'status', 'match_method', 'change_type',
        'errors', 'warnings', 'raw', 'requires_manual_completion', 'applied', 'applied_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'difference' => 'decimal:2',
        'percent_change' => 'decimal:2',
        'errors' => 'array',
        'warnings' => 'array',
        'raw' => 'array',
        'requires_manual_completion' => 'boolean',
        'applied' => 'boolean',
        'applied_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PriceImportBatch::class, 'batch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function changeLabel(): string
    {
        return self::CHANGE_LABELS[$this->change_type] ?? ucfirst((string) $this->change_type);
    }

    public function changeBadge(): string
    {
        return match ($this->change_type) {
            self::CHANGE_INCREASE => 'badge-warning',
            self::CHANGE_DECREASE => 'badge-info',
            self::CHANGE_UNCHANGED => 'badge-dark',
            self::CHANGE_NEW => 'badge-success',
            self::CHANGE_INVALID, self::CHANGE_AMBIGUOUS => 'badge-error',
            default => 'badge-dark',
        };
    }
}

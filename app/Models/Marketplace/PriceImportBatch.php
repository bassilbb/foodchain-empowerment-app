<?php

namespace App\Models\Marketplace;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceImportBatch extends Model
{
    protected $table = 'marketplace_price_import_batches';

    public const STATUS_UPLOADED = 'uploaded';
    public const STATUS_PREVIEWED = 'previewed';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PARTIALLY_APPLIED = 'partially_applied';
    public const STATUS_FAILED = 'failed';
    public const STATUS_ROLLED_BACK = 'rolled_back';

    public const STATUSES = [
        self::STATUS_UPLOADED => 'Uploaded',
        self::STATUS_PREVIEWED => 'Awaiting confirmation',
        self::STATUS_QUEUED => 'Queued',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_PARTIALLY_APPLIED => 'Partially applied',
        self::STATUS_FAILED => 'Failed',
        self::STATUS_ROLLED_BACK => 'Rolled back',
    ];

    protected $fillable = [
        'vendor_id', 'admin_user_id', 'original_filename', 'stored_path', 'file_hash', 'format',
        'status', 'field_mapping', 'options',
        'total_records', 'matched_count', 'increased_count', 'reduced_count', 'unchanged_count',
        'new_count', 'missing_count', 'invalid_count', 'ambiguous_count', 'manual_count',
        'applied_count', 'created_count', 'failed_count', 'deactivated_count',
        'rolled_back_count', 'processed_count', 'last_error',
        'previewed_at', 'applied_at', 'rolled_back_at',
        'rollback_admin_user_id', 'rollback_reason',
    ];

    protected $casts = [
        'field_mapping' => 'array',
        'options' => 'array',
        'previewed_at' => 'datetime',
        'applied_at' => 'datetime',
        'rolled_back_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }

    public function rollbackAdmin(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'rollback_admin_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceImportItem::class, 'batch_id');
    }

    public function missingProducts(): HasMany
    {
        return $this->hasMany(PriceImportMissingProduct::class, 'batch_id');
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class, 'batch_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'badge-success',
            self::STATUS_PREVIEWED, self::STATUS_QUEUED, self::STATUS_PROCESSING => 'badge-warning',
            self::STATUS_FAILED, self::STATUS_PARTIALLY_APPLIED => 'badge-error',
            self::STATUS_ROLLED_BACK => 'badge-dark',
            default => 'badge-info',
        };
    }

    public function isAwaitingConfirmation(): bool
    {
        return in_array($this->status, [self::STATUS_PREVIEWED, self::STATUS_FAILED], true);
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, [self::STATUS_QUEUED, self::STATUS_PROCESSING], true);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_PARTIALLY_APPLIED], true);
    }

    public function canRollback(): bool
    {
        return $this->isCompleted() && $this->applied_at !== null;
    }
}

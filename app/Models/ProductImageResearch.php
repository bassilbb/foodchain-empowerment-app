<?php

namespace App\Models;

use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per marketplace product that needs a genuine packaging image.
 *
 * The row is the audit trail for the research workflow: which candidate was
 * found, where it came from, whether it was downloaded, and who verified it.
 * Images are never attached to a product until an administrator approves the
 * candidate (or uploads a replacement).
 */
class ProductImageResearch extends Model
{
    protected $table = 'marketplace_product_image_research';

    public const STATUS_PENDING_RESEARCH = 'pending_research';

    public const STATUS_CANDIDATE_FOUND = 'candidate_found';

    public const STATUS_PENDING_VERIFICATION = 'pending_verification';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_DOWNLOADED = 'downloaded';

    public const STATUS_NOT_FOUND = 'not_found';

    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING_RESEARCH => 'Pending Research',
        self::STATUS_CANDIDATE_FOUND => 'Candidate Found',
        self::STATUS_PENDING_VERIFICATION => 'Pending Verification',
        self::STATUS_VERIFIED => 'Verified',
        self::STATUS_DOWNLOADED => 'Downloaded',
        self::STATUS_NOT_FOUND => 'Not Found',
        self::STATUS_REJECTED => 'Rejected',
    ];

    public const DOWNLOAD_NOT_STARTED = 'not_started';

    public const DOWNLOAD_DOWNLOADED = 'downloaded';

    public const DOWNLOAD_FAILED = 'failed';

    protected $fillable = [
        'product_id',
        'vendor_id',
        'sku',
        'product_name',
        'status',
        'candidate_image_url',
        'candidate_source_url',
        'candidate_source_site',
        'candidate_title',
        'candidate_query',
        'downloaded_path',
        'download_status',
        'download_error',
        'downloaded_at',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'reviewer_notes',
        'research_attempts',
        'last_researched_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'last_researched_at' => 'datetime',
        'research_attempts' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'verified_by');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendingResearch($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING_RESEARCH,
            self::STATUS_CANDIDATE_FOUND,
            self::STATUS_NOT_FOUND,
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function isDownloaded(): bool
    {
        return $this->status === self::STATUS_DOWNLOADED && $this->downloaded_path;
    }
}

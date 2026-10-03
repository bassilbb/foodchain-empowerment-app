<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPayout extends Model
{
    protected $table = 'marketplace_vendor_payouts';

    protected $fillable = [
        'vendor_id',
        'reference',
        'amount',
        'gross_sales',
        'commission_amount',
        'commission_rate',
        'other_deductions',
        'status',
        'payout_method',
        'account_name',
        'account_number',
        'bank_name',
        'notes',
        'rejection_reason',
        'requested_at',
        'processed_at',
        'paid_at',
        'processed_by_type',
        'processed_by_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gross_sales' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public const STATUSES = [
        'requested' => 'Requested',
        'approved' => 'Approved',
        'processing' => 'Processing',
        'paid' => 'Paid',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function scopeForVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /** Payouts that have actually reached the vendor's bank. */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /** Payouts still in flight — requested but not yet settled. */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['requested', 'approved', 'processing']);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'PO-' . date('ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }
}

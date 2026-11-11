<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ManualPayment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'payment_id',
        'marketplace_order_id',
        'bank_account_id',
        'type',
        'amount',
        'currency',
        'reference',
        'sender_name',
        'sender_account_number',
        'sender_bank_name',
        'transfer_date',
        'proof_image_path',
        'status',
        'payment_status',
        'verification_status',
        'admin_notes',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transfer_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function marketplaceOrder()
    {
        return $this->belongsTo(\App\Models\Marketplace\Order::class, 'marketplace_order_id');
    }

    public function verifier()
    {
        return $this->belongsTo(AdminUser::class, 'verified_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Record an administrator approval. Payment stays "paid" only once the
     * admin explicitly confirms the funds landed in the company account.
     */
    public function verify(int $adminId, ?string $notes = null): void
    {
        if ($this->isVerified()) {
            throw new \DomainException('This payment has already been verified.');
        }

        if ($this->isRejected()) {
            throw new \DomainException('A rejected payment cannot be verified. Ask the customer to submit a new notification.');
        }

        $this->update([
            'status' => self::STATUS_VERIFIED,
            'payment_status' => 'paid',
            'verification_status' => 'approved',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'admin_notes' => $notes,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Record an administrator rejection. The original notification is kept
     * intact and the order remains unpaid.
     */
    public function reject(int $adminId, string $notes): void
    {
        if (trim($notes) === '') {
            throw new \DomainException('A rejection reason is required.');
        }

        if ($this->isVerified()) {
            throw new \DomainException('A verified payment cannot be rejected.');
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'payment_status' => 'unpaid',
            'verification_status' => 'rejected',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'rejection_reason' => $notes,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Collision-resistant reference. The database also enforces uniqueness.
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'MP-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_VERIFIED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Pending Verification',
        };
    }
}

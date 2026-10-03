<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_distribution_id',
        'manual_payment_id',
        'voucher_number',
        'status',
        'issued_at',
        'collected_at',
        'expires_at',
        'collection_code',
        'collected_by',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'collected_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }

    public function acknowledge(): void
    {
        $this->update(['acknowledged_at' => now()]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function distribution()
    {
        return $this->belongsTo(FoodDistribution::class, 'food_distribution_id');
    }

    public function manualPayment()
    {
        return $this->belongsTo(ManualPayment::class, 'manual_payment_id');
    }

    public function collector()
    {
        return $this->belongsTo(AdminUser::class, 'collected_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeCollected($query)
    {
        return $query->where('status', 'collected');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeValid($query)
    {
        return $query->whereIn('status', ['pending', 'issued'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    public function isCollected(): bool
    {
        return $this->status === 'collected';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->expires_at && $this->expires_at->isPast());
    }

    public function isValid(): bool
    {
        return in_array($this->status, ['pending', 'issued']) && ! $this->isExpired();
    }

    public function issue(): void
    {
        $validityHours = (int) setting('voucher_validity_hours', 72);

        $this->update([
            'status' => 'issued',
            'issued_at' => now(),
            'collection_code' => $this->generateCollectionCode(),
            'expires_at' => now()->addHours($validityHours),
        ]);
    }

    public function collect(int $collectedBy, ?string $notes = null): void
    {
        $this->update([
            'status' => 'collected',
            'collected_at' => now(),
            'collected_by' => $collectedBy,
            'notes' => $notes,
        ]);

        if ($this->distribution) {
            $this->distribution->update([
                'collection_status' => 'collected',
                'status' => 'completed',
            ]);
        }
    }

    public function markExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function verifyCollectionCode(string $code): bool
    {
        return $this->collection_code === $code && $this->isValid();
    }

    private function generateCollectionCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public static function generateVoucherNumber(): string
    {
        do {
            $number = 'VCH-'.date('Y').'-'.strtoupper(uniqid());
        } while (self::where('voucher_number', $number)->exists());

        return $number;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference',
        'type',
        'amount',
        'status',
        'payment_method',
        'paystack_reference',
        'paystack_response',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'paystack_response' => 'array',
        'meta' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function starterPack()
    {
        return $this->hasOne(StarterPack::class);
    }

    public function renewal()
    {
        return $this->hasOne(MembershipRenewal::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeMembership($query)
    {
        return $query->where('type', 'membership');
    }

    public function scopeStarterPack($query)
    {
        return $query->where('type', 'starter_pack');
    }

    // Helper Methods
    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsSuccessful(array $paystackResponse = []): void
    {
        $this->update([
            'status' => 'successful',
            'paid_at' => now(),
            'paystack_response' => $paystackResponse,
        ]);
    }

    public function markAsFailed(array $paystackResponse = []): void
    {
        $this->update([
            'status' => 'failed',
            'paystack_response' => $paystackResponse,
        ]);
    }
}
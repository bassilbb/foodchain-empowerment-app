<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'renewal_date',
        'expiry_date',
        'amount',
        'status',
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'expiry_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('expiry_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                     ->orWhere('expiry_date', '<', now());
    }

    public function scopeExpiringIn($query, $days)
    {
        return $query->where('status', 'active')
                     ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expiry_date->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function daysUntilExpiry(): int
    {
        return now()->diffInDays($this->expiry_date, false);
    }
}

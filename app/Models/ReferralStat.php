<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'direct_referrals',
        'active_referrals',
        'total_referral_value',
        'last_referral_at',
    ];

    protected $casts = [
        'direct_referrals' => 'integer',
        'active_referrals' => 'integer',
        'total_referral_value' => 'decimal:2',
        'last_referral_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper Methods
    public function incrementDirectReferrals(): void
    {
        $this->increment('direct_referrals');
        $this->update(['last_referral_at' => now()]);
    }

    public function incrementActiveReferrals(): void
    {
        $this->increment('active_referrals');
    }

    public function addReferralValue(float $amount): void
    {
        $this->increment('total_referral_value', $amount);
    }
}

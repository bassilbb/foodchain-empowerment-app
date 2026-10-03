<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TierProgressionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_tier_id',
        'to_tier_id',
        'referral_count_at_upgrade',
        'notes',
    ];

    protected $casts = [
        'referral_count_at_upgrade' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromTier()
    {
        return $this->belongsTo(MembershipTier::class, 'from_tier_id');
    }

    public function toTier()
    {
        return $this->belongsTo(MembershipTier::class, 'to_tier_id');
    }
}
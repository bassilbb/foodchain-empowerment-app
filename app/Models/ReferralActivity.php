<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_user_id',
        'activity_type',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    // Relationships
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeRegistrations($query)
    {
        return $query->where('activity_type', 'registered');
    }

    public function scopePayments($query)
    {
        return $query->whereIn('activity_type', ['paid_membership', 'paid_starter']);
    }
}


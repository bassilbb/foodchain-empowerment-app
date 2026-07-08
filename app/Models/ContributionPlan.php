<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'weekly_amount',
        'duration_weeks',
        'total_contribution',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weekly_amount' => 'decimal:2',
        'total_contribution' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'selected_plan_id');
    }
}

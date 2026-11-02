<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsPlanOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'savings_plan_id',
        'duration_weeks',
        'weekly_contribution',
        'is_active',
    ];

    protected $casts = [
        'weekly_contribution' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(SavingsPlan::class, 'savings_plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

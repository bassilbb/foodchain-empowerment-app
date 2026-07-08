<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target_amount',
        'description',
        'is_active',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(SavingsPlanOption::class);
    }

    public function activeOptions()
    {
        return $this->options()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

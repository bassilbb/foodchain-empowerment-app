<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tier_id',
        'name',
        'description',
        'total_value',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Relationships
    public function tier()
    {
        return $this->belongsTo(MembershipTier::class, 'tier_id');
    }

    public function items()
    {
        return $this->hasMany(FoodPackageItem::class, 'package_id');
    }

    public function distributions()
    {
        return $this->hasMany(FoodDistribution::class, 'package_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForTier($query, $tierId)
    {
        return $query->where('tier_id', $tierId);
    }

    // Helper Methods
    public function calculateTotalValue(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_value;
        });
    }

    public function hasStock(): bool
    {
        foreach ($this->items as $item) {
            if ($item->foodItem->getTotalStock() < $item->quantity) {
                return false;
            }
        }
        return true;
    }
}
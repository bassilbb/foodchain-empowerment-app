<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'phone',
        'email',
        'is_active',
        'is_headquarters',
        'operating_hours',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_headquarters' => 'boolean',
    ];

    // Relationships
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'location_id');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'location_id');
    }

    public function distributions()
    {
        return $this->hasMany(FoodDistribution::class, 'pickup_location_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHeadquarters($query)
    {
        return $query->where('is_headquarters', true);
    }

    // Helper Methods
    public function getTotalInventoryValue(): float
    {
        return $this->inventory->sum(function ($inv) {
            return $inv->available_quantity * $inv->cost_per_unit;
        });
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state}";
    }
}
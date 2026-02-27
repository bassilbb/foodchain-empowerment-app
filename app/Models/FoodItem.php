<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'unit',
        'unit_price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function packageItems()
    {
        return $this->hasMany(FoodPackageItem::class);
    }

    public function distributionItems()
    {
        return $this->hasMany(FoodDistributionItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function getTotalStock(): float
    {
        return $this->inventory()->sum('available_quantity');
    }

    public function getTotalStockValue(): float
    {
        return $this->inventory()->get()->sum(function ($inv) {
            return $inv->available_quantity * $inv->cost_per_unit;
        });
    }

    public function getStockAtLocation(int $locationId): float
    {
        return $this->inventory()
                    ->where('location_id', $locationId)
                    ->sum('available_quantity');
    }

    public function isLowStock(float $threshold = 10): bool
    {
        return $this->getTotalStock() < $threshold;
    }
}


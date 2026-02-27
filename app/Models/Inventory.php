<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    
    protected $table = 'inventories';

    protected $fillable = [
        'food_item_id',
        'location_id',
        'quantity',
        'reserved_quantity',
        'available_quantity',
        'expiry_date',
        'cost_per_unit',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Boot method to calculate available quantity
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($inventory) {
            $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
        });
    }

    // Relationships
    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class);
    }

    public function location()
    {
        return $this->belongsTo(PickupLocation::class, 'location_id');
    }

    // Scopes
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('available_quantity', '<', $threshold);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<', now());
    }

    // Helper Methods
    public function addStock(float $quantity, float $costPerUnit = null): void
    {
        $this->quantity += $quantity;
        if ($costPerUnit !== null) {
            $this->cost_per_unit = $costPerUnit;
        }
        $this->save();
    }

    public function removeStock(float $quantity): bool
    {
        if ($this->available_quantity < $quantity) {
            return false;
        }

        $this->quantity -= $quantity;
        $this->save();
        return true;
    }

    public function reserveStock(float $quantity): bool
    {
        if ($this->available_quantity < $quantity) {
            return false;
        }

        $this->reserved_quantity += $quantity;
        $this->save();
        return true;
    }

    public function releaseReservedStock(float $quantity): void
    {
        $this->reserved_quantity = max(0, $this->reserved_quantity - $quantity);
        $this->save();
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon($days = 30): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->between(now(), now()->addDays($days));
    }
}

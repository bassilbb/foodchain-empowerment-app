<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_item_id',
        'location_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'unit_cost',
        'reference',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    // Relationships
    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class);
    }

    public function location()
    {
        return $this->belongsTo(PickupLocation::class, 'location_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(AdminUser::class, 'performed_by');
    }

    // Scopes
    public function scopeStockIn($query)
    {
        return $query->where('type', 'stock_in');
    }

    public function scopeStockOut($query)
    {
        return $query->where('type', 'stock_out');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper Methods
    public function isStockIn(): bool
    {
        return $this->type === 'stock_in';
    }

    public function isStockOut(): bool
    {
        return $this->type === 'stock_out';
    }
}


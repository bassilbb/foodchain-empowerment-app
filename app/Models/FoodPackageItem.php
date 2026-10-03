<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodPackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'food_item_id',
        'quantity',
        'unit_value',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_value' => 'decimal:2',
    ];

    // Relationships
    public function package()
    {
        return $this->belongsTo(FoodPackage::class, 'package_id');
    }

    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class);
    }

    // Helper Methods
    public function getTotalValue(): float
    {
        return $this->quantity * $this->unit_value;
    }
}

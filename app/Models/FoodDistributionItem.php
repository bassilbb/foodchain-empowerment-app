<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodDistributionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_id',
        'food_item_id',
        'quantity',
        'unit_value',
        'total_value',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_value' => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    // Boot method to calculate total value
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_value = $item->quantity * $item->unit_value;
        });
    }

    // Relationships
    public function distribution()
    {
        return $this->belongsTo(FoodDistribution::class, 'distribution_id');
    }

    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class);
    }
}

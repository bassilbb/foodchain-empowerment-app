<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $table = 'marketplace_delivery_zones';

    protected $fillable = [
        'name', 'state', 'lga', 'delivery_fee', 'min_order_amount', 'estimated_days', 'is_active',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForState($query, ?string $state)
    {
        return $query->where('state', $state ?? 'Lagos');
    }
}

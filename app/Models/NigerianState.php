<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NigerianState extends Model
{
    protected $table = 'nigerian_states';

    protected $fillable = [
        'name', 'code', 'region', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function productStatePrices(): HasMany
    {
        return $this->hasMany(\App\Models\Marketplace\ProductStatePrice::class, 'state_id');
    }

    public static function ensureState(string $name): self
    {
        return self::firstOrCreate(['name' => $name]);
    }
}

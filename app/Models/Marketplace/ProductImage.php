<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $table = 'marketplace_product_images';

    protected $fillable = [
        'product_id', 'image_path', 'is_primary', 'is_placeholder', 'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_placeholder' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getUrlAttribute(): string
    {
        $path = (string) $this->image_path;

        if ($path === '' || str_starts_with($path, 'data:') || str_starts_with($path, 'http')) {
            return $path;
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, 'assets/')) {
            return '/'.ltrim($path, '/');
        }

        return '/storage/'.ltrim($path, '/');
    }
}

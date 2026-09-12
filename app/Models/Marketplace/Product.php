<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'marketplace_products';

    protected $fillable = [
        'vendor_id', 'category_id', 'brand_id', 'name', 'slug', 'sku', 'barcode',
        'unit', 'weight', 'weight_unit', 'price', 'discount_price', 'member_price',
        'stock_quantity', 'low_stock_threshold', 'description', 'ingredients',
        'nutritional_info', 'storage_instructions', 'expiry_date', 'manufacturing_date',
        'country_of_origin', 'video_url', 'batch_number', 'meta',
        'is_featured', 'is_active', 'average_rating', 'rating_count',
        'sold_count', 'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'member_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'expiry_date' => 'date',
        'manufacturing_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'average_rating' => 'decimal:2',
        'meta' => 'array',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function flashSaleItems(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class, 'product_id');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_id');
    }

    public function statePrices(): HasMany
    {
        return $this->hasMany(ProductStatePrice::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price');
    }

    public function scopeAvailable($query)
    {
        return $query->active()->where(function ($q) {
            $q->where('stock_quantity', '>', 0)->orWhere('meta->allow_backorder', true);
        });
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['category'] ?? null, function ($q, $category) {
            $q->where('category_id', $category);
        })
        ->when($filters['brand'] ?? null, function ($q, $brand) {
            $q->where('brand_id', $brand);
        })
        ->when($filters['vendor'] ?? null, function ($q, $vendor) {
            $q->where('vendor_id', $vendor);
        })
        ->when($filters['min_price'] ?? null, function ($q, $min) {
            $q->where('price', '>=', (float) $min);
        })
        ->when($filters['max_price'] ?? null, function ($q, $max) {
            $q->where('price', '<=', (float) $max);
        })
        ->when($filters['rating'] ?? null, function ($q, $rating) {
            $q->where('average_rating', '>=', (float) $rating);
        })
        ->when($filters['in_stock'] ?? null, function ($q) {
            $q->where('stock_quantity', '>', 0);
        })
        ->when($filters['discounted'] ?? null, function ($q) {
            $q->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price');
        })
        ->when($filters['on_sale'] ?? null, function ($q) {
            $q->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price');
        })
        ->when($filters['sort'] ?? null, function ($q, $sort) {
            match ($sort) {
                'price_asc' => $q->orderBy('price', 'asc'),
                'price_desc' => $q->orderBy('price', 'desc'),
                'newest' => $q->orderBy('created_at', 'desc'),
                'best_selling' => $q->orderBy('sold_count', 'desc'),
                'rating' => $q->orderBy('average_rating', 'desc'),
                'name_asc' => $q->orderBy('name', 'asc'),
                default => $q->orderBy('created_at', 'desc'),
            };
        });
    }

    public function scopeSearch($query, string $term)
    {
        $like = '%'.trim($term).'%';

        return $query->where(function ($q) use ($like, $term) {
            $q->where('name', 'like', $like)
                ->orWhere('sku', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('ingredients', 'like', $like)
                ->orWhereHas('brand', function ($b) use ($like) {
                    $b->where('name', 'like', $like);
                })
                ->orWhereHas('category', function ($c) use ($like) {
                    $c->where('name', 'like', $like);
                })
                ->orWhereHas('vendor', function ($v) use ($like) {
                    $v->where('name', 'like', $like)->orWhere('business_name', 'like', $like);
                });
        });
    }

    public function getRealImagesAttribute()
    {
        return $this->images->filter(fn ($image) => ! $image->is_placeholder)->values();
    }

    public function hasRealImages(): bool
    {
        return $this->images->contains(fn ($image) => ! $image->is_placeholder);
    }

    public function getPrimaryImageAttribute()
    {
        $real = $this->real_images;

        if ($real->isNotEmpty()) {
            return $real->firstWhere('is_primary', true) ?? $real->first();
        }

        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function getImageAttribute()
    {
        return $this->primary_image?->url;
    }

    public function getSellingPriceAttribute(): float
    {
        if ($this->discount_price && $this->discount_price < $this->price) {
            return (float) $this->discount_price;
        }

        $flashSale = $this->activeFlashSalePrice();

        return $flashSale ?? (float) $this->price;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        $price = (float) $this->price;
        $selling = $this->selling_price;

        if ($selling >= $price || $price <= 0) {
            return null;
        }

        return (int) round((($price - $selling) / $price) * 100);
    }

    public function getMemberSellingPriceAttribute(): ?float
    {
        if ($this->member_price && $this->member_price < $this->price) {
            return (float) $this->member_price;
        }

        return null;
    }

    public function activeFlashSalePrice(): ?float
    {
        foreach ($this->flashSaleItems as $item) {
            if ($item->flashSale && $item->flashSale->isActive() && $item->sale_price > 0) {
                return (float) $item->sale_price;
            }
        }

        return null;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && $this->expiry_date->isAfter(now()) && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function decrementStock(int $quantity): void
    {
        $this->decrement('stock_quantity', $quantity);
        $this->increment('sold_count', $quantity);
    }

    public function incrementStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    public function recordView(): void
    {
        $this->increment('views_count');
    }

    public function updateRatingStats(): void
    {
        $rating = $this->approvedReviews()->avg('rating') ?? 0;
        $this->update([
            'average_rating' => round((float) $rating, 2),
            'rating_count' => $this->approvedReviews()->count(),
        ]);
    }

    public static function generateSku(int $length = 8): string
    {
        do {
            $sku = 'FC-'.strtoupper(substr(md5(uniqid()), 0, $length));
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }
}

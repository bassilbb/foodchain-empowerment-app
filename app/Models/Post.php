<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = ['draft', 'published'];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'image',
        'image_alt',
        'status',
        'is_featured',
        'published_at',
        'author_id',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * A slug is derived from the title only while the post has none. Once
     * assigned it is left alone, because regenerating it on a title edit would
     * silently break every existing link to the post.
     */
    protected static function booted(): void
    {
        static::saving(function (self $post) {
            if (blank($post->slug) && filled($post->title)) {
                $post->slug = static::uniqueSlug($post->title, $post->exists ? $post->id : null);
            }
        });
    }

    /**
     * Seeded posts keep their original public/ asset when the copy into
     * storage/app/public failed, so the blog never renders a broken image.
     */
    private const LEGACY_ASSETS = [
        'how-we-keep-your-groceries-quality-safe-and-approved' => 'assets/landing/images/blogsa.jpeg',
        'how-community-bulk-purchasing-helps-your-household-save' => 'assets/landing/images/blogsa.jpeg',
        'from-local-stall-to-nationwide-marketplace-store' => 'assets/landing/images/blogpost.jpeg',
        'getting-better-grocery-value-with-loyalty-points' => 'assets/landing/images/about-uss.webp',
        'home-pickup-or-community-delivery' => 'assets/landing/images/blogf.jpeg',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Resolves the image to a URL, tolerating a missing file so the card
     * layout survives a deleted or failed upload.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (Storage::disk('public')->exists($this->image)) {
            return public_storage_url($this->image);
        }

        $legacy = self::LEGACY_ASSETS[$this->slug] ?? null;

        return $legacy ? asset($legacy) : null;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && ($this->published_at === null || $this->published_at->lessThanOrEqualTo(now()));
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $suffix = 2;

        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }
}

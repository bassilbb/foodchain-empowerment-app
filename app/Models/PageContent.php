<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PageContent extends Model
{
    protected $fillable = [
        'page',
        'section',
        'label',
        'type',
        'value',
        'options',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePage($query, string $page)
    {
        return $query->where('page', $page);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getContent(string $page, string $section, $default = null)
    {
        if (! Schema::hasTable('page_contents')) {
            return $default;
        }

        $content = self::where('page', $page)->where('section', $section)->first();

        return $content ? $content->value : $default;
    }

    public static function getPageSections(string $page): array
    {
        if (! Schema::hasTable('page_contents')) {
            return [];
        }

        return self::where('page', $page)->orderBy('sort_order')->get()->keyBy('section')->toArray();
    }

    public static function isPageActive(string $page): bool
    {
        if (! Schema::hasTable('page_contents')) {
            return true;
        }

        $count = self::where('page', $page)->count();
        if ($count === 0) {
            return true;
        }

        return self::where('page', $page)->where('is_active', true)->exists();
    }
}

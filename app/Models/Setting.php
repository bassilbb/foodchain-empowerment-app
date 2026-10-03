<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    // Helper Methods

    /**
     * Per-request memo of setting key => value.
     *
     * Blade partials call app_name()/site_logo()/module_enabled() many times per
     * page; without this cache each call issues its own SELECT on `settings`.
     *
     * @var array<string, mixed>
     */
    protected static array $memo = [];

    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, self::$memo)) {
            return self::$memo[$key] ?? $default;
        }

        $setting = self::where('key', $key)->first();

        if (! $setting) {
            return self::$memo[$key] = $default;
        }

        return self::$memo[$key] = self::castValue($setting->value, $setting->type);
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Invalidate the memo so the new value is visible immediately.
        unset(self::$memo[$key]);
    }

    /** Flush the per-request memo (useful in tests / after bulk updates). */
    public static function flushMemo(): void
    {
        self::$memo = [];
    }

    private static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'decimal', 'float' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
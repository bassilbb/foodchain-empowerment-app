<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductStatePrice;
use App\Models\NigerianState;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class MarketplacePricingService
{
    private const DEFAULT_STATE = 'Lagos';

    /**
     * Regional price factors. Base = 1.00 (FCT Abuja, the storefront default).
     * South-west markets are the most competitive; far north-east is costlier.
     */
    public const REGION_FACTORS = [
        'south-west' => 0.96,
        'south-east' => 1.03,
        'south-south' => 1.04,
        'north-central' => 1.02,
        'north-west' => 1.06,
        'north-east' => 1.08,
    ];

    private static ?array $mapCache = [];
    private static array $nameCache = [];
    private static ?string $currentCache = null;

    /**
     * Canonical state name for a user-provided value (handles aliases, codes and slugs).
     */
    public function normalize(string $state): ?string
    {
        $name = trim($state);

        $name = match (strtolower($name)) {
            'abuja', 'fct', 'federal capital territory', 'fct-abuja', 'fct abuja' => 'FCT Abuja',
            default => $name,
        };

        $key = strtolower($name);

        if (array_key_exists($key, self::$nameCache)) {
            return self::$nameCache[$key];
        }

        // Direct name match first, then ISO-2 code, then a URL-friendly slug.
        $canonical = NigerianState::query()
            ->where('is_active', true)
            ->where(function ($q) use ($name, $key) {
                $q->whereRaw('lower(name) = ?', [$key])
                    ->orWhereRaw('lower(code) = ?', [$key])
                    ->orWhereRaw('lower(trim(name)) = ?', [$key]);
            })
            ->value('name');

        if (! $canonical && strlen($name) >= 3) {
            $slug = Str::slug($name);
            $canonical = NigerianState::query()
                ->where('is_active', true)
                ->get(['name'])
                ->firstWhere(fn ($s) => Str::slug($s->name) === $slug)?->name;
        }

        if (! $canonical) {
            $canonical = NigerianState::where('name', $name)->where('is_active', true)->value('name');
        }

        return self::$nameCache[$key] = $canonical;
    }

    public function defaultState(): string
    {
        $first = NigerianState::active()->ordered()->value('name');

        return $first ?: self::DEFAULT_STATE;
    }

    /**
     * Active, ordered list of Nigerian state names used by the selector.
     */
    public function states(): array
    {
        return NigerianState::active()->ordered()->pluck('name')->all();
    }

    /**
     * Active, ordered list of state names and ISO-2 codes used by the selector
     * (codes keep the switch URL clean — no encoded spaces).
     */
    public function stateOptions(): array
    {
        return NigerianState::active()->ordered()->get(['name', 'code'])
            ->map(fn ($s) => ['name' => $s->name, 'code' => strtolower($s->code)])
            ->all();
    }

    /**
     * State that governs storefront prices for the current visitor.
     */
    public function currentState(): string
    {
        if (self::$currentCache) {
            return self::$currentCache;
        }

        $candidate = session('marketplace_state') ?: Cookie::get('marketplace_state');

        if (auth()->check() && ! $candidate) {
            $candidate = auth()->user()->state;
        }

        if ($candidate) {
            $canonical = $this->normalize((string) $candidate);
            if ($canonical) {
                return self::$currentCache = $canonical;
            }
        }

        return self::$currentCache = $this->defaultState();
    }

    /**
     * Persist the visitor's pricing state (session + long-lived cookie).
     */
    public function setState(string $state): ?string
    {
        $canonical = $this->normalize($state);
        if (! $canonical) {
            return null;
        }

        session(['marketplace_state' => $canonical]);
        Cookie::queue('marketplace_state', $canonical, 60 * 24 * 365);
        self::$currentCache = $canonical;

        if (auth()->check() && ! in_array(auth()->user()->state, $this->states(), true)) {
            // keep profile untouched; state selection is session-only
        }

        return $canonical;
    }

    /**
     * Per-request map of product_id => chargeable price for a state.
     */
    public function mapFor(string $state): array
    {
        if (isset(self::$mapCache[$state])) {
            return self::$mapCache[$state];
        }

        $rows = ProductStatePrice::query()
            ->select('product_id', 'price')
            ->where('is_active', true)
            ->whereHas('state', fn ($q) => $q->where('name', $state))
            ->pluck('price', 'product_id');

        return self::$mapCache[$state] = $rows->map(fn ($v) => (float) $v)->all();
    }

    /**
     * Effective chargeable price for a product in the given (or current) state.
     */
    public function priceFor(Product $product, ?string $state = null): float
    {
        $name = $state ? ($this->normalize($state) ?? $this->currentState()) : $this->currentState();
        $map = $this->mapFor($name);

        return array_key_exists($product->id, $map)
            ? (float) $map[$product->id]
            : (float) $product->selling_price;
    }

    /**
     * "Was" price shown struck-through when the state price beats the list price.
     */
    public function compareFor(Product $product, ?string $state = null): ?float
    {
        $charge = $this->priceFor($product, $state);
        $regular = (float) $product->price;

        return $charge < $regular ? $regular : null;
    }
}

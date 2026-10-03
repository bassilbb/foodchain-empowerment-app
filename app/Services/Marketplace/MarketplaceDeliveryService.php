<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\DeliveryZone;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MarketplaceDeliveryService
{
    private const DEFAULT_STANDARD_FEE = 1500;
    private const DEFAULT_EXPRESS_FEE = 3000;
    private const DEFAULT_SAME_DAY_FEE = 5000;
    private const FREE_DELIVERY_THRESHOLD = 50000;
    private const WEIGHT_RATE_PER_KG = 200;
    private const SAME_DAY_CITIES = ['Lagos', 'Abuja', 'Port Harcourt', 'Ibadan'];

    public function getZones(): \Illuminate\Support\Collection
    {
        return Cache::remember('marketplace_delivery_zones', 3600, function () {
            return DeliveryZone::active()->orderBy('state')->get();
        });
    }

    public function getStates(): array
    {
        return $this->getZones()->pluck('state')->unique()->sort()->values()->toArray();
    }

    public function calculate(array $data): array
    {
        $state = $data['state'] ?? 'Lagos';
        $lga = $data['lga'] ?? null;
        $deliveryType = $data['delivery_type'] ?? 'home_delivery';
        $subtotal = (float) ($data['subtotal'] ?? 0);
        $weight = (float) ($data['weight'] ?? 0);
        $user = $data['user'] ?? null;

        if ($deliveryType === 'pickup' || $deliveryType === 'community_delivery') {
            return [
                'delivery_fee' => 0.0,
                'estimated_days' => 0,
                'delivery_type' => $deliveryType,
                'message' => 'No delivery fee for pickup / community delivery.',
            ];
        }

        $zone = DeliveryZone::active()
            ->where('state', $state)
            ->when($lga, fn ($q) => $q->where(function ($q2) use ($lga) {
                $q2->whereNull('lga')->orWhere('lga', $lga);
            }))
            ->first();

        if (! $zone) {
            $zone = DeliveryZone::active()->where('state', $state)->first();
        }

        $fee = $zone?->delivery_fee ?? self::DEFAULT_STANDARD_FEE;

        $weightFee = max(0, ($weight - 5)) * self::WEIGHT_RATE_PER_KG;
        $fee += $weightFee;

        if ($subtotal >= self::FREE_DELIVERY_THRESHOLD) {
            $fee = 0;
        } elseif ($zone && $zone->min_order_amount !== null && $subtotal >= $zone->min_order_amount) {
            $fee = 0;
        }

        if ($deliveryType === 'express') {
            $fee = max($fee, self::DEFAULT_EXPRESS_FEE);
        }

        if ($deliveryType === 'same_day') {
            $fee = self::DEFAULT_SAME_DAY_FEE;
            if (! in_array($state, self::SAME_DAY_CITIES)) {
                return [
                    'delivery_fee' => $fee,
                    'estimated_days' => $zone?->estimated_days ?? 3,
                    'delivery_type' => 'same_day',
                    'message' => 'Same day delivery not available in '.$state.', standard delivery applied.',
                ];
            }
        }

        $estimatedDays = match ($deliveryType) {
            'express' => 1,
            'same_day' => 0,
            default => $zone?->estimated_days ?? 3,
        };

        return [
            'delivery_fee' => round($fee, 2),
            'estimated_days' => $estimatedDays,
            'estimated_delivery_date' => now()->addDays($estimatedDays)->toDateString(),
            'delivery_type' => $deliveryType,
            'zone' => $zone?->name,
        ];
    }

    public function estimate(string $state, string $deliveryType = 'home_delivery'): array
    {
        $zone = DeliveryZone::active()->where('state', $state)->first();

        return [
            'fee' => $zone?->delivery_fee ?? self::DEFAULT_STANDARD_FEE,
            'estimated_days' => $zone?->estimated_days ?? 3,
            'delivery_type' => $deliveryType,
        ];
    }
}

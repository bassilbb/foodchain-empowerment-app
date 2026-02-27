<?php

namespace App\Services;

use App\Models\User;
use App\Models\MembershipTier;
use App\Models\PickupLocation;
use Illuminate\Support\Facades\Cache;

class DeliveryService
{
    private const BASE_DELIVERY_FEE = 1500;
    private const DEFAULT_PICKUP_FEE = 500;

    public function calculateDeliveryFee(User $user, float $baseFee = null): array
    {
        $baseFee = $baseFee ?? self::BASE_DELIVERY_FEE;
        
        $tier = $user->currentTier;

        if (!$tier || !$tier->has_delivery) {
            return [
                'available' => false,
                'reason' => $tier ? 'Delivery not available for ' . $tier->name : 'No tier assigned',
                'fee' => 0,
                'fee_kobo' => 0,
            ];
        }

        $calculatedFee = $tier->calculateDeliveryFee($baseFee);

        return [
            'available' => true,
            'tier' => $tier->name,
            'base_fee' => $baseFee,
            'calculated_fee' => $calculatedFee,
            'fee_kobo' => (int) ($calculatedFee * 100),
            'is_free' => $tier->hasFreeDelivery(),
            'subsidy_percentage' => (float) $tier->delivery_subsidy_percentage,
            'subsidy_applied' => $calculatedFee < $baseFee,
            'currency' => 'NGN',
        ];
    }

    public function getDeliveryOptions(User $user): array
    {
        $tier = $user->currentTier;
        
        $options = [
            'delivery' => [
                'available' => $tier?->has_delivery ?? false,
                'fee_details' => $this->calculateDeliveryFee($user),
            ],
            'pickup' => [
                'available' => true,
                'fee' => self::DEFAULT_PICKUP_FEE,
                'fee_kobo' => self::DEFAULT_PICKUP_FEE * 100,
                'locations' => $this->getPickupLocations(),
            ],
        ];

        return $options;
    }

    public function getPickupLocations(): array
    {
        return Cache::remember('pickup_locations', 3600, function () {
            return PickupLocation::active()
                ->select('id', 'name', 'address', 'city', 'state', 'operating_hours')
                ->get()
                ->toArray();
        });
    }

    public function calculateOrderDelivery(User $user, array $items, string $deliveryType = 'delivery'): array
    {
        $tier = $user->currentTier;
        $subtotal = collect($items)->sum('price');
        
        $deliveryFee = 0;
        $deliveryDetails = [];

        if ($deliveryType === 'delivery') {
            $deliveryCalculation = $this->calculateDeliveryFee($user);
            $deliveryFee = $deliveryCalculation['calculated_fee'];
            $deliveryDetails = $deliveryCalculation;
        } elseif ($deliveryType === 'pickup') {
            $deliveryFee = self::DEFAULT_PICKUP_FEE;
            $deliveryDetails = [
                'type' => 'pickup',
                'fee' => $deliveryFee,
                'fee_kobo' => $deliveryFee * 100,
                'locations' => $this->getPickupLocations(),
            ];
        }

        return [
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'delivery_fee_kobo' => (int) ($deliveryFee * 100),
            'total' => $subtotal + $deliveryFee,
            'total_kobo' => (int) (($subtotal + $deliveryFee) * 100),
            'delivery_details' => $deliveryDetails,
            'breakdown' => [
                'items' => $items,
                'delivery_type' => $deliveryType,
                'tier_benefit' => $tier?->name ?? 'None',
            ],
        ];
    }

    public function getDeliveryFeeBreakdown(MembershipTier $tier, float $baseFee = null): array
    {
        $baseFee = $baseFee ?? self::BASE_DELIVERY_FEE;

        if (!$tier->has_delivery) {
            return [
                'available' => false,
                'tier' => $tier->name,
            ];
        }

        $subsidyAmount = $tier->getDeliverySubsidyAmount($baseFee);
        $finalFee = $baseFee - $subsidyAmount;

        return [
            'available' => true,
            'tier' => $tier->name,
            'base_fee' => $baseFee,
            'subsidy_percentage' => (float) $tier->delivery_subsidy_percentage,
            'subsidy_amount' => $subsidyAmount,
            'final_fee' => max(0, $finalFee),
            'is_free' => $tier->hasFreeDelivery(),
            'tier_applies' => [
                'bronze' => 'Member pays full fee',
                'silver' => 'Member pays full fee',
                'gold' => '50% subsidy applied',
                'emerald' => 'Free delivery',
            ],
        ];
    }

    public function validateDeliveryAddress(array $address): array
    {
        $required = ['street', 'city', 'state'];
        $missing = [];

        foreach ($required as $field) {
            if (empty($address[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            return [
                'valid' => false,
                'errors' => "Missing required fields: " . implode(', ', $missing),
            ];
        }

        return [
            'valid' => true,
            'formatted' => "{$address['street']}, {$address['city']}, {$address['state']}",
        ];
    }

    public function estimateDeliveryTime(string $city, string $state): array
    {
        $majorCities = ['Lagos', 'Abuja', 'Port Harcourt', 'Ibadan', 'Kano'];
        
        $isMajorCity = in_array($city, $majorCities) || in_array($state, $majorCities);

        return [
            'estimated_days' => $isMajorCity ? '1-2' : '3-5',
            'delivery_type' => $isMajorCity ? 'express' : 'standard',
            'note' => $isMajorCity 
                ? 'Express delivery available' 
                : 'Standard delivery times apply',
        ];
    }
}

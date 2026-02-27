<?php


namespace App\Services;

use App\Models\User;
use App\Models\FoodDistribution;
use App\Models\FoodDistributionItem;
use App\Models\FoodPackage;
use App\Models\AdminUser;
use App\Models\PickupLocation;
use Illuminate\Support\Facades\DB;

class DistributionService
{
    /**
     * Create a tier reward distribution
     */
    public function createTierRewardDistribution(
        User $user,
        FoodPackage $package,
        PickupLocation $location,
        string $deliveryMethod = 'pickup'
    ): FoodDistribution {
        return DB::transaction(function () use ($user, $package, $location, $deliveryMethod) {
            // Create distribution
            $distribution = FoodDistribution::create([
                'user_id' => $user->id,
                'tier_id' => $user->current_tier_id,
                'package_id' => $package->id,
                'distribution_type' => 'tier_reward',
                'package_value' => $package->total_value,
                'status' => 'pending',
                'pickup_location_id' => $location->id,
                'delivery_method' => $deliveryMethod,
            ]);

            // Add items from package
            foreach ($package->items as $packageItem) {
                FoodDistributionItem::create([
                    'distribution_id' => $distribution->id,
                    'food_item_id' => $packageItem->food_item_id,
                    'quantity' => $packageItem->quantity,
                    'unit_value' => $packageItem->unit_value,
                ]);
            }

            return $distribution;
        });
    }

    /**
     * Create a discretionary distribution (admin-initiated)
     */
    public function createDiscretionaryDistribution(
        User $user,
        array $items,
        PickupLocation $location,
        AdminUser $admin,
        string $notes = null
    ): FoodDistribution {
        return DB::transaction(function () use ($user, $items, $location, $admin, $notes) {
            $totalValue = collect($items)->sum(fn($item) => $item['quantity'] * $item['unit_value']);

            $distribution = FoodDistribution::create([
                'user_id' => $user->id,
                'tier_id' => $user->current_tier_id,
                'distribution_type' => 'discretionary',
                'package_value' => $totalValue,
                'status' => 'approved', // Auto-approved since admin creates it
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'pickup_location_id' => $location->id,
                'admin_notes' => $notes,
            ]);

            // Add items
            foreach ($items as $item) {
                FoodDistributionItem::create([
                    'distribution_id' => $distribution->id,
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_value' => $item['unit_value'],
                ]);
            }

            return $distribution;
        });
    }

    /**
     * Approve a pending distribution
     */
    public function approveDistribution(
        FoodDistribution $distribution,
        AdminUser $admin,
        ?string $scheduledDate = null,
        ?string $notes = null
    ): bool {
        if (!$distribution->isPending()) {
            return false;
        }

        DB::transaction(function () use ($distribution, $admin, $scheduledDate, $notes) {
            $distribution->approve($admin, $notes);

            if ($scheduledDate) {
                $distribution->update(['scheduled_date' => $scheduledDate]);
            }

            // Reserve inventory
            $items = $distribution->items->map(function ($item) {
                return [
                    'food_item_id' => $item->food_item_id,
                    'quantity' => $item->quantity,
                ];
            })->toArray();

            app(InventoryService::class)->reserveStock($items, $distribution->pickupLocation);

            // Send notification
            app(NotificationService::class)->sendDistributionApprovedNotification($distribution);
        });

        return true;
    }

    /**
     * Mark distribution as dispatched
     */
    public function dispatchDistribution(
        FoodDistribution $distribution,
        ?string $trackingNumber = null,
        ?string $courierName = null
    ): void {
        DB::transaction(function () use ($distribution, $trackingNumber, $courierName) {
            $distribution->markAsDispatched($trackingNumber, $courierName);

            // Remove from reserved and deduct from actual inventory
            $inventoryService = app(InventoryService::class);
            
            foreach ($distribution->items as $item) {
                $inventory = $item->foodItem->inventory()
                                           ->where('location_id', $distribution->pickup_location_id)
                                           ->first();
                
                if ($inventory) {
                    $inventory->releaseReservedStock($item->quantity);
                    $inventory->removeStock($item->quantity);
                }
            }

            // Send notification
            app(NotificationService::class)->sendDistributionDispatchedNotification($distribution);
        });
    }

    /**
     * Get eligible users for distribution based on tier
     */
    public function getEligibleUsersForDistribution(int $tierId): array
    {
        return User::active()
            ->where('current_tier_id', $tierId)
            ->whereHas('distributions', function ($query) {
                // No distribution in current period
                $query->where('created_at', '>=', now()->subMonths(12));
            }, '<', 1)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'tier' => $user->currentTier->name,
                    'referrals' => $user->direct_referrals_count,
                ];
            })
            ->toArray();
    }

    /**
     * Check if user is eligible for discretionary distribution
     */
    public function isEligibleForDiscretionary(User $user): bool
    {
        $minReferrals = (int) \App\Models\Setting::get('min_referrals_for_discretionary', 5);
        
        return $user->direct_referrals_count >= $minReferrals 
            && $user->direct_referrals_count < 10; // Below Silver tier
    }
}
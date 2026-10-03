<?php

namespace App\Services;

use App\Models\FoodItem;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\PickupLocation;
use App\Models\AdminUser;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Add stock to inventory
     */
    public function addStock(
        FoodItem $foodItem,
        PickupLocation $location,
        float $quantity,
        float $costPerUnit,
        AdminUser $admin,
        ?string $notes = null,
        ?string $reference = null
    ): InventoryTransaction {
        return DB::transaction(function () use ($foodItem, $location, $quantity, $costPerUnit, $admin, $notes, $reference) {
            // Get or create inventory record
            $inventory = Inventory::firstOrCreate(
                [
                    'food_item_id' => $foodItem->id,
                    'location_id' => $location->id,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'cost_per_unit' => $costPerUnit,
                ]
            );

            $quantityBefore = $inventory->quantity;

            // Add stock
            $inventory->addStock($quantity, $costPerUnit);

            // Create transaction record
            return InventoryTransaction::create([
                'food_item_id' => $foodItem->id,
                'location_id' => $location->id,
                'type' => 'stock_in',
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $inventory->quantity,
                'unit_cost' => $costPerUnit,
                'reference' => $reference,
                'notes' => $notes,
                'performed_by' => $admin->id,
            ]);
        });
    }

    /**
     * Remove stock from inventory
     */
    public function removeStock(
        FoodItem $foodItem,
        PickupLocation $location,
        float $quantity,
        string $type,
        AdminUser $admin,
        ?string $notes = null,
        ?string $reference = null
    ): ?InventoryTransaction {
        return DB::transaction(function () use ($foodItem, $location, $quantity, $type, $admin, $notes, $reference) {
            $inventory = Inventory::where('food_item_id', $foodItem->id)
                                  ->where('location_id', $location->id)
                                  ->first();

            if (!$inventory || !$inventory->removeStock($quantity)) {
                return null; // Insufficient stock
            }

            $quantityAfter = $inventory->quantity;

            return InventoryTransaction::create([
                'food_item_id' => $foodItem->id,
                'location_id' => $location->id,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityAfter + $quantity,
                'quantity_after' => $quantityAfter,
                'unit_cost' => $inventory->cost_per_unit,
                'reference' => $reference,
                'notes' => $notes,
                'performed_by' => $admin->id,
            ]);
        });
    }

    /**
     * Reserve stock for a distribution
     */
    public function reserveStock(array $items, PickupLocation $location): bool
    {
        return DB::transaction(function () use ($items, $location) {
            foreach ($items as $item) {
                $inventory = Inventory::where('food_item_id', $item['food_item_id'])
                                     ->where('location_id', $location->id)
                                     ->lockForUpdate()
                                     ->first();

                if (!$inventory || !$inventory->reserveStock($item['quantity'])) {
                    DB::rollBack();
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Release reserved stock
     */
    public function releaseReservedStock(array $items, PickupLocation $location): void
    {
        DB::transaction(function () use ($items, $location) {
            foreach ($items as $item) {
                $inventory = Inventory::where('food_item_id', $item['food_item_id'])
                                     ->where('location_id', $location->id)
                                     ->first();

                if ($inventory) {
                    $inventory->releaseReservedStock($item['quantity']);
                }
            }
        });
    }

    /**
     * Get low stock items
     */
    public function getLowStockItems(float $threshold = 10): array
    {
        return FoodItem::active()
            ->get()
            ->filter(function ($item) use ($threshold) {
                return $item->isLowStock($threshold);
            })
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'unit' => $item->unit,
                    'current_stock' => $item->getTotalStock(),
                    'value' => $item->getTotalStockValue(),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get inventory summary
     */
    public function getInventorySummary(): array
    {
        $foodItems = FoodItem::with('inventory')->active()->get();

        return [
            'total_items' => $foodItems->count(),
            'total_value' => $foodItems->sum(fn($item) => $item->getTotalStockValue()),
            'low_stock_count' => $foodItems->filter(fn($item) => $item->isLowStock())->count(),
            'items' => $foodItems->map(function ($item) {
                return [
                    'name' => $item->name,
                    'stock' => $item->getTotalStock(),
                    'unit' => $item->unit,
                    'value' => $item->getTotalStockValue(),
                ];
            }),
        ];
    }
}
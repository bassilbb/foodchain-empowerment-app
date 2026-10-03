<?php
// app/Http/Controllers/Admin/InventoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Models\PickupLocation;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $foodItems = FoodItem::with('inventory.location')->active()->get();
        $locations = PickupLocation::active()->get();

        $inventorySummary = app(InventoryService::class)->getInventorySummary();

        return view('admin.inventory.index', compact('foodItems', 'locations', 'inventorySummary'));
    }

    public function transactions(Request $request)
    {
        $query = InventoryTransaction::with(['foodItem', 'location', 'performedBy']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by food item
        if ($request->filled('food_item_id')) {
            $query->where('food_item_id', $request->food_item_id);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->latest()->paginate(20);
        $foodItems = FoodItem::active()->get();
        $locations = PickupLocation::active()->get();

        return view('admin.inventory.transactions', compact('transactions', 'foodItems', 'locations'));
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'location_id' => 'required|exists:pickup_locations,id',
            'quantity' => 'required|numeric|min:0.01',
            'cost_per_unit' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'reference' => 'nullable|string|max:255',
        ]);

        $foodItem = FoodItem::findOrFail($request->food_item_id);
        $location = PickupLocation::findOrFail($request->location_id);
        $admin = Auth::guard('admin')->user();

        app(InventoryService::class)->addStock(
            $foodItem,
            $location,
            $request->quantity,
            $request->cost_per_unit,
            $admin,
            $request->notes,
            $request->reference
        );

        return back()->with('success', 'Stock added successfully.');
    }

    public function removeStock(Request $request)
    {
        $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'location_id' => 'required|exists:pickup_locations,id',
            'quantity' => 'required|numeric|min:0.01',
            'type' => 'required|in:stock_out,damaged,expired,adjustment',
            'notes' => 'nullable|string|max:500',
            'reference' => 'nullable|string|max:255',
        ]);

        $foodItem = FoodItem::findOrFail($request->food_item_id);
        $location = PickupLocation::findOrFail($request->location_id);
        $admin = Auth::guard('admin')->user();

        $transaction = app(InventoryService::class)->removeStock(
            $foodItem,
            $location,
            $request->quantity,
            $request->type,
            $admin,
            $request->notes,
            $request->reference
        );

        if (!$transaction) {
            return back()->with('error', 'Insufficient stock available.');
        }

        return back()->with('success', 'Stock removed successfully.');
    }

    public function lowStock()
    {
        $lowStockItems = app(InventoryService::class)->getLowStockItems();

        return view('admin.inventory.low-stock', compact('lowStockItems'));
    }
}

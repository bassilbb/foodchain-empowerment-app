<?php

// app/Http/Controllers/Admin/PackageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodPackage;
use App\Models\MembershipTier;
use App\Models\FoodItem;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = FoodPackage::with('tier', 'items.foodItem')->latest()->paginate(20);

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $tiers = MembershipTier::ordered()->get();
        $foodItems = FoodItem::active()->get();

        return view('admin.packages.create', compact('tiers', 'foodItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_value' => 'required|numeric|min:0',
        ]);

        $tier = MembershipTier::findOrFail($request->tier_id);
        $totalValue = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_value']);
        if ($totalValue > $tier->food_package_value) {
            return back()->withErrors(['items' => "Total package value (₦" . number_format($totalValue, 2) . ") exceeds {$tier->name} tier food package value (₦" . number_format($tier->food_package_value, 2) . ")."])->withInput();
        }

        $package = FoodPackage::create([
            'tier_id' => $request->tier_id,
            'name' => $request->name,
            'description' => $request->description,
            'total_value' => $totalValue,
            'is_default' => $request->boolean('is_default'),
        ]);

        foreach ($request->items as $item) {
            $package->items()->create([
                'food_item_id' => $item['food_item_id'],
                'quantity' => $item['quantity'],
                'unit_value' => $item['unit_value'],
            ]);
        }

        $package->update(['total_value' => $totalValue]);

        // If set as default, unset other defaults for this tier
        if ($request->boolean('is_default')) {
            FoodPackage::where('tier_id', $request->tier_id)
                ->where('id', '!=', $package->id)
                ->update(['is_default' => false]);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function show(FoodPackage $package)
    {
        $package->load(['tier', 'items.foodItem', 'distributions']);

        return view('admin.packages.show', compact('package'));
    }

    public function edit(FoodPackage $package)
    {
        $package->load('items.foodItem');
        $tiers = MembershipTier::ordered()->get();
        $foodItems = FoodItem::active()->get();

        return view('admin.packages.edit', compact('package', 'tiers', 'foodItems'));
    }

    public function update(Request $request, FoodPackage $package)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_value' => 'required|numeric|min:0',
        ]);

        $tier = MembershipTier::findOrFail($request->tier_id);
        $totalValue = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_value']);
        if ($totalValue > $tier->food_package_value) {
            return back()->withErrors(['items' => "Total package value (₦" . number_format($totalValue, 2) . ") exceeds {$tier->name} tier food package value (₦" . number_format($tier->food_package_value, 2) . ")."])->withInput();
        }

        $package->update([
            'tier_id' => $request->tier_id,
            'name' => $request->name,
            'description' => $request->description,
            'total_value' => $totalValue,
            'is_active' => $request->boolean('is_active'),
            'is_default' => $request->boolean('is_default'),
        ]);

        // Delete existing items and recreate
        $package->items()->delete();

        foreach ($request->items as $item) {
            $package->items()->create([
                'food_item_id' => $item['food_item_id'],
                'quantity' => $item['quantity'],
                'unit_value' => $item['unit_value'],
            ]);
        }

        // If set as default, unset other defaults
        if ($request->boolean('is_default')) {
            FoodPackage::where('tier_id', $request->tier_id)
                ->where('id', '!=', $package->id)
                ->update(['is_default' => false]);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(FoodPackage $package)
    {
        if ($package->distributions()->count() > 0) {
            return back()->with('error', 'Cannot delete package that has been distributed.');
        }

        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }
}


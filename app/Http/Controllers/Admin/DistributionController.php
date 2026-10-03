<?php

// app/Http/Controllers/Admin/DistributionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodDistribution;
use App\Models\FoodItem;
use App\Models\FoodPackage;
use App\Models\PickupLocation;
use App\Models\User;
use App\Services\DistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributionController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodDistribution::with(['user', 'tier', 'pickupLocation']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('distribution_type', $request->type);
        }

        // Filter by tier
        if ($request->filled('tier_id')) {
            $query->where('tier_id', $request->tier_id);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('distribution_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $distributions = $query->latest()->paginate(20);

        return view('admin.distributions.index', compact('distributions'));
    }

    public function create()
    {
        $users = User::active()->get();
        $foodItems = FoodItem::active()->get();
        $locations = PickupLocation::active()->get();
        $packages = FoodPackage::active()->get();

        return view('admin.distributions.create', compact('users', 'foodItems', 'locations', 'packages'));
    }

    public function packageItems(FoodPackage $package)
    {
        $items = $package->items()->with('foodItem')->get()->map(function ($pi) {
            return [
                'food_item_id' => $pi->food_item_id,
                'food_item_name' => $pi->foodItem->name,
                'quantity' => $pi->quantity,
                'unit_value' => $pi->unit_value,
            ];
        });

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:pickup_locations,id',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_value' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = User::findOrFail($request->user_id);
        $location = PickupLocation::findOrFail($request->location_id);
        $admin = Auth::guard('admin')->user();

        $distribution = app(DistributionService::class)->createDiscretionaryDistribution(
            $user,
            $request->items,
            $location,
            $admin,
            $request->notes
        );

        return redirect()->route('admin.distributions.show', $distribution)
            ->with('success', 'Discretionary distribution created successfully.');
    }

    public function edit(FoodDistribution $distribution)
    {
        $users = User::active()->get();
        $foodItems = FoodItem::active()->get();
        $locations = PickupLocation::active()->get();

        $distribution->load('items');

        return view('admin.distributions.edit', compact('distribution', 'users', 'foodItems', 'locations'));
    }

    public function update(Request $request, FoodDistribution $distribution)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:pickup_locations,id',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_value' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,packaged,dispatched,delivered,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $distribution->update([
            'user_id' => $request->user_id,
            'pickup_location_id' => $request->location_id,
            'status' => $request->status,
            'admin_notes' => $request->notes,
        ]);

        // Update items
        $distribution->items()->delete();
        $totalValue = 0;
        foreach ($request->items as $item) {
            $distribution->items()->create([
                'food_item_id' => $item['food_item_id'],
                'quantity' => $item['quantity'],
                'unit_value' => $item['unit_value'],
                'total_value' => $item['quantity'] * $item['unit_value'],
            ]);
            $totalValue += $item['quantity'] * $item['unit_value'];
        }

        $distribution->update(['total_value' => $totalValue]);

        return redirect()->route('admin.distributions.show', $distribution)
            ->with('success', 'Distribution updated successfully.');
    }

    public function show(FoodDistribution $distribution)
    {
        $distribution->load([
            'user',
            'tier',
            'package',
            'items.foodItem',
            'pickupLocation',
            'approvedBy',
            'packagedBy',
        ]);

        return view('admin.distributions.show', compact('distribution'));
    }

    public function approve(Request $request, FoodDistribution $distribution)
    {
        $request->validate([
            'scheduled_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $admin = Auth::guard('admin')->user();

        $success = app(DistributionService::class)->approveDistribution(
            $distribution,
            $admin,
            $request->scheduled_date,
            $request->notes
        );

        if (! $success) {
            return back()->with('error', 'Distribution cannot be approved. It may already be processed.');
        }

        return back()->with('success', 'Distribution approved successfully.');
    }

    public function markAsPackaged(FoodDistribution $distribution)
    {
        $admin = Auth::guard('admin')->user();
        $distribution->markAsPackaged($admin);

        return back()->with('success', 'Distribution marked as packaged.');
    }

    public function dispatch(Request $request, FoodDistribution $distribution)
    {
        $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'courier_name' => 'nullable|string|max:255',
        ]);

        app(DistributionService::class)->dispatchDistribution(
            $distribution,
            $request->tracking_number,
            $request->courier_name
        );

        return back()->with('success', 'Distribution dispatched successfully.');
    }

    public function deliver(FoodDistribution $distribution)
    {
        $distribution->markAsDelivered();

        return back()->with('success', 'Distribution marked as delivered.');
    }

    public function cancel(Request $request, FoodDistribution $distribution)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $distribution->cancel($request->reason);

        return back()->with('success', 'Distribution cancelled.');
    }
}

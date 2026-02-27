<?php

// app/Http/Controllers/Admin/FoodItemController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class FoodItemController extends Controller
{
    public function index()
    {
        $foodItems = FoodItem::latest()->paginate(20);

        return view('admin.food-items.index', compact('foodItems'));
    }

    public function create()
    {
        return view('admin.food-items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:food_items,name',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'unit', 'unit_price']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('food-items', 'public');
        }

        FoodItem::create($data);

        return redirect()->route('admin.food-items.index')
            ->with('success', 'Food item created successfully.');
    }

    public function show(FoodItem $foodItem)
    {
        $foodItem->load('inventory.location');

        return view('admin.food-items.show', compact('foodItem'));
    }

    public function edit(FoodItem $foodItem)
    {
        return view('admin.food-items.edit', compact('foodItem'));
    }

    public function update(Request $request, FoodItem $foodItem)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:food_items,name,' . $foodItem->id,
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'unit', 'unit_price', 'is_active']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($foodItem->image) {
                Storage::disk('public')->delete($foodItem->image);
            }
            $data['image'] = $request->file('image')->store('food-items', 'public');
        }

        $foodItem->update($data);

        return redirect()->route('admin.food-items.index')
            ->with('success', 'Food item updated successfully.');
    }

    public function destroy(FoodItem $foodItem)
    {
        // Check if item is used in any packages or distributions
        if ($foodItem->packageItems()->count() > 0 || $foodItem->distributionItems()->count() > 0) {
            return back()->with('error', 'Cannot delete food item that is used in packages or distributions.');
        }

        $foodItem->delete();

        return redirect()->route('admin.food-items.index')
            ->with('success', 'Food item deleted successfully.');
    }
}

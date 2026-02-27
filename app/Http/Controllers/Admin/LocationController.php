<?php

// app/Http/Controllers/Admin/LocationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupLocation;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = PickupLocation::latest()->paginate(20);

        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'operating_hours' => 'nullable|string',
            'is_headquarters' => 'boolean',
        ]);

        PickupLocation::create($request->all());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function show(PickupLocation $location)
    {
        $location->load(['inventory.foodItem', 'distributions']);

        return view('admin.locations.show', compact('location'));
    }

    public function edit(PickupLocation $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, PickupLocation $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'operating_hours' => 'nullable|string',
            'is_active' => 'boolean',
            'is_headquarters' => 'boolean',
        ]);

        $location->update($request->all());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(PickupLocation $location)
    {
        if ($location->is_headquarters) {
            return back()->with('error', 'Cannot delete headquarters location.');
        }

        if ($location->inventory()->count() > 0) {
            return back()->with('error', 'Cannot delete location with existing inventory. Please transfer or remove inventory first.');
        }

        if ($location->distributions()->count() > 0) {
            return back()->with('error', 'Cannot delete location with distribution history.');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}

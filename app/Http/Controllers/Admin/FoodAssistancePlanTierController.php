<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodAssistancePlanTier;
use Illuminate\Http\Request;

class FoodAssistancePlanTierController extends Controller
{
    public function index()
    {
        $tiers = FoodAssistancePlanTier::orderBy('amount')->get();

        return view('admin.food-assistance-tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('admin.food-assistance-tiers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        FoodAssistancePlanTier::create($validated);

        return redirect()->route('admin.food-assistance-tiers.index')
            ->with('success', 'Plan tier created successfully.');
    }

    public function edit(FoodAssistancePlanTier $tier)
    {
        return view('admin.food-assistance-tiers.edit', compact('tier'));
    }

    public function update(Request $request, FoodAssistancePlanTier $tier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $tier->update($validated);

        return redirect()->route('admin.food-assistance-tiers.index')
            ->with('success', 'Plan tier updated successfully.');
    }

    public function destroy(FoodAssistancePlanTier $tier)
    {
        if ($tier->plans()->exists()) {
            return back()->with('error', 'Cannot delete tier with active plans.');
        }

        $tier->delete();

        return redirect()->route('admin.food-assistance-tiers.index')
            ->with('success', 'Plan tier deleted successfully.');
    }
}

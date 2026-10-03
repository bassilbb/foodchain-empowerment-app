<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodSupportPlan;
use Illuminate\Http\Request;

class FoodSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodSupportPlan::with('user');

        if ($request->status === 'active') {
            $query->whereIn('status', ['pending', 'active', 'fully_funded']);
        } elseif ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->plan_type) {
            $query->where('plan_type', $request->plan_type);
        }

        $plans = $query->latest()->paginate(20);

        $stats = [
            'active' => FoodSupportPlan::whereIn('status', ['pending', 'active', 'fully_funded'])->count(),
            'fully_funded' => FoodSupportPlan::where('status', 'fully_funded')->count(),
            'matured' => FoodSupportPlan::where('status', 'matured')->count(),
            'withdrawn' => FoodSupportPlan::where('status', 'withdrawn')->count(),
            'total_contributions' => FoodSupportPlan::sum('total_contributed'),
            'total_payout' => FoodSupportPlan::sum('total_payout_value'),
        ];

        return view('admin.food-support.index', compact('plans', 'stats'));
    }

    public function show(FoodSupportPlan $supportPlan)
    {
        $supportPlan->load('user', 'contributions');
        $contributions = $supportPlan->contributions()->latest()->get();

        return view('admin.food-support.show', compact('supportPlan', 'contributions'));
    }

    public function markMatured(FoodSupportPlan $supportPlan)
    {
        if (! in_array($supportPlan->status, ['active', 'fully_funded'])) {
            return back()->with('error', 'Only active or fully funded plans can be marked as matured.');
        }

        $supportPlan->processMaturity();

        return back()->with('success', 'Plan marked as matured. 20% top-up has been applied.');
    }

    public function processWithdrawal(FoodSupportPlan $supportPlan)
    {
        if ($supportPlan->status !== 'matured') {
            return back()->with('error', 'Only matured plans can be withdrawn.');
        }

        try {
            $supportPlan->processWithdrawal();

            return back()->with('success', 'Withdrawal processed successfully. Distribution and voucher created.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, FoodSupportPlan $supportPlan)
    {
        if (in_array($supportPlan->status, ['matured', 'withdrawn', 'cancelled'])) {
            return back()->with('error', 'Plan cannot be cancelled in its current state.');
        }

        $supportPlan->update(['status' => 'cancelled']);

        return back()->with('success', 'Plan cancelled successfully.');
    }
}

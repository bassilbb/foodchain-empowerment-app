<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FoodSupportPlan;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class FoodSupportController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct()
    {
        $this->paymentService = app(PaymentService::class);
    }

    public function index()
    {
        $user = auth()->user();
        $plans = FoodSupportPlan::with('contributions')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('user.food-support.index', compact('plans'));
    }

    public function create()
    {
        return view('user.food-support.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_type' => 'required|in:six_months,one_year',
            'plan_value' => 'required|numeric|min:1000',
            'terms' => 'accepted',
        ]);

        $user = auth()->user();

        $plan = FoodSupportPlan::create([
            'user_id' => $user->id,
            'plan_type' => $validated['plan_type'],
            'plan_value' => $validated['plan_value'],
            'top_up_percentage' => 20.00,
            'status' => 'pending',
            'start_date' => now()->format('Y-m-d'),
        ]);

        // Redirect to first contribution
        $result = $this->paymentService->initializePayment(
            $user,
            'support_plan_contribution',
            $validated['plan_value'],
            [
                'support_plan_id' => $plan->id,
                'payment_for' => 'Food Support Plan - Initial Contribution',
            ]
        );

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return redirect()->route('user.food-support.show', $plan)
            ->with('error', 'Payment initialization failed. Please try again.');
    }

    public function show(FoodSupportPlan $supportPlan)
    {
        if ($supportPlan->user_id !== auth()->id()) {
            abort(403);
        }
        $supportPlan->load('contributions');
        $contributions = $supportPlan->contributions()->latest()->get();
        $progress = $supportPlan->plan_value > 0
            ? min(100, ($supportPlan->total_contributed / $supportPlan->plan_value) * 100)
            : 0;

        return view('user.food-support.show', compact('supportPlan', 'contributions', 'progress'));
    }

    public function makeContribution(Request $request, FoodSupportPlan $supportPlan)
    {
        if ($supportPlan->user_id !== auth()->id()) {
            abort(403);
        }
        if (in_array($supportPlan->status, ['matured', 'withdrawn', 'cancelled'])) {
            return back()->with('error', 'Plan is no longer accepting contributions.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $remaining = $supportPlan->plan_value - $supportPlan->total_contributed;
        $amount = min($validated['amount'], $remaining);

        $result = $this->paymentService->initializePayment(
            auth()->user(),
            'support_plan_contribution',
            $amount,
            [
                'support_plan_id' => $supportPlan->id,
            ]
        );

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return back()->with('error', 'Payment initialization failed.');
    }
}

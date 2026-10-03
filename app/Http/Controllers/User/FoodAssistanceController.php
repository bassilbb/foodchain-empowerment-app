<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FoodAssistancePlan;
use App\Models\FoodAssistancePlanTier;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class FoodAssistanceController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct()
    {
        $this->paymentService = app(PaymentService::class);
    }

    public function index()
    {
        $user = auth()->user();
        $plans = FoodAssistancePlan::with('tier', 'installments')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('user.food-assistance.index', compact('plans'));
    }

    public function create()
    {
        $tiers = FoodAssistancePlanTier::active()->orderBy('amount')->get();

        return view('user.food-assistance.create', compact('tiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tier_id' => 'required|exists:food_assistance_plan_tiers,id',
            'terms' => 'accepted',
        ]);

        $tier = FoodAssistancePlanTier::findOrFail($validated['tier_id']);
        $user = auth()->user();

        $totalAmount = $tier->amount;
        $firstInstallmentAmount = $totalAmount * 0.30;
        $balanceAmount = $totalAmount - $firstInstallmentAmount;
        $weeklyInstallmentAmount = $balanceAmount / 12;

        $plan = FoodAssistancePlan::create([
            'user_id' => $user->id,
            'tier_id' => $tier->id,
            'total_amount' => $totalAmount,
            'processing_fee' => 3500.00,
            'first_installment_percentage' => 30.00,
            'first_installment_amount' => $firstInstallmentAmount,
            'balance_amount' => $balanceAmount,
            'weekly_installment_amount' => $weeklyInstallmentAmount,
            'number_of_installments' => 12,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        $plan->generateInstallmentSchedule();

        // Redirect to pay processing fee
        $result = $this->paymentService->initializePayment(
            $user,
            'assistance_processing_fee',
            3500.00,
            [
                'assistance_plan_id' => $plan->id,
                'payment_for' => 'Food Assistance Plan - Processing Fee',
            ]
        );

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return redirect()->route('user.food-assistance.show', $plan)
            ->with('error', 'Payment initialization failed. Please try again.');
    }

    public function show(FoodAssistancePlan $assistancePlan)
    {
        if ($assistancePlan->user_id !== auth()->id()) {
            abort(403);
        }
        $assistancePlan->load('tier', 'installments');
        $installments = $assistancePlan->installments()->orderBy('installment_number')->get();
        $nextInstallment = $assistancePlan->getNextPendingInstallment();

        return view('user.food-assistance.show', compact('assistancePlan', 'installments', 'nextInstallment'));
    }

    public function payProcessingFee(FoodAssistancePlan $assistancePlan)
    {
        if ($assistancePlan->user_id !== auth()->id()) {
            abort(403);
        }
        if ($assistancePlan->status !== 'pending') {
            return back()->with('error', 'Processing fee has already been paid.');
        }

        if (! $assistancePlan->approved_at) {
            return back()->with('error', 'Plan must be approved by an admin before paying the processing fee.');
        }

        $result = $this->paymentService->initializePayment(
            auth()->user(),
            'assistance_processing_fee',
            $assistancePlan->processing_fee,
            [
                'assistance_plan_id' => $assistancePlan->id,
            ]
        );

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return back()->with('error', 'Payment initialization failed.');
    }

    public function payFirstInstallment(FoodAssistancePlan $assistancePlan)
    {
        if ($assistancePlan->user_id !== auth()->id()) {
            abort(403);
        }
        if ($assistancePlan->status !== 'processing_fee_paid') {
            return back()->with('error', 'Processing fee must be paid first.');
        }

        $result = $this->paymentService->initializePayment(
            auth()->user(),
            'assistance_first_installment',
            $assistancePlan->first_installment_amount,
            [
                'assistance_plan_id' => $assistancePlan->id,
            ]
        );

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return back()->with('error', 'Payment initialization failed.');
    }

    public function payWeeklyInstallment(FoodAssistancePlan $assistancePlan)
    {
        if ($assistancePlan->user_id !== auth()->id()) {
            abort(403);
        }
        if ($assistancePlan->status !== 'active') {
            return back()->with('error', 'Plan must be active to pay weekly installments.');
        }

        $next = $assistancePlan->getNextPendingInstallment();
        if (! $next || $next->type !== 'weekly_repayment') {
            return back()->with('error', 'No pending weekly installment found.');
        }

        $result = $this->paymentService->initializePayment(
            auth()->user(),
            'assistance_weekly_repayment',
            $next->amount_due,
            [
                'assistance_plan_id' => $assistancePlan->id,
                'installment_number' => $next->installment_number,
            ]
        );

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return back()->with('error', 'Payment initialization failed.');
    }
}

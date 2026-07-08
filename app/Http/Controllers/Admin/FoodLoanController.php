<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodLoan;
use App\Models\LoanRepayment;
use App\Models\Setting;
use App\Services\FoodLoanEligibilityService;
use Illuminate\Http\Request;

class FoodLoanController extends Controller
{
    protected FoodLoanEligibilityService $eligibilityService;

    public function __construct()
    {
        $this->eligibilityService = new FoodLoanEligibilityService;
    }

    public function index(Request $request)
    {
        $query = FoodLoan::with('union');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pending' => FoodLoan::where('status', 'pending')->count(),
            'approved' => FoodLoan::where('status', 'approved')->count(),
            'disbursed' => FoodLoan::where('status', 'disbursed')->count(),
            'repaying' => FoodLoan::where('status', 'repaying')->count(),
            'completed' => FoodLoan::where('status', 'completed')->count(),
            'defaulted' => FoodLoan::where('status', 'defaulted')->count(),
            'total_disbursed' => FoodLoan::whereIn('status', ['disbursed', 'repaying', 'completed'])->sum('amount_disbursed'),
            'total_repaid' => FoodLoan::sum('amount_repaid'),
        ];

        return view('admin.food-loans.index', compact('loans', 'stats'));
    }

    public function show(FoodLoan $foodLoan)
    {
        $foodLoan->load('union', 'repayments');

        $repayments = $foodLoan->repayments()->orderBy('installment_number')->get();
        $pendingRepayments = $foodLoan->pendingRepayments;

        return view('admin.food-loans.show', compact('foodLoan', 'repayments', 'pendingRepayments'));
    }

    public function approve(Request $request, FoodLoan $foodLoan)
    {
        $request->validate([
            'approved_amount' => 'required|numeric|min:1',
        ]);

        $foodLoan->update([
            'loan_amount_approved' => $request->approved_amount,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Loan approved successfully.');
    }

    public function disburse(FoodLoan $foodLoan)
    {
        if ($foodLoan->status !== 'approved') {
            return back()->with('error', 'Only approved loans can be disbursed.');
        }

        $foodLoan->disburse();

        return back()->with('success', 'Loan disbursed successfully. Repayment schedule has been generated.');
    }

    public function reject(FoodLoan $foodLoan)
    {
        $foodLoan->update(['status' => 'rejected']);

        return back()->with('success', 'Loan rejected.');
    }

    public function updateRepayment(Request $request, LoanRepayment $repayment)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,partial,overdue',
        ]);

        $repayment->update([
            'amount_paid' => $request->amount_paid,
            'status' => $request->status,
            'paid_date' => $request->status === 'paid' ? now() : null,
        ]);

        $foodLoan = $repayment->foodLoan;
        $totalPaid = $foodLoan->repayments()->where('status', 'paid')->sum('amount_paid');
        $totalPaid += $foodLoan->repayments()->where('status', 'partial')->sum('amount_paid');

        $foodLoan->update([
            'amount_repaid' => $totalPaid,
            'remaining_balance' => $foodLoan->total_repayment_amount - $totalPaid,
            'installments_paid' => $foodLoan->repayments()->where('status', 'paid')->count(),
        ]);

        if ($totalPaid >= $foodLoan->total_repayment_amount) {
            $foodLoan->update(['status' => 'completed']);
        }

        return back()->with('success', 'Repayment updated successfully.');
    }

    public function eligibilityRules()
    {
        $rules = $this->eligibilityService->getRules();

        return view('admin.food-loans.eligibility-rules', compact('rules'));
    }

    public function updateEligibilityRule(Request $request, string $ruleKey)
    {
        $rule = $this->eligibilityService->getRule($ruleKey);

        if (! $rule) {
            return back()->with('error', 'Rule not found.');
        }

        $data = [];

        if ($rule['type'] === 'boolean') {
            $data['enabled'] = $request->has('enabled');
        } else {
            $data['enabled'] = $request->has('enabled');
            $data['value'] = (int) $request->input('value', $rule['value'] ?? 0);
        }

        $this->eligibilityService->updateRule($ruleKey, $data);

        return back()->with('success', "Rule '{$rule['name']}' updated successfully.");
    }

    public function toggleRule(Request $request, string $ruleKey)
    {
        $rule = $this->eligibilityService->getRule($ruleKey);

        if (! $rule) {
            return response()->json(['error' => 'Rule not found'], 404);
        }

        $settingKey = "loan_eligibility_require_{$ruleKey}";
        $currentValue = Setting::get($settingKey, false);
        Setting::set($settingKey, ! $currentValue);

        $this->eligibilityService->loadRules();

        return response()->json([
            'success' => true,
            'enabled' => ! $currentValue,
        ]);
    }

    public function updateBatchRules(Request $request)
    {
        $rules = $request->input('rules', []);

        foreach ($rules as $ruleKey => $data) {
            $rule = $this->eligibilityService->getRule($ruleKey);

            if (! $rule) {
                continue;
            }

            $this->eligibilityService->updateRule($ruleKey, [
                'enabled' => isset($data['enabled']) && $data['enabled'] == '1',
                'value' => isset($data['value']) ? (int) $data['value'] : null,
            ]);
        }

        return back()->with('success', 'Eligibility rules updated successfully.');
    }

    public function checkEligibility(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::find($request->user_id);
        $result = $this->eligibilityService->setUser($user)->checkEligibility();

        return response()->json($result);
    }
}

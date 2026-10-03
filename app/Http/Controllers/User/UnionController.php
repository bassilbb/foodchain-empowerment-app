<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Union;
use App\Services\FoodLoanEligibilityService;
use App\Services\MembershipGradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnionController extends Controller
{
    private MembershipGradeService $membershipService;

    private FoodLoanEligibilityService $eligibilityService;

    public function __construct(MembershipGradeService $membershipService, FoodLoanEligibilityService $eligibilityService)
    {
        $this->membershipService = $membershipService;
        $this->eligibilityService = $eligibilityService;
    }

    public function index()
    {
        $user = Auth::user();

        $unions = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->with(['tier', 'leader', 'members.user'])->get();

        $myUnion = $unions->first();

        if ($myUnion) {
            $loan = $myUnion->foodLoan;
        } else {
            $loan = null;
        }

        return view('user.unions.index', compact('unions', 'myUnion', 'loan'));
    }

    public function create()
    {
        $user = Auth::user();

        $existingUnion = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->whereIn('status', ['forming', 'active', 'disbursed', 'repaying'])->first();

        if ($existingUnion) {
            return redirect()->route('unions.show', $existingUnion->id)
                ->with('error', 'You already belong to an active union.');
        }

        $tier = $user->currentTier;

        if (! $tier) {
            return redirect()->route('dashboard')
                ->with('error', 'You need an active membership to create a union.');
        }

        return view('user.unions.create', compact('tier'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $existingUnion = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->whereIn('status', ['forming', 'active', 'disbursed', 'repaying'])->first();

        if ($existingUnion) {
            return redirect()->route('unions.show', $existingUnion->id)
                ->with('error', 'You already belong to an active union.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:unions,name',
            'equity_contribution' => 'nullable|numeric|min:0',
        ]);

        try {
            $equityContribution = $request->input('equity_contribution', 0);
            $union = $this->membershipService->createUnion($user, $request->name);

            if ($equityContribution > 0) {
                $member = $union->members()->where('user_id', $user->id)->first();
                if ($member) {
                    $member->update(['equity_contributed' => $equityContribution]);
                    $union->update(['total_equity_contributed' => $equityContribution]);
                }
            }

            return redirect()->route('unions.show', $union->id)
                ->with('success', 'Union created successfully! Share the union code with members to join.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        $user = Auth::user();

        $union = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->with(['tier', 'leader', 'members.user', 'foodLoan'])->findOrFail($id);

        $isLeader = $union->leader_id === $user->id;

        return view('user.unions.show', compact('union', 'isLeader'));
    }

    public function join(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'union_code' => 'required|string',
        ]);

        $union = Union::where('code', $request->union_code)
            ->whereIn('status', ['forming', 'active'])
            ->first();

        if (! $union) {
            return back()->with('error', 'Invalid union code or union is not accepting new members.');
        }

        $existingUnion = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->whereIn('status', ['forming', 'active', 'disbursed', 'repaying'])->first();

        if ($existingUnion) {
            return back()->with('error', 'You already belong to an active union. Leave it first to join another.');
        }

        try {
            $equityContribution = $request->input('equity_contribution', 0);
            $this->membershipService->joinUnion($user, $union, $equityContribution);

            return redirect()->route('unions.show', $union->id)
                ->with('success', 'You have successfully joined the union!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function leave(string $id)
    {
        $user = Auth::user();

        $union = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->findOrFail($id);

        if ($union->leader_id === $user->id) {
            return back()->with('error', 'As the union leader, you cannot leave. Transfer leadership first or dissolve the union.');
        }

        $member = $union->members()->where('user_id', $user->id)->first();

        if ($member) {
            try {
                $union->removeMember($member);

                return redirect()->route('unions.index')
                    ->with('success', 'You have left the union.');
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return back()->with('error', 'You are not a member of this union.');
    }

    public function applyLoan(Request $request, string $id)
    {
        $user = Auth::user();

        $union = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->findOrFail($id);

        if ($union->leader_id !== $user->id) {
            return back()->with('error', 'Only the union leader can apply for a loan.');
        }

        if ($union->foodLoan && in_array($union->foodLoan->status, ['pending', 'approved', 'disbursed', 'repaying'])) {
            return back()->with('error', 'The union already has an active loan.');
        }

        $eligibility = $this->eligibilityService->setUser($user)->checkEligibility();

        if (! $eligibility['eligible']) {
            $failedRules = collect($eligibility['failed'])->pluck('name')->toArray();
            $failedMessage = 'You do not meet the eligibility requirements: '.implode(', ', $failedRules);

            if (session('error')) {
                session()->keep(['error']);
            }

            return back()->with('error', $failedMessage);
        }

        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        try {
            $loan = $this->membershipService->applyForFoodLoan($union, $request->amount);

            return redirect()->route('unions.show', $union->id)
                ->with('success', 'Loan application submitted successfully! Pending admin approval.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function makeRepayment(Request $request, string $id)
    {
        $user = Auth::user();

        $union = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->with('foodLoan')->findOrFail($id);

        $loan = $union->foodLoan;

        if (! $loan || ! in_array($loan->status, ['disbursed', 'repaying'])) {
            return back()->with('error', 'No active loan found for repayment.');
        }

        $member = $union->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return back()->with('error', 'You are not a member of this union.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        try {
            $this->membershipService->recordLoanRepayment($loan, $user, $request->amount);

            return redirect()->route('unions.show', $union->id)
                ->with('success', 'Repayment recorded successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getLoanDetails(string $id)
    {
        $user = Auth::user();

        $union = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->with(['foodLoan', 'foodLoan.repayments', 'foodLoan.union'])->findOrFail($id);

        $loan = $union->foodLoan;

        if (! $loan) {
            return response()->json(['error' => 'No loan found'], 404);
        }

        return response()->json([
            'loan' => [
                'id' => $loan->id,
                'reference' => $loan->reference,
                'principal_amount' => $loan->principal_amount,
                'loan_amount_approved' => $loan->loan_amount_approved,
                'remaining_balance' => $loan->remaining_balance,
                'status' => $loan->status,
                'installments_paid' => $loan->installments_paid,
                'total_installments' => $loan->total_installments,
                'next_payment_due' => $loan->next_payment_due?->format('Y-m-d'),
            ],
            'union' => [
                'name' => $union->name,
                'code' => $union->code,
            ],
        ]);
    }

    public function repaymentSchedule(string $id)
    {
        $user = Auth::user();

        $union = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->with(['foodLoan', 'foodLoan.repayments'])->findOrFail($id);

        $loan = $union->foodLoan;

        if (! $loan) {
            return back()->with('error', 'No loan found for this union.');
        }

        $repayments = $loan->repayments()->orderBy('installment_number')->get();
        $isLeader = $union->members()->where('user_id', $user->id)->where('is_leader', true)->exists();

        return view('user.unions.repayment-schedule', compact('union', 'loan', 'repayments', 'isLeader'));
    }
}

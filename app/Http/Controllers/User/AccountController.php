<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\BankAccount;
use App\Models\ClearanceRequest;
use App\Models\SavingsPlan;
use App\Models\SavingsPlanOption;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $accounts = $user->linkedAccounts()
            ->with('plan', 'savingsPlanOption.plan')
            ->latest()
            ->get();

        return view('user.accounts.index', compact('user', 'accounts'));
    }

    public function create()
    {
        $savingsPlans = SavingsPlan::with('activeOptions')->active()->get();

        $plansData = $savingsPlans->mapWithKeys(function ($plan) {
            return [$plan->id => [
                'name' => $plan->name,
                'target' => $plan->target_amount,
                'options' => $plan->activeOptions->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'weeks' => $option->duration_weeks,
                        'amount' => (float) $option->weekly_contribution,
                    ];
                }),
            ]];
        });

        return view('user.accounts.create', compact('savingsPlans', 'plansData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'savings_plan_option_id' => 'required|exists:savings_plan_options,id',
            'referral_code' => 'nullable|string|max:8|exists:users,referral_code',
        ]);

        $user = auth()->user();
        $option = SavingsPlanOption::with('plan')->findOrFail($request->savings_plan_option_id);

        $referredBy = null;
        if ($request->filled('referral_code')) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        UserAccount::create([
            'primary_user_id' => $user->id,
            'account_name' => $request->account_name,
            'savings_plan_option_id' => $option->id,
            'weekly_contribution_amount' => $option->weekly_contribution,
            'current_tariff_weeks' => 0,
            'status' => 'active',
            'referred_by' => $referredBy,
        ]);

        return redirect()->route('user.accounts.index')
            ->with('success', 'Account created successfully!');
    }

    public function show(UserAccount $account)
    {
        $user = auth()->user();

        if ($account->primary_user_id !== $user->id) {
            abort(403);
        }

        $account->load('plan', 'savingsPlanOption.plan', 'clearanceRequest');
        $transactions = $account->transactions()->latest()->paginate(15);

        return view('user.accounts.show', compact('user', 'account', 'transactions'));
    }

    public function switchAccount($id)
    {
        $user = auth()->user();
        $account = $user->linkedAccounts()->findOrFail($id);

        session(['active_account_id' => $account->id]);

        return redirect()->route('user.accounts.show', $account->id)
            ->with('success', 'Switched to account: '.$account->account_name);
    }

    public function wallet()
    {
        $user = auth()->user();
        $walletService = app(WalletService::class);
        $balance = $walletService->getBalance($user);

        $transactions = $user->accountTransactions()->latest()->paginate(15);
        $bankAccounts = BankAccount::getAllActive();

        return view('user.accounts.wallet', compact('user', 'balance', 'transactions', 'bankAccounts'));
    }

    public function submitTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'sender_name' => 'required|string|max:255',
            'transfer_date' => 'required|date',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        $bankAccount = BankAccount::findOrFail($request->bank_account_id);

        $proofPath = $request->file('proof_image')->store('manual-payments/proofs', 'public');

        $manualPayment = \App\Models\ManualPayment::create([
            'user_id' => $user->id,
            'type' => 'wallet_topup',
            'amount' => $request->amount,
            'reference' => \App\Models\ManualPayment::generateReference(),
            'bank_account_id' => $bankAccount->id,
            'sender_name' => $request->sender_name,
            'sender_account_number' => $request->sender_account_number,
            'sender_bank_name' => $request->sender_bank_name,
            'transfer_date' => $request->transfer_date,
            'proof_image_path' => $proofPath,
            'status' => 'pending',
        ]);

        return redirect()->route('user.accounts.wallet')
            ->with('success', 'Top-up of ₦'.number_format($request->amount, 2).' submitted successfully. Your wallet will be credited once payment is verified.');
    }

    public function transactions(Request $request, ?UserAccount $account = null)
    {
        $user = auth()->user();

        if ($account) {
            if ($account->primary_user_id !== $user->id) {
                abort(403);
            }
            $transactions = $account->transactions()->with('account')->latest()->paginate(15);
        } else {
            $transactions = $user->accountTransactions()->with('account')->latest()->paginate(15);
        }

        return view('user.accounts.transactions', compact('user', 'transactions', 'account'));
    }

    public function clearances()
    {
        $user = auth()->user();

        $accounts = $user->linkedAccounts()
            ->with('savingsPlanOption.plan', 'clearanceRequest')
            ->where('status', '!=', 'completed')
            ->get()
            ->filter(function ($account) {
                return $account->isMatured();
            })
            ->values();

        return view('user.accounts.clearances', compact('user', 'accounts'));
    }

    public function payClearanceFee(UserAccount $account)
    {
        $user = auth()->user();

        if ($account->primary_user_id !== $user->id) {
            abort(403);
        }

        $account->load('savingsPlanOption.plan');

        if (! $account->isMatured()) {
            return back()->with('error', 'This account has not reached its maturity yet.');
        }

        $clearanceRequest = $account->clearanceRequest;

        if (! $clearanceRequest) {
            $clearanceRequest = ClearanceRequest::create([
                'user_account_id' => $account->id,
                'user_id' => $user->id,
                'status' => 'pending_payment',
                'fee_amount' => 3000,
            ]);
        }

        if ($clearanceRequest->status !== 'pending_payment') {
            return back()->with('info', 'This account is already '.str_replace('_', ' ', $clearanceRequest->status).'.');
        }

        $feeAmount = 3000;

        if (! $user->hasSufficientWalletBalance($feeAmount)) {
            return back()->with('error', 'Insufficient wallet balance. Please top up your wallet.');
        }

        DB::transaction(function () use ($user, $account, $clearanceRequest, $feeAmount) {
            $user->deductFromWallet($feeAmount);

            $clearanceRequest->update([
                'status' => 'pending_approval',
                'fee_paid_at' => now(),
                'payment_reference' => 'WALLET-'.strtoupper(uniqid()),
            ]);

            AccountTransaction::create([
                'primary_user_id' => $user->id,
                'user_account_id' => $account->id,
                'amount' => $feeAmount,
                'type' => 'penalty',
                'description' => 'Clearance fee - '.$account->account_name,
            ]);
        });

        return redirect()->route('user.accounts.clearances')
            ->with('success', 'Clearance fee paid from wallet. Remaining balance: ₦'.number_format($user->fresh()->wallet_balance, 2));
    }

    public function payBatchClearanceFee(Request $request)
    {
        $request->validate([
            'account_ids' => 'required|array|min:1',
            'account_ids.*' => 'required|exists:user_accounts,id',
        ]);

        $user = auth()->user();
        $accountIds = $request->account_ids;
        $feePerAccount = 3000;
        $accounts = [];

        foreach ($accountIds as $id) {
            $account = UserAccount::with('savingsPlanOption.plan')->find($id);

            if (! $account || $account->primary_user_id !== $user->id) {
                return back()->with('error', 'Invalid account selected.');
            }

            if (! $account->isMatured()) {
                return back()->with('error', "Account \"{$account->account_name}\" has not reached maturity.");
            }

            $clearanceRequest = $account->clearanceRequest;

            if ($clearanceRequest && $clearanceRequest->status !== 'pending_payment') {
                return back()->with('info', "Account \"{$account->account_name}\" is already {$clearanceRequest->status}.");
            }

            if (! $clearanceRequest) {
                ClearanceRequest::create([
                    'user_account_id' => $account->id,
                    'user_id' => $user->id,
                    'status' => 'pending_payment',
                    'fee_amount' => $feePerAccount,
                ]);
            }

            $accounts[] = $account;
        }

        $totalFee = count($accounts) * $feePerAccount;

        if (! $user->hasSufficientWalletBalance($totalFee)) {
            return back()->with('error', 'Insufficient wallet balance. You need ₦'.number_format($totalFee, 2).' but your wallet has ₦'.number_format($user->wallet_balance, 2).'. <a href="'.route('user.accounts.wallet').'" style="text-decoration:underline;font-weight:600;">Top up now</a>.');
        }

        DB::transaction(function () use ($user, $accounts, $totalFee, $feePerAccount) {
            $user->deductFromWallet($totalFee);

            foreach ($accounts as $account) {
                $clearanceRequest = $account->clearanceRequest;

                if (! $clearanceRequest) {
                    $clearanceRequest = ClearanceRequest::create([
                        'user_account_id' => $account->id,
                        'user_id' => $user->id,
                        'status' => 'pending_payment',
                        'fee_amount' => $feePerAccount,
                    ]);
                }

                $clearanceRequest->update([
                    'status' => 'pending_approval',
                    'fee_paid_at' => now(),
                    'payment_reference' => 'WALLET-'.strtoupper(uniqid()),
                ]);

                AccountTransaction::create([
                    'primary_user_id' => $user->id,
                    'user_account_id' => $account->id,
                    'amount' => $feePerAccount,
                    'type' => 'penalty',
                    'description' => 'Clearance fee - '.$account->account_name,
                ]);
            }
        });

        return redirect()->route('user.accounts.clearances')
            ->with('success', '₦'.number_format($totalFee, 2).' deducted from wallet for clearance of '.count($accounts).' account(s). Remaining balance: ₦'.number_format($user->wallet_balance, 2).'.');
    }
}

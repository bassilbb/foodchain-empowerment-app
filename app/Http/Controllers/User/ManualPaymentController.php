<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ManualPayment;
use Illuminate\Http\Request;

class ManualPaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $payments = $user->manualPayments()
            ->with(['bankAccount', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.manual-payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $type = $request->get('type', 'membership');
        $amount = $request->get('amount', 0);

        $bankAccounts = BankAccount::getAllActive();

        return view('user.manual-payments.create', compact('type', 'amount', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:membership,starter_pack,instant_food_pack,delivery,union_equity,loan_repayment,assistance_processing_fee,assistance_first_installment,assistance_weekly_repayment,support_plan_contribution,wallet_topup',
            'amount' => 'required|numeric|min:100',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'sender_name' => 'required|string|max:255',
            'sender_account_number' => 'nullable|string|max:20',
            'sender_bank_name' => 'nullable|string|max:100',
            'transfer_date' => 'required|date',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        $bankAccount = BankAccount::findOrFail($request->bank_account_id);

        $proofPath = $request->file('proof_image')->store('manual-payments/proofs', 'public');

        $manualPayment = ManualPayment::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'reference' => ManualPayment::generateReference(),
            'bank_account_id' => $bankAccount->id,
            'sender_name' => $request->sender_name,
            'sender_account_number' => $request->sender_account_number,
            'sender_bank_name' => $request->sender_bank_name,
            'transfer_date' => $request->transfer_date,
            'proof_image_path' => $proofPath,
            'status' => 'pending',
        ]);

        if ($request->type === 'wallet_topup') {
            return redirect()->route('user.accounts.wallet')
                ->with('success', 'Top-up submitted successfully. Your wallet will be credited once the payment is verified.');
        }

        return redirect()->route('manual-payments.show', $manualPayment->id)
            ->with('success', 'Payment submitted successfully. Please wait for admin verification.');
    }

    public function show(ManualPayment $manualPayment)
    {
        $user = auth()->user();

        if ($manualPayment->user_id !== $user->id) {
            abort(403);
        }

        return view('user.manual-payments.show', compact('manualPayment'));
    }

    public function bankAccounts()
    {
        $accounts = BankAccount::getAllActive();

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }
}

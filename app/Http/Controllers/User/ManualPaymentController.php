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
            'type' => 'required|in:membership,starter_pack,instant_food_pack,delivery,union_equity,loan_repayment,assistance_processing_fee,assistance_first_installment,assistance_weekly_repayment,support_plan_contribution,wallet_topup,marketplace_order',
            'amount' => 'required|numeric|min:1',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'marketplace_order_id' => 'nullable|required_if:type,marketplace_order|exists:marketplace_orders,id',
            'sender_name' => 'required|string|max:255',
            'sender_account_number' => 'nullable|string|max:20',
            'sender_bank_name' => 'nullable|string|max:100',
            'transfer_date' => 'required|date',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        if ($request->type === 'marketplace_order') {
            $order = \App\Models\Marketplace\Order::findOrFail($request->marketplace_order_id);
            abort_unless($order->user_id === $user->id, 403);
            if ($order->payment_status === 'paid') {
                return redirect()->route('marketplace.orders.show', $order)
                    ->with('info', 'This order has already been paid.');
            }

            $existingPending = ManualPayment::where('marketplace_order_id', $order->id)
                ->where('status', 'pending')
                ->first();

            if ($existingPending) {
                return redirect()->route('marketplace.orders.show', $order)
                    ->with('info', 'A bank transfer for this order is already awaiting verification.');
            }
        }

        $bankAccount = BankAccount::findOrFail($request->bank_account_id);

        $proofPath = $request->file('proof_image')->store('manual-payments/proofs', 'public');

        $manualPayment = ManualPayment::create([
            'user_id' => $user->id,
            'marketplace_order_id' => $request->type === 'marketplace_order' ? $request->marketplace_order_id : null,
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
            return redirect()->route('account.wallet.index')
                ->with('success', 'Top-up submitted successfully. Your wallet will be credited once the payment is verified.');
        }

        if ($request->type === 'marketplace_order') {
            $order->update(['payment_reference' => $manualPayment->reference]);

            return redirect()->route('marketplace.orders.show', $order)
                ->with('success', 'Bank transfer submitted. Your order will be confirmed once payment is verified.');
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

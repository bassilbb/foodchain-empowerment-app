<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\FoodDistribution;
use App\Models\ManualPayment;
use App\Models\Payment;
use App\Models\User;
use App\Models\Voucher;
use App\Services\NotificationService;
use App\Services\PaymentService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManualPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = ManualPayment::with(['user', 'bankAccount', 'verifier']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', "%{$request->search}%")
                    ->orWhere('sender_name', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($uq) use ($request) {
                        $uq->where('email', 'like', "%{$request->search}%")
                            ->orWhere('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%");
                    });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pending' => ManualPayment::pending()->count(),
            'verified' => ManualPayment::verified()->count(),
            'rejected' => ManualPayment::rejected()->count(),
            'total' => ManualPayment::count(),
        ];

        return view('admin.manual-payments.index', compact('payments', 'stats'));
    }

    public function show(ManualPayment $manualPayment)
    {
        $manualPayment->load(['user', 'bankAccount', 'verifier', 'payment']);

        return view('admin.manual-payments.show', compact('manualPayment'));
    }

    public function verifyForm(ManualPayment $manualPayment)
    {
        return redirect()->route('admin.manual-payments.show', $manualPayment)
            ->with('error', 'Please use the "Submit" button on the Actions form below to verify or reject this payment.');
    }

    public function verify(Request $request, ManualPayment $manualPayment)
    {
        $request->validate([
            'action' => 'required|in:verify,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        $adminId = Auth::guard('admin')->id();

        DB::transaction(function () use ($manualPayment, $request, $adminId) {
            if ($request->action === 'verify') {
                $this->processVerifiedPayment($manualPayment, $adminId, $request->notes);
            } else {
                $manualPayment->reject($adminId, $request->notes);
            }
        });

        $message = $request->action === 'verify'
            ? 'Payment verified successfully'
            : 'Payment rejected';

        return redirect()->route('admin.manual-payments.index')
            ->with('success', $message);
    }

    private function processVerifiedPayment(ManualPayment $manualPayment, int $adminId, ?string $notes = null): void
    {
        $manualPayment->verify($adminId, $notes);

        $payment = Payment::create([
            'user_id' => $manualPayment->user_id,
            'reference' => 'MANUAL-'.$manualPayment->reference,
            'type' => $manualPayment->type,
            'amount' => $manualPayment->amount,
            'status' => 'successful',
            'payment_method' => 'manual_transfer',
            'manual_payment_id' => $manualPayment->id,
            'paid_at' => $manualPayment->transfer_date,
            'meta' => [
                'bank_account' => $manualPayment->bankAccount->account_name ?? null,
                'sender_name' => $manualPayment->sender_name,
                'transfer_date' => $manualPayment->transfer_date,
            ],
        ]);

        $manualPayment->update(['payment_id' => $payment->id]);

        if ($manualPayment->type === 'wallet_topup') {
            $user = $payment->user;
            app(WalletService::class)->topUp($user, $payment->amount, 'wallet top-up: SUCCESSFUL-'.$manualPayment->reference);
            app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
        } else {
            app(PaymentService::class)->verifyPayment($payment->reference);
        }
    }

    public function bankAccounts()
    {
        $accounts = BankAccount::active()->orderByDesc('is_default')->get();

        return view('admin.bank-accounts.index', compact('accounts'));
    }

    public function storeBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:20|unique:bank_accounts,account_number',
            'account_name' => 'required|string|max:255',
            'account_type' => 'nullable|in:current,savings',
            'instructions' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            BankAccount::where('is_default', true)->update(['is_default' => false]);
        }

        BankAccount::create($request->all());

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Bank account added successfully');
    }

    public function updateBankAccount(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:20|unique:bank_accounts,account_number,'.$bankAccount->id,
            'account_name' => 'required|string|max:255',
            'account_type' => 'nullable|in:current,savings',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            BankAccount::where('is_default', true)->where('id', '!=', $bankAccount->id)
                ->update(['is_default' => false]);
        }

        $bankAccount->update($request->all());

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Bank account updated successfully');
    }

    public function deleteBankAccount(BankAccount $bankAccount)
    {
        if ($bankAccount->manualPayments()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete bank account with existing payments');
        }

        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Bank account deleted successfully');
    }

    public function vouchers(Request $request)
    {
        $query = Voucher::with(['user', 'distribution', 'manualPayment']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('voucher_number', 'like', "%{$request->search}%")
                    ->orWhere('collection_code', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($uq) use ($request) {
                        $uq->where('email', 'like', "%{$request->search}%")
                            ->orWhere('first_name', 'like', "%{$request->search}%");
                    });
            });
        }

        $vouchers = $query->orderBy('created_at', 'desc')->paginate(20);

        $users = User::where('is_active', true)->limit(50)->get();
        $distributions = FoodDistribution::where('status', 'approved')->limit(20)->get();

        return view('admin.vouchers.index', compact('vouchers', 'users', 'distributions'));
    }

    public function issueVoucher(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'food_distribution_id' => 'nullable|exists:food_distributions,id',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);

        $voucher = Voucher::create([
            'user_id' => $user->id,
            'food_distribution_id' => $request->food_distribution_id,
            'voucher_number' => Voucher::generateVoucherNumber(),
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.vouchers.show', $voucher->id)
            ->with('success', 'Voucher created successfully');
    }

    public function showVoucher(Voucher $voucher)
    {
        $voucher->load(['user', 'distribution', 'manualPayment', 'collector']);

        return view('admin.vouchers.show', compact('voucher'));
    }

    public function markAsIssued(Voucher $voucher)
    {
        $voucher->issue();

        if ($voucher->distribution) {
            $voucher->distribution->update([
                'collection_status' => 'ready',
                'collection_code' => $voucher->collection_code,
                'collection_date' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Voucher issued successfully');
    }

    public function collectVoucher(Request $request, Voucher $voucher)
    {
        $request->validate([
            'collection_code' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if (! $voucher->verifyCollectionCode($request->collection_code)) {
            return redirect()->back()->with('error', 'Invalid collection code');
        }

        $adminId = Auth::guard('admin')->id();
        $voucher->collect($adminId, $request->notes);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher collected successfully');
    }
}

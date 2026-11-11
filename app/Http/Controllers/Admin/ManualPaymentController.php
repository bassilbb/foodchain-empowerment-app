<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\BankAccount;
use App\Models\FoodDistribution;
use App\Models\ManualPayment;
use App\Models\Payment;
use App\Models\User;
use App\Models\Voucher;
use App\Services\AuditService;
use App\Services\Marketplace\MarketplaceNotificationService;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManualPaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorise('view_payments');

        $query = ManualPayment::with(['user', 'bankAccount', 'verifier', 'marketplaceOrder']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%'.$request->reference.'%');
        }

        if ($request->filled('order_number')) {
            $query->whereHas('marketplaceOrder', function ($q) use ($request) {
                $q->where('order_number', 'like', '%'.$request->order_number.'%');
            });
        }

        if ($request->filled('customer')) {
            $customer = $request->customer;
            $query->whereHas('user', function ($q) use ($customer) {
                $q->where('email', 'like', "%{$customer}%")
                    ->orWhere('first_name', 'like', "%{$customer}%")
                    ->orWhere('last_name', 'like', "%{$customer}%")
                    ->orWhere('phone', 'like', "%{$customer}%");
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('marketplaceOrder', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'pending' => ManualPayment::pending()->count(),
            'approved' => ManualPayment::verified()->count(),
            'verified' => ManualPayment::verified()->count(),
            'rejected' => ManualPayment::rejected()->count(),
            'total' => ManualPayment::count(),
            'pending_amount' => (float) ManualPayment::pending()->sum('amount'),
        ];

        return view('admin.manual-payments.index', compact('payments', 'stats'));
    }

    public function show(ManualPayment $manualPayment)
    {
        $this->authorise('view_payments');

        $manualPayment->load([
            'user',
            'bankAccount',
            'verifier',
            'payment',
            'marketplaceOrder.items.product',
        ]);

        $order = $manualPayment->marketplaceOrder;
        $expectedAmount = $order ? (float) $order->total : (float) $manualPayment->amount;
        $amountMatches = abs($expectedAmount - (float) $manualPayment->amount) < 0.01;

        $auditLogs = AdminAuditLog::with('adminUser')
            ->where('subject_type', ManualPayment::class)
            ->where('subject_id', $manualPayment->id)
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.manual-payments.show', compact(
            'manualPayment', 'order', 'expectedAmount', 'amountMatches', 'auditLogs'
        ));
    }

    public function verifyForm(ManualPayment $manualPayment)
    {
        return redirect()->route('admin.manual-payments.show', $manualPayment)
            ->with('error', 'Please use the "Submit" button on the Actions form below to verify or reject this payment.');
    }

    public function verify(Request $request, ManualPayment $manualPayment)
    {
        $this->authorise('verify_payments');

        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
            'notes' => 'nullable|string|max:1000',
            'confirmed_amount' => 'nullable|numeric|min:0',
            'amount_confirmed' => 'nullable|boolean',
        ]);

        $notes = isset($validated['notes']) ? trim((string) $validated['notes']) : null;

        if ($validated['action'] === 'reject' && ($notes === null || $notes === '')) {
            return redirect()->route('admin.manual-payments.show', $manualPayment)
                ->withInput()
                ->with('error', 'A rejection reason is required.');
        }

        $adminId = Auth::guard('admin')->id();
        $action = $validated['action'];
        $amountConfirmed = $request->boolean('amount_confirmed');
        $confirmedAmount = isset($validated['confirmed_amount'])
            ? (float) $validated['confirmed_amount']
            : null;

        try {
            DB::transaction(function () use ($manualPayment, $action, $notes, $adminId, $amountConfirmed, $confirmedAmount) {
                // Lock the row so two admins cannot verify/reject simultaneously.
                $locked = ManualPayment::whereKey($manualPayment->id)->lockForUpdate()->firstOrFail();

                if ($locked->isVerified() || $locked->isRejected()) {
                    throw new \DomainException('This payment has already been processed.');
                }

                if ($action === 'verify') {
                    if (! $amountConfirmed) {
                        throw new \DomainException('Confirm that the funds were received before approving this payment.');
                    }

                    $order = $locked->marketplaceOrder;
                    $expected = $order ? (float) $order->total : (float) $locked->amount;
                    $confirmed = $confirmedAmount ?? $expected;

                    if (abs($confirmed - $expected) >= 0.01) {
                        throw new \DomainException(
                            'Confirmed amount ('.number_format($confirmed, 2).') does not match the expected amount ('
                            .number_format($expected, 2).'). Do not approve until the exact amount has been received.'
                        );
                    }

                    $this->processVerifiedPayment($locked, $adminId, $notes);
                } else {
                    $locked->reject($adminId, $notes);
                }

                AuditService::log(
                    $action === 'verify' ? 'manual_payment.verified' : 'manual_payment.rejected',
                    $locked,
                    [
                        'status' => $locked->status,
                        'payment_status' => $locked->payment_status,
                        'amount' => (float) $locked->amount,
                        'notes' => $notes,
                    ],
                    $action === 'verify'
                        ? 'Manual payment '.$locked->reference.' approved.'
                        : 'Manual payment '.$locked->reference.' rejected.'
                );
            });
        } catch (\DomainException $e) {
            return redirect()->route('admin.manual-payments.show', $manualPayment)
                ->withInput()
                ->with('error', $e->getMessage());
        }

        $manualPayment->refresh();

        // Let the customer know the outcome of their notification.
        if ($manualPayment->type === 'marketplace_order') {
            $notifications = app(MarketplaceNotificationService::class);

            if ($manualPayment->isVerified()) {
                $notifications->bankTransferVerified($manualPayment);
            } elseif ($manualPayment->isRejected()) {
                $notifications->bankTransferRejected($manualPayment);
            }
        }

        $message = $action === 'verify'
            ? 'Payment verified successfully. The customer has been notified.'
            : 'Payment notification rejected. The order remains unpaid and the customer has been notified.';

        return redirect()->route('admin.manual-payments.show', $manualPayment)
            ->with('success', $message);
    }

    private function processVerifiedPayment(ManualPayment $manualPayment, int $adminId, ?string $notes = null): void
    {
        if ($manualPayment->payment_id) {
            throw new \DomainException('This payment has already been processed.');
        }

        $manualPayment->verify($adminId, $notes);

        $paymentReference = 'MANUAL-'.$manualPayment->reference;

        if (Payment::where('reference', $paymentReference)->exists()) {
            throw new \DomainException('A financial record already exists for this reference.');
        }

        $payment = Payment::create([
            'user_id' => $manualPayment->user_id,
            'reference' => $paymentReference,
            'type' => $manualPayment->type,
            'amount' => $manualPayment->amount,
            'status' => 'successful',
            'payment_method' => 'manual_transfer',
            'manual_payment_id' => $manualPayment->id,
            'paid_at' => $manualPayment->transfer_date ?? now(),
            'meta' => [
                'bank_account' => $manualPayment->bankAccount?->account_name,
                'sender_name' => $manualPayment->sender_name,
                'transfer_date' => $manualPayment->transfer_date,
                'verified_by' => $adminId,
            ],
        ]);

        $manualPayment->update(['payment_id' => $payment->id]);

        if ($manualPayment->type === 'wallet_topup') {
            $user = $payment->user;
            $user->addToWallet((float) $payment->amount);
            app(\App\Services\Marketplace\MarketplaceWalletService::class)
                ->logTransaction($user, null, (float) $payment->amount, 'credit', 'Wallet top-up: SUCCESSFUL-'.$manualPayment->reference);
            app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
        } elseif ($manualPayment->type === 'marketplace_order') {
            $order = $manualPayment->marketplaceOrder;
            if ($order) {
                app(\App\Services\Marketplace\OrderService::class)
                    ->confirmExternalPayment($order, 'bank_transfer', $payment->reference);
            }
        } else {
            app(PaymentService::class)->verifyPayment($payment->reference);
        }
    }

    private function authorise(string $permission): void
    {
        $admin = auth('admin')->user();

        abort_unless($admin && $admin->hasPermission($permission), 403, 'You do not have permission to manage payments.');
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

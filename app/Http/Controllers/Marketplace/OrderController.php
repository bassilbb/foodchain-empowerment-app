<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ManualPayment;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\MarketplaceNotificationService;
use App\Services\Marketplace\OrderService;
use App\Services\Payments\PaymentMethodSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService,
        private PaymentMethodSettings $paymentMethods,
        private MarketplaceNotificationService $notificationService,
    ) {
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Order::with('items')->where('user_id', $user->id);

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(12)->withQueryString();

        return view('marketplace.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'currentStatus' => $request->input('status'),
            'title' => 'My Orders',
        ]);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items.product.images', 'items.review', 'statusHistories', 'user']);

        $bankAccounts = BankAccount::getAllActive();
        $bankTransferPayment = ManualPayment::where('marketplace_order_id', $order->id)
            ->latest()
            ->first();

        $latestRejected = ManualPayment::where('marketplace_order_id', $order->id)
            ->where('status', ManualPayment::STATUS_REJECTED)
            ->latest()
            ->first();

        return view('marketplace.orders.show', [
            'order' => $order,
            'title' => 'Order '.$order->order_number,
            'bankAccounts' => $bankAccounts,
            'bankTransferPayment' => $bankTransferPayment,
            'latestRejectedPayment' => $latestRejected,
            'bankTransferEnabled' => $this->paymentMethods->isAvailable(PaymentMethodSettings::BANK_TRANSFER),
            'showBankTransferModal' => request()->boolean('bank_transfer') || (request()->query('payment') === 'bank_transfer'),
        ]);
    }

    public function submitBankTransfer(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->payment_status === 'paid') {
            return redirect()->route('marketplace.orders.show', $order)
                ->with('info', 'This order has already been paid.');
        }

        if ($order->payment_method !== PaymentMethodSettings::BANK_TRANSFER) {
            return redirect()->route('marketplace.orders.show', $order)
                ->with('error', 'This order was not placed with bank transfer.');
        }

        if (! $this->paymentMethods->isAvailable(PaymentMethodSettings::BANK_TRANSFER)) {
            return redirect()->route('marketplace.orders.show', $order)
                ->with('error', 'Bank transfer is not available right now. Please contact support.');
        }

        $validated = $request->validate([
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'sender_name' => 'nullable|string|max:255',
            'sender_account_number' => 'nullable|string|max:20',
            'sender_bank_name' => 'nullable|string|max:100',
            'transfer_date' => 'nullable|date|before_or_equal:today',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $bankAccount = ! empty($validated['bank_account_id'])
            ? BankAccount::active()->find($validated['bank_account_id'])
            : BankAccount::getActiveForPayment();

        if (! $bankAccount) {
            return back()->with('error', 'No bank account is configured for transfers. Please contact support.');
        }

        $proofPath = 'manual-payments/proofs/not-provided';
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('manual-payments/proofs', 'public');
        }

        try {
            $manualPayment = DB::transaction(function () use ($order, $bankAccount, $validated, $proofPath) {
                // Lock the order so two concurrent clicks cannot both pass the
                // duplicate check below.
                $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

                $existingPending = ManualPayment::where('marketplace_order_id', $lockedOrder->id)
                    ->where('status', ManualPayment::STATUS_PENDING)
                    ->lockForUpdate()
                    ->first();

                if ($existingPending) {
                    return null;
                }

                $manualPayment = ManualPayment::create([
                    'user_id' => auth()->id(),
                    'marketplace_order_id' => $lockedOrder->id,
                    'type' => 'marketplace_order',
                    'amount' => $lockedOrder->total,
                    'currency' => 'NGN',
                    'reference' => ManualPayment::generateReference(),
                    'bank_account_id' => $bankAccount->id,
                    'sender_name' => $validated['sender_name'] ?? (auth()->user()->name ?? 'Customer'),
                    'sender_account_number' => $validated['sender_account_number'] ?? null,
                    'sender_bank_name' => $validated['sender_bank_name'] ?? null,
                    'transfer_date' => $validated['transfer_date'] ?? now(),
                    'proof_image_path' => $proofPath,
                    'status' => ManualPayment::STATUS_PENDING,
                    'payment_status' => 'pending',
                    'verification_status' => 'pending',
                ]);

                $lockedOrder->update(['payment_reference' => $manualPayment->reference]);

                return $manualPayment;
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->route('marketplace.orders.show', $order)
                ->with('info', 'A payment notification for this order is already awaiting verification.');
        }

        if (! $manualPayment) {
            return redirect()->route('marketplace.orders.show', $order)
                ->with('info', 'A bank transfer for this order is already awaiting verification.');
        }

        $this->notificationService->bankTransferNotificationReceived($manualPayment);

        return redirect()->route('marketplace.orders.show', $order)
            ->with('success', 'Payment notification submitted successfully. Your order is awaiting payment verification by our administration team.')
            ->with('payment_notification_reference', $manualPayment->reference);
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $result = $this->orderService->cancelOrder(auth()->user(), $order, $request->input('reason'));

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function confirmDelivery(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $result = $this->orderService->markDelivered(auth()->user(), $order);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function reorder(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $result = $this->orderService->reorder(auth()->user(), $order);

        if ($result['success']) {
            return redirect()->route('marketplace.cart.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function requestReturn(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $request->validate(['reason' => 'required|string|min:10|max:2000']);

        $result = $this->orderService->requestReturn(auth()->user(), $order, $request->input('reason'));

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function invoice(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items.product.images', 'user']);

        return view('marketplace.orders.invoice', [
            'order' => $order,
            'title' => 'Invoice '.$order->order_number,
        ]);
    }

    public function review(Request $request, Order $order, OrderItem $item)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($item->order_id === $order->id, 403);
        abort_unless(in_array($order->status, ['delivered']), 403);

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string|max:120',
            'comment' => 'nullable|string|max:2000',
        ]);

        // Shared with the account dashboard so review rules live in one place.
        $result = $this->orderService->reviewItem(
            auth()->user(),
            $order,
            $item,
            $request->only('rating', 'title', 'comment')
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}

<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ManualPayment;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService,
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

        return view('marketplace.orders.show', [
            'order' => $order,
            'title' => 'Order '.$order->order_number,
            'bankAccounts' => $bankAccounts,
            'bankTransferPayment' => $bankTransferPayment,
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

        $existingPending = ManualPayment::where('marketplace_order_id', $order->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return redirect()->route('marketplace.orders.show', $order)
                ->with('info', 'A bank transfer for this order is already awaiting verification.');
        }

        $validated = $request->validate([
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'sender_name' => 'nullable|string|max:255',
            'sender_account_number' => 'nullable|string|max:20',
            'sender_bank_name' => 'nullable|string|max:100',
            'transfer_date' => 'nullable|date',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $bankAccount = null;
        if (! empty($validated['bank_account_id'])) {
            $bankAccount = BankAccount::findOrFail($validated['bank_account_id']);
        } else {
            $bankAccount = BankAccount::getActiveForPayment();
        }

        if (! $bankAccount) {
            return back()->with('error', 'No bank account is configured for transfers. Please contact support.');
        }

        $proofPath = 'manual-payments/proofs/not-provided';
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('manual-payments/proofs', 'public');
        }

        $manualPayment = ManualPayment::create([
            'user_id' => auth()->id(),
            'marketplace_order_id' => $order->id,
            'type' => 'marketplace_order',
            'amount' => $order->total,
            'reference' => ManualPayment::generateReference(),
            'bank_account_id' => $bankAccount->id,
            'sender_name' => $validated['sender_name'] ?? (auth()->user()->name ?? 'Customer'),
            'sender_account_number' => $validated['sender_account_number'] ?? null,
            'sender_bank_name' => $validated['sender_bank_name'] ?? null,
            'transfer_date' => $validated['transfer_date'] ?? now(),
            'proof_image_path' => $proofPath,
            'status' => 'pending',
        ]);

        $order->update(['payment_reference' => $manualPayment->reference]);

        return redirect()->route('marketplace.orders.show', $order)
            ->with('success', 'Bank transfer submitted. Your order will be confirmed once payment is verified.');
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

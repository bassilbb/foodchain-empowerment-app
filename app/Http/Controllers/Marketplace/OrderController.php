<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Review;
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

        return view('marketplace.orders.show', [
            'order' => $order,
            'title' => 'Order '.$order->order_number,
        ]);
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

        $review = Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'order_item_id' => $item->id,
            ],
            [
                'product_id' => $item->product_id,
                'order_id' => $order->id,
                'rating' => $request->integer('rating'),
                'title' => $request->input('title'),
                'comment' => $request->input('comment'),
                'is_approved' => false,
            ]
        );

        $item->update(['review_id' => $review->id]);

        return back()->with('success', 'Thank you! Your review has been submitted for approval.');
    }
}

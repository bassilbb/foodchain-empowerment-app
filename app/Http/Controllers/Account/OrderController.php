<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Services\Marketplace\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * The customer-facing status groups shown in the sidebar / filter bar.
     * Each maps to one or more real `marketplace_orders.status` values, so the
     * UI never invents a stage the application does not actually track.
     */
    public const STATUS_GROUPS = [
        'pending' => ['label' => 'Pending', 'statuses' => ['pending', 'paid']],
        'processing' => ['label' => 'Processing', 'statuses' => ['confirmed', 'processing', 'packed']],
        'shipped' => ['label' => 'Shipped', 'statuses' => ['shipped']],
        'delivered' => ['label' => 'Delivered', 'statuses' => ['delivered']],
        'cancelled' => ['label' => 'Cancelled', 'statuses' => ['cancelled', 'refunded']],
    ];

    public function __construct(
        private OrderService $orderService,
    ) {}

    /**
     * Order list with search, status-group and date filters.
     *
     * Every query is scoped to the authenticated user (Section 23 — a customer
     * must never be able to enumerate another customer's orders).
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Order::query()
            ->where('user_id', $user->id)
            ->with(['items:id,order_id,product_id,product_name,quantity,total_price,status']);

        // Status group filter
        $statusGroup = $request->input('status');
        if ($statusGroup && isset(self::STATUS_GROUPS[$statusGroup])) {
            $query->whereIn('status', self::STATUS_GROUPS[$statusGroup]['statuses']);
        }

        // Free-text search on order number and purchased product names
        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where('order_number', 'like', $like)
                    ->orWhereHas('items', fn($i) => $i->where('product_name', 'like', $like));
            });
        }

        // Date range filter
        $from = $request->input('from');
        $to = $request->input('to');
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        // Counts per group for the filter chips (one grouped query, user-scoped).
        $counts = Order::query()
            ->where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $groupCounts = collect(self::STATUS_GROUPS)->map(
            fn($g) => (int) collect($g['statuses'])->sum(fn($s) => (int) ($counts[$s] ?? 0))
        )->all();

        $wishlistCount = \App\Models\Marketplace\SavedProduct::where('user_id', $user->id)->count();
        $unreadNotifications = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();

        return view('account.orders.index', [
            'orders' => $orders,
            'groups' => self::STATUS_GROUPS,
            'groupCounts' => $groupCounts,
            'totalCount' => (int) $counts->sum(),
            'currentStatus' => $statusGroup,
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'wishlistCount' => $wishlistCount,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    /**
     * Order detail. Ownership is enforced with a scoped firstOrFail() so a direct
     * URL guess (/account/orders/123) belonging to someone else returns 404
     * rather than leaking the record.
     */
    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load([
            'items.product.images',
            'items.vendor',
            'items.review',
            'statusHistories',
        ]);

        $user = auth()->user();

        return view('account.orders.show', [
            'order' => $order,
            'timeline' => $this->buildTimeline($order),
            'wishlistCount' => \App\Models\Marketplace\SavedProduct::where('user_id', $user->id)->count(),
            'unreadNotifications' => \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count(),
        ]);
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $result = $this->orderService->cancelOrder(auth()->user(), $order, $request->input('reason'));

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function confirmDelivery(Order $order)
    {
        $this->authorizeOrder($order);

        $result = $this->orderService->markDelivered(auth()->user(), $order);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function reorder(Order $order)
    {
        $this->authorizeOrder($order);

        $result = $this->orderService->reorder(auth()->user(), $order);

        if ($result['success']) {
            return redirect()->route('marketplace.cart.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function requestReturn(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'reason' => 'required|string|min:10|max:2000',
        ]);

        $result = $this->orderService->requestReturn(auth()->user(), $order, $request->input('reason'));

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function invoice(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load(['items.product.images', 'user']);

        // Reuse the existing print-optimised invoice template rather than
        // maintaining a second one. It reads the stored `unit_price`, so the
        // historical state-based price actually charged is preserved (Section 27).
        return view('marketplace.orders.invoice', [
            'order' => $order,
        ]);
    }

    public function review(Request $request, Order $order, OrderItem $item)
    {
        $this->authorizeOrder($order);

        abort_unless($item->order_id === $order->id, 404);

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string|max:120',
            'comment' => 'nullable|string|max:2000',
        ]);

        $result = $this->orderService->reviewItem(
            auth()->user(),
            $order,
            $item,
            $request->only('rating', 'title', 'comment')
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Ownership guard for every single-order action.
     *
     * Returning 404 (not 403) avoids confirming that an order id exists, which
     * is the correct response for an IDOR probe.
     */
    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->user_id === auth()->id(), 404);
    }

    /**
     * Build the delivery timeline from real recorded order history.
     *
     * Only stages the order has actually passed through are marked done; the
     * remaining stages are shown as upcoming. Cancelled/refunded orders short
     * circuit to a terminal state rather than showing a misleading progression.
     */
    private function buildTimeline(Order $order): array
    {
        $history = $order->statusHistories->keyBy('status');

        if (in_array($order->status, ['cancelled', 'refunded'], true)) {
            return [
                [
                    'key' => 'placed',
                    'label' => 'Order Placed',
                    'done' => true,
                    'at' => $history->get('pending')?->created_at,
                    'note' => $history->get('pending')?->note,
                ],
                [
                    'key' => $order->status,
                    'label' => $order->status === 'refunded' ? 'Refunded' : 'Cancelled',
                    'done' => true,
                    'at' => $history->get($order->status)?->created_at ?? $order->cancelled_at ?? $order->refunded_at,
                    'note' => $history->get($order->status)?->note,
                    'terminal' => true,
                ],
            ];
        }

        $stages = [
            'pending' => 'Order Placed',
            'paid' => 'Payment Confirmed',
            'confirmed' => 'Processing',
            'processing' => 'Processing',
            'packed' => 'Packed',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
        ];

        $order->loadMissing('statusHistories');
        $reached = $order->statusHistories->pluck('status')->unique()->all();
        $reached[] = $order->status;

        $timeline = [];
        $seen = [];

        foreach ($stages as $key => $label) {
            if (isset($seen[$label])) {
                continue;
            }
            $seen[$label] = true;

            $entry = $history->get($key);

            $timeline[] = [
                'key' => $key,
                'label' => $label,
                'done' => in_array($key, $reached, true),
                'at' => $entry?->created_at,
                'note' => $entry?->note,
            ];
        }

        return $timeline;
    }
}

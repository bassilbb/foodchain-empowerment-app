<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\SavedProduct;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Customer marketplace dashboard overview.
     *
     * Aggregation strategy (Section 25 — dashboard performance):
     *  - order counters are produced by ONE grouped query, not one COUNT per status
     *  - money totals come from a single aggregate select
     *  - wishlist / notification counts are two cheap indexed counts
     *  - recent orders eager-load their items to avoid N+1 in the view
     */
    public function index()
    {
        $user = auth()->user();

        // Vendor application status, surfaced as a banner on the dashboard.
        $vendor = $user->marketplaceVendor;

        // Single grouped pass over the customer's orders -> status => count.
        $statusCounts = Order::query()
            ->where('user_id', $user->id)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Single aggregate pass for lifetime spend (excludes cancelled/refunded).
        $lifetimeSpend = (float) Order::query()
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');

        // Keys are only present when at least one order has that status, so each
        // aggregate is null-coalesced individually (a trailing ?? cannot guard a
        // sum expression).
        $count = fn (string ...$statuses): int => (int) collect($statuses)
            ->sum(fn ($s) => (int) ($statusCounts[$s] ?? 0));

        $stats = [
            'total' => (int) $statusCounts->sum(),
            'pending' => $count('pending', 'paid'),
            'processing' => $count('confirmed', 'processing', 'packed'),
            'shipped' => $count('shipped'),
            'delivered' => $count('delivered'),
            'cancelled' => $count('cancelled'),
            'refunded' => $count('refunded'),
            'wishlist' => SavedProduct::where('user_id', $user->id)->count(),
            'unread_notifications' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            'wallet_balance' => (float) $user->wallet_balance,
            'lifetime_spend' => $lifetimeSpend,
        ];

        $recentOrders = Order::query()
            ->where('user_id', $user->id)
            ->with(['items:id,order_id,product_name,quantity,total_price'])
            ->latest()
            ->limit(5)
            ->get();

        // Data shared with the sidebar badges.
        $wishlistCount = $stats['wishlist'];
        $unreadNotifications = $stats['unread_notifications'];

        return view('account.dashboard', compact('stats', 'recentOrders', 'user', 'wishlistCount', 'unreadNotifications', 'vendor'));
    }
}

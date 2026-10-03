<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Notification types grouped for the filter chips.
     *
     * Matching is prefix-based because notification `type` values are written by
     * the marketplace notification service (order.*, payment.*, etc.).
     */
    public const FILTERS = [
        'orders' => ['label' => 'Orders', 'prefixes' => ['order', 'delivery']],
        'payments' => ['label' => 'Payments', 'prefixes' => ['payment', 'wallet', 'refund']],
        'account' => ['label' => 'Account', 'prefixes' => ['account', 'security', 'tier', 'membership']],
        'promotions' => ['label' => 'Promotions', 'prefixes' => ['promo', 'campaign', 'deal']],
    ];

    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->input('type');

        $query = Notification::where('user_id', $user->id);

        if ($filter && isset(self::FILTERS[$filter])) {
            $prefixes = self::FILTERS[$filter]['prefixes'];
            $query->where(function ($q) use ($prefixes) {
                foreach ($prefixes as $prefix) {
                    $q->orWhere('type', 'like', $prefix . '%');
                }
            });
        }

        if ($request->input('unread') === '1') {
            $query->where('is_read', false);
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();

        // One grouped pass for per-filter counts rather than one query per chip.
        $counts = Notification::where('user_id', $user->id)
            ->select('type', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $filterCounts = collect(self::FILTERS)->map(function ($meta) use ($counts) {
            return (int) $counts->filter(
                fn($total, $type) => collect($meta['prefixes'])->contains(
                    fn($p) => str_starts_with((string) $type, $p)
                )
            )->sum();
        })->all();

        return view('account.notifications.index', [
            'notifications' => $notifications,
            'filters' => self::FILTERS,
            'filterCounts' => $filterCounts,
            'currentFilter' => $filter,
            'unreadOnly' => $request->input('unread') === '1',
            'totalCount' => (int) $counts->sum(),
            'unreadNotifications' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            'wishlistCount' => $user->savedProducts()->count(),
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 404);

        // Use the model helper so both `is_read` and `read_at` are set.
        $notification->markAsRead();

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        // Single UPDATE for all unread rows — avoids N individual queries.
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 404);

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    public function destroyAll()
    {
        auth()->user()->notifications()->delete();

        return back()->with('success', 'All notifications cleared.');
    }
}

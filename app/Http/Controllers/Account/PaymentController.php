<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Marketplace payment history.
     *
     * Reads the recorded `payments` rows for this user. Payment status is never
     * inferred from the order status — it comes from the payment records
     * themselves (Section 27).
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Payment::where('user_id', $user->id);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        // One grouped pass for the status chips.
        $counts = Payment::where('user_id', $user->id)
            ->select('status', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'total_paid' => (float) Payment::where('user_id', $user->id)->where('status', 'successful')->sum('amount'),
            'total_pending' => (float) Payment::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'total_failed' => (float) Payment::where('user_id', $user->id)->where('status', 'failed')->sum('amount'),
            'count' => (int) $counts->sum(),
        ];

        return view('account.payments.index', [
            'payments' => $payments,
            'counts' => $counts,
            'summary' => $summary,
            'currentStatus' => $status,
            'from' => $from,
            'to' => $to,
            'wishlistCount' => $user->savedProducts()->count(),
            'unreadNotifications' => $user->notifications()->where('is_read', false)->count(),
        ]);
    }
}

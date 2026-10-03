<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\WalletTransaction;
use App\Services\Marketplace\MarketplaceWalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        private MarketplaceWalletService $walletService,
    ) {}

    public function index()
    {
        $user = auth()->user();

        $balance = $this->walletService->getBalance($user);

        // Aggregate activity in one grouped pass rather than three SUM queries.
        $totals = WalletTransaction::where('user_id', $user->id)
            ->select('type', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('account.wallet.index', [
            'balance' => $balance,
            'totalCredit' => (float) ($totals['credit'] ?? 0),
            'totalDebit' => (float) ($totals['debit'] ?? 0),
            'recentTransactions' => WalletTransaction::with('order')
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
            'wishlistCount' => $user->savedProducts()->count(),
            'unreadNotifications' => $user->notifications()->where('is_read', false)->count(),
        ]);
    }

    public function transactions(Request $request)
    {
        $user = auth()->user();

        $transactions = WalletTransaction::with('order')
            ->where('user_id', $user->id)
            ->when($request->input('type'), fn($q, $type) => $q->where('type', $type))
            ->when($request->input('from'), fn($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($request->input('to'), fn($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totals = WalletTransaction::where('user_id', $user->id)
            ->select('type', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('account.wallet.transactions', [
            'transactions' => $transactions,
            'balance' => $this->walletService->getBalance($user),
            'totalCredit' => (float) ($totals['credit'] ?? 0),
            'totalDebit' => (float) ($totals['debit'] ?? 0),
            'currentType' => $request->input('type'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'wishlistCount' => $user->savedProducts()->count(),
            'unreadNotifications' => $user->notifications()->where('is_read', false)->count(),
        ]);
    }
}

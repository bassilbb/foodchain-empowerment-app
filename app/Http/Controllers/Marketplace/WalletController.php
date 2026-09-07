<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\MarketplaceWalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        private MarketplaceWalletService $walletService,
    ) {
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        return view('marketplace.wallet', [
            'balance' => $this->walletService->getBalance($user),
            'transactions' => $this->walletService->getTransactions($user),
            'title' => 'Marketplace Wallet',
        ]);
    }

    public function transactions(Request $request)
    {
        $user = auth()->user();

        $transactions = \App\Models\Marketplace\WalletTransaction::with('order')
            ->where('user_id', $user->id)
            ->when($request->input('type'), fn ($q, $type) => $q->where('type', $type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('marketplace.wallet', [
            'balance' => $this->walletService->getBalance($user),
            'transactions' => $transactions,
            'paginated' => true,
            'currentType' => $request->input('type'),
            'title' => 'Wallet Transactions',
        ]);
    }

    public function redeemPoints(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:10',
        ]);

        try {
            $value = app(\App\Services\Marketplace\LoyaltyService::class)->redeem(auth()->user(), $request->integer('points'));

            return back()->with('success', 'Redeemed '.$request->integer('points').' points for ₦'.number_format($value, 2).' wallet credit.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

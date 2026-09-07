<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\LoyaltyService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function __construct(
        private LoyaltyService $loyaltyService,
    ) {
    }

    public function index()
    {
        $user = auth()->user();

        return view('marketplace.loyalty', [
            'balance' => $this->loyaltyService->getBalance($user),
            'history' => $this->loyaltyService->getHistory($user),
            'redeemableValue' => $this->loyaltyService->getBalance($user),
            'title' => 'Loyalty Points',
        ]);
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:10|max:'.auth()->user()->loyalty_points_balance,
        ]);

        try {
            $value = $this->loyaltyService->redeem(auth()->user(), $request->integer('points'));

            return back()->with('success', 'Redeemed '.$request->integer('points').' points for ₦'.number_format($value, 2).' wallet credit.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

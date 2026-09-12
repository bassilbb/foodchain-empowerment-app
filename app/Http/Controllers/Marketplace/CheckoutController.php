<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\CouponService;
use App\Services\Marketplace\MarketplaceDeliveryService;
use App\Services\Marketplace\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService,
        private MarketplaceDeliveryService $deliveryService,
        private CouponService $couponService,
    ) {
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $data = $this->orderService->buildCheckoutData($user, $request->only([
            'coupon_code', 'delivery_state', 'delivery_lga', 'delivery_type', 'pickup_location',
        ]));

        if (! $data['success']) {
            return redirect()->route('marketplace.cart.index')->with('error', $data['message']);
        }

        return view('marketplace.checkout', [
            'data' => $data,
            'states' => $this->deliveryService->getStates(),
            'zones' => $this->deliveryService->getZones(),
            'pickupLocations' => \App\Models\PickupLocation::active()->get(),
            'deliveryTypes' => [
                'home_delivery' => 'Home Delivery',
                'express' => 'Express Delivery',
                'same_day' => 'Same-Day Delivery',
                'pickup' => 'Pickup Station',
                'community_delivery' => 'Community Delivery',
            ],
            'paymentMethods' => \App\Models\Marketplace\Order::PAYMENT_METHODS,
            'title' => 'Checkout',
        ]);
    }

    public function estimate(Request $request)
    {
        $request->validate([
            'state' => 'required|string',
            'lga' => 'nullable|string',
            'delivery_type' => 'required|string',
        ]);

        $user = auth()->user();
        $subtotal = $this->cartService->getSubtotal($user);
        $weight = $this->cartService->getCart($user)->sum(fn ($item) => (float) $item->product->weight * $item->quantity);

        $result = $this->deliveryService->calculate([
            'state' => $request->input('state'),
            'lga' => $request->input('lga'),
            'delivery_type' => $request->input('delivery_type'),
            'subtotal' => $subtotal,
            'weight' => $weight,
            'user' => $user,
        ]);

        return response()->json([
            'delivery_fee' => (float) $result['delivery_fee'],
            'estimated_days' => $result['estimated_days'],
            'estimated_delivery_date' => $result['estimated_delivery_date'] ?? null,
            'total' => round($subtotal + (float) $result['delivery_fee'], 2),
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $subtotal = $this->cartService->getSubtotal(auth()->user());
        $result = $this->couponService->validate($request->input('coupon_code'), $subtotal, auth()->user());

        if (! $result['valid']) {
            return response()->json(['success' => false, 'message' => $result['message']]);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'discount' => (float) $result['discount'],
            'code' => $request->input('coupon_code'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:wallet,wallet_cash,wallet_paystack,cash,paystack,flutterwave,bank_transfer',
            'wallet_amount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'delivery_type' => 'required|in:home_delivery,express,same_day,pickup,community_delivery',
            'delivery_state' => 'required|string',
            'delivery_lga' => 'nullable|string',
            'delivery_address' => 'nullable|string|max:500',
            'delivery_city' => 'nullable|string|max:120',
            'pickup_location' => 'nullable|string|max:255',
            'delivery_notes' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:1000',
        ]);

        $result = $this->orderService->placeOrder(auth()->user(), $request->all());

        if (! $result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        if (! empty($result['authorization_url'])) {
            return redirect()->away($result['authorization_url']);
        }

        return redirect($result['redirect'])->with('success', $result['message']);
    }
}

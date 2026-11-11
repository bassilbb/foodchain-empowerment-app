<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\CouponService;
use App\Services\Marketplace\MarketplaceDeliveryService;
use App\Services\Marketplace\OrderService;
use App\Services\Payments\PaymentMethodSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService,
        private MarketplaceDeliveryService $deliveryService,
        private CouponService $couponService,
        private PaymentMethodSettings $paymentMethods,
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

        // Only methods the administrator enabled (and that have the config
        // they need) are offered. Nothing is hard-coded here.
        $paymentMethods = $this->paymentMethods->enabledLabels();
        $defaultPaymentMethod = array_key_first($paymentMethods);

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
            'paymentMethods' => $paymentMethods,
            'defaultPaymentMethod' => $defaultPaymentMethod,
            'bankTransferDetails' => $this->paymentMethods->bankTransferDetails(),
            'bankTransferEnabled' => $this->paymentMethods->isAvailable(PaymentMethodSettings::BANK_TRANSFER),
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
        $serviceChargeRate = ($subtotal >= 500000) ? 0.03 : 0.05;
        $serviceCharge = round($subtotal * $serviceChargeRate, 2);
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
            'service_charge' => $serviceCharge,
            'service_charge_rate' => $serviceChargeRate,
            'estimated_days' => $result['estimated_days'],
            'estimated_delivery_date' => $result['estimated_delivery_date'] ?? null,
            'total' => round($subtotal + (float) $result['delivery_fee'] + $serviceCharge, 2),
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
        $enabled = array_keys($this->paymentMethods->enabledLabels());

        if (empty($enabled)) {
            return back()->withInput()->with(
                'error',
                'No payment method is currently available. Please contact support.'
            );
        }

        $request->validate([
            // Server-side enforcement: a disabled method cannot be posted even
            // by crafting a request or replaying an old checkout session.
            'payment_method' => ['required', 'string', Rule::in($enabled)],
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
        ], [
            'payment_method.in' => 'The selected payment method is not available. Please choose another method.',
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

<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private CartService $cartService,
        private CouponService $couponService,
        private MarketplaceDeliveryService $deliveryService,
        private MarketplaceWalletService $walletService,
        private LoyaltyService $loyaltyService,
        private PaymentService $paymentService,
        private MarketplaceNotificationService $notificationService,
    ) {
    }

    public function buildCheckoutData(User $user, array $data): array
    {
        $cartItems = $this->cartService->getCart($user);
        $subtotal = $this->cartService->getSubtotal($user);

        if ($cartItems->isEmpty()) {
            return ['success' => false, 'message' => 'Your cart is empty.'];
        }

        $couponDiscount = 0.0;
        $couponId = null;
        $couponCode = null;

        if (! empty($data['coupon_code'])) {
            $couponResult = $this->couponService->applyToOrder($data['coupon_code'], $subtotal, $user);
            if ($couponResult['valid']) {
                $couponDiscount = $couponResult['coupon_discount'];
                $couponId = $couponResult['coupon_id'];
                $couponCode = $couponResult['coupon_code'];
            }
        }

        $weight = $cartItems->sum(fn ($item) => (float) $item->product->weight * $item->quantity);

        $delivery = $this->deliveryService->calculate([
            'state' => $data['delivery_state'] ?? 'Lagos',
            'lga' => $data['delivery_lga'] ?? null,
            'delivery_type' => $data['delivery_type'] ?? 'home_delivery',
            'subtotal' => $subtotal - $couponDiscount,
            'weight' => $weight,
            'user' => $user,
        ]);

        $shippingFee = (float) $delivery['delivery_fee'];
        $total = max(0, $subtotal - $couponDiscount + $shippingFee);

        return [
            'success' => true,
            'cart_items' => $cartItems,
            'subtotal' => round($subtotal, 2),
            'coupon_id' => $couponId,
            'coupon_code' => $couponCode,
            'coupon_discount' => round($couponDiscount, 2),
            'shipping_fee' => $shippingFee,
            'estimated_delivery_date' => $delivery['estimated_delivery_date'] ?? null,
            'delivery_type' => $data['delivery_type'] ?? 'home_delivery',
            'total' => round($total, 2),
            'wallet_balance' => (float) $user->wallet_balance,
            'points_to_earn' => $this->loyaltyService->pointsForAmount($total),
        ];
    }

    public function placeOrder(User $user, array $data): array
    {
        $checkout = $this->buildCheckoutData($user, $data);

        if (! $checkout['success']) {
            return $checkout;
        }

        $paymentMethod = $data['payment_method'] ?? 'wallet';
        $total = $checkout['total'];
        $walletBalance = (float) $user->wallet_balance;

        if (in_array($paymentMethod, ['wallet', 'wallet_cash', 'wallet_paystack'])) {
            $walletAmount = $paymentMethod === 'wallet'
                ? $total
                : (float) ($data['wallet_amount'] ?? 0);

            if ($paymentMethod === 'wallet' && $walletBalance < $total) {
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Please top up your wallet or use another payment method.',
                ];
            }

            if ($paymentMethod !== 'wallet' && $walletAmount > $walletBalance) {
                return ['success' => false, 'message' => 'Wallet amount exceeds your available balance.'];
            }
        }

        try {
            $order = DB::transaction(function () use ($user, $data, $checkout, $paymentMethod) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => Order::generateOrderNumber(),
                    'status' => 'pending',
                    'subtotal' => $checkout['subtotal'],
                    'discount_amount' => 0,
                    'shipping_fee' => $checkout['shipping_fee'],
                    'total' => $checkout['total'],
                    'coupon_id' => $checkout['coupon_id'],
                    'coupon_code' => $checkout['coupon_code'],
                    'coupon_discount' => $checkout['coupon_discount'],
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentMethod === 'cash' ? 'unpaid' : 'pending',
                    'delivery_type' => $checkout['delivery_type'],
                    'delivery_address' => $data['delivery_address'] ?? null,
                    'delivery_city' => $data['delivery_city'] ?? null,
                    'delivery_state' => $data['delivery_state'] ?? null,
                    'delivery_lga' => $data['delivery_lga'] ?? null,
                    'estimated_delivery_date' => $checkout['estimated_delivery_date'] ?? null,
                    'pickup_location' => $data['pickup_location'] ?? null,
                    'delivery_notes' => $data['delivery_notes'] ?? null,
                    'bulk_order_id' => $data['bulk_order_id'] ?? null,
                    'note' => $data['note'] ?? null,
                    'meta' => ['customer_note' => $data['note'] ?? null, 'pricing_state' => mp_state()],
                ]);

                foreach ($checkout['cart_items'] as $item) {
                    $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                    $unitPrice = app(MarketplacePricingService::class)->priceFor($product);
                    $lineTotal = $unitPrice * $item->quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'vendor_id' => $product->vendor_id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'unit_price' => $unitPrice,
                        'quantity' => $item->quantity,
                        'discount_amount' => 0,
                        'total_price' => $lineTotal,
                        'status' => 'pending',
                    ]);

                    $product->decrementStock($item->quantity);

                    $product->inventoryMovements()->create([
                        'vendor_id' => $product->vendor_id,
                        'type' => 'sale',
                        'quantity' => -$item->quantity,
                        'reason' => 'Order '.$order->order_number,
                        'changed_by_type' => User::class,
                        'changed_by_id' => $user->id,
                    ]);
                }

                $order->updateStatus('pending', 'Order placed.');

                if ($checkout['coupon_id']) {
                    $this->couponService->markUsed(\App\Models\Marketplace\Coupon::find($checkout['coupon_id']));
                }

                $this->cartService->clear($user);

                return $order;
            });

            if ($paymentMethod === 'wallet') {
                $this->walletService->payWithWallet($user, $order, $order->total);
                $order->refresh();
                $this->loyaltyService->awardForOrder($order);
                $this->notificationService->orderPlaced($order);
                $this->notificationService->paymentSuccessful($order);

                return [
                    'success' => true,
                    'message' => 'Order placed successfully and paid from wallet.',
                    'order' => $order,
                    'redirect' => route('marketplace.orders.show', $order->id),
                ];
            }

            if ($paymentMethod === 'wallet_cash') {
                $walletAmount = (float) ($data['wallet_amount'] ?? 0);
                $this->walletService->payWithWallet($user, $order, $walletAmount);
                $order->refresh();

                $cashAmount = $order->total - $walletAmount;
                $order->update([
                    'cash_amount' => round($cashAmount, 2),
                    'payment_status' => 'partially_paid',
                ]);
                $order->updateStatus('confirmed', 'Partial wallet payment. Balance payable on delivery.');

                $this->loyaltyService->awardForOrder($order);
                $this->notificationService->orderPlaced($order);
                $this->notificationService->paymentSuccessful($order);

                return [
                    'success' => true,
                    'message' => 'Order placed. Wallet deducted, balance payable on delivery.',
                    'order' => $order,
                    'redirect' => route('marketplace.orders.show', $order->id),
                ];
            }

            if ($paymentMethod === 'cash') {
                $order->update(['payment_status' => 'unpaid']);
                $this->notificationService->orderPlaced($order);

                return [
                    'success' => true,
                    'message' => 'Order placed. You will pay cash on delivery.',
                    'order' => $order,
                    'redirect' => route('marketplace.orders.show', $order->id),
                ];
            }

            if ($paymentMethod === 'paystack') {
                $payment = $this->paymentService->initializePayment(
                    $user,
                    'marketplace_order',
                    $order->total,
                    ['marketplace_order_id' => $order->id, 'order_number' => $order->order_number]
                );

                if ($payment['status'] === 'success') {
                    $order->update([
                        'payment_reference' => $payment['reference'],
                        'payment_id' => $payment['payment_id'],
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Redirecting to payment gateway...',
                        'order' => $order,
                        'authorization_url' => $payment['authorization_url'],
                    ];
                }

                return [
                    'success' => false,
                    'message' => $payment['message'] ?? 'Payment initialization failed.',
                    'order' => $order,
                ];
            }

            if (in_array($paymentMethod, ['flutterwave', 'bank_transfer'])) {
                $order->update(['payment_status' => 'pending']);

                if ($paymentMethod === 'bank_transfer') {
                    $this->notificationService->orderPlaced($order);

                    return [
                        'success' => true,
                        'message' => 'Order placed. Please complete payment via bank transfer and your order will be confirmed.',
                        'order' => $order,
                        'redirect' => route('marketplace.orders.show', $order->id),
                    ];
                }

                $flutterwave = $this->initiateFlutterwave($user, $order);

                if ($flutterwave['success']) {
                    $order->update(['payment_reference' => $flutterwave['reference']]);

                    return [
                        'success' => true,
                        'message' => 'Redirecting to Flutterwave...',
                        'order' => $order,
                        'authorization_url' => $flutterwave['authorization_url'],
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Order placed. Payment will be verified once received.',
                    'order' => $order,
                    'redirect' => route('marketplace.orders.show', $order->id),
                ];
            }

            return [
                'success' => false,
                'message' => 'Unsupported payment method.',
                'order' => $order,
            ];
        } catch (\Throwable $e) {
            report($e);

            return ['success' => false, 'message' => 'Unable to place order: '.$e->getMessage()];
        }
    }

    private function initiateFlutterwave(User $user, Order $order): array
    {
        $secretKey = setting('flutterwave_secret_key');

        if (! $secretKey) {
            return ['success' => false, 'message' => 'Flutterwave not configured.'];
        }

        try {
            $reference = 'FLW-'.strtoupper(uniqid());
            $response = \Illuminate\Support\Facades\Http::withToken($secretKey)
                ->post('https://api.flutterwave.com/v3/payments', [
                    'tx_ref' => $reference,
                    'amount' => $order->total,
                    'currency' => 'NGN',
                    'redirect_url' => route('payment.callback'),
                    'customer' => [
                        'email' => $user->email,
                        'name' => $user->full_name,
                        'phonenumber' => $user->phone,
                    ],
                    'customizations' => [
                        'title' => 'FoodChain Marketplace Order '.$order->order_number,
                    ],
                    'meta' => [
                        'order_id' => $order->id,
                        'payment_type' => 'marketplace_order',
                        'gateway' => 'flutterwave',
                    ],
                ]);

            if ($response->successful() && ($response->json('status') === 'success' || $response->json('status') === 'successful')) {
                return [
                    'success' => true,
                    'reference' => $reference,
                    'authorization_url' => $response->json('data.link'),
                ];
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return ['success' => false, 'message' => 'Flutterwave payment initialization failed.'];
    }

    public function getOrder(User $user, int $orderId): ?Order
    {
        return Order::with(['items.product.images', 'user', 'statusHistories'])
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();
    }

    public function getOrders(User $user): \Illuminate\Support\Collection
    {
        return Order::with('items')
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    public function cancelOrder(User $user, Order $order, ?string $reason = null): array
    {
        if ($order->user_id !== $user->id) {
            return ['success' => false, 'message' => 'You cannot cancel this order.'];
        }

        if (! $order->isCancellable()) {
            return ['success' => false, 'message' => 'This order can no longer be cancelled.'];
        }

        DB::transaction(function () use ($order, $reason) {
            foreach ($order->items as $item) {
                $item->product?->incrementStock($item->quantity);
                $item->product?->inventoryMovements()->create([
                    'vendor_id' => $item->vendor_id,
                    'type' => 'stock_in',
                    'quantity' => $item->quantity,
                    'reason' => 'Order cancelled: '.$order->order_number,
                ]);
            }

            if ($order->wallet_amount > 0 && in_array($order->payment_status, ['paid', 'partially_paid'])) {
                app(MarketplaceWalletService::class)->refund($order->user, $order, $order->wallet_amount, 'Refund for cancelled order '.$order->order_number);
            }

            if ($order->loyalty_points_earned > 0) {
                $points = $order->loyalty_points_earned;
                $order->user->update(['loyalty_points_balance' => max(0, $order->user->loyalty_points_balance - $points)]);
                \App\Models\Marketplace\LoyaltyPoint::create([
                    'user_id' => $order->user_id,
                    'points' => -$points,
                    'type' => 'adjust',
                    'description' => 'Points reversed for cancelled order '.$order->order_number,
                    'order_id' => $order->id,
                    'balance_after' => $order->user->loyalty_points_balance,
                ]);
                $order->update(['loyalty_points_earned' => 0]);
            }

            $order->updateStatus('cancelled', $reason ?? 'Cancelled by customer.');
            $this->notificationService->orderCancelled($order);
        });

        return ['success' => true, 'message' => 'Order cancelled successfully.'];
    }

    public function markDelivered(User $user, Order $order): array
    {
        if ($order->user_id !== $user->id || $order->status !== 'shipped') {
            return ['success' => false, 'message' => 'Unable to confirm delivery.'];
        }

        $order->updateStatus('delivered', 'Delivered - confirmed by customer.');
        $this->notificationService->orderDelivered($order);

        return ['success' => true, 'message' => 'Delivery confirmed. Enjoy your food!'];
    }

    public function reorder(User $user, Order $order): array
    {
        if ($order->user_id !== $user->id) {
            return ['success' => false, 'message' => 'Invalid order.'];
        }

        $added = 0;

        foreach ($order->items as $item) {
            $product = Product::active()->find($item->product_id);

            if (! $product || ! $product->isInStock()) {
                continue;
            }

            $result = $this->cartService->add($user, $product->id, $item->quantity);
            if ($result['success']) {
                $added++;
            }
        }

        if ($added === 0) {
            return ['success' => false, 'message' => 'None of the items could be reordered (out of stock).'];
        }

        return ['success' => true, 'message' => $added.' item(s) added to your cart.'];
    }

    public function requestReturn(User $user, Order $order, string $reason): array
    {
        if ($order->user_id !== $user->id || ! $order->isReturnable()) {
            return ['success' => false, 'message' => 'This order cannot be returned.'];
        }

        $order->update(['meta' => array_merge($order->meta ?? [], ['return_requested' => true, 'return_reason' => $reason, 'return_requested_at' => now()->toDateTimeString()])]);

        $this->notificationService->returnRequested($order);

        return ['success' => true, 'message' => 'Return request submitted. We will review and contact you.'];
    }

    public function confirmExternalPayment(Order $order, string $gateway, ?string $reference = null): array
    {
        if ($order->payment_status === 'paid') {
            return ['success' => true, 'message' => 'Order already paid.'];
        }

        if ($order->payment_method === 'paystack' && $gateway !== 'paystack') {
            return ['success' => false, 'message' => 'Payment gateway mismatch.'];
        }

        DB::transaction(function () use ($order, $gateway, $reference) {
            $order->update([
                'payment_status' => 'paid',
                'payment_reference' => $reference ?: $order->payment_reference,
                'payment_gateway' => $gateway,
                'paid_at' => now(),
            ]);

            $order->updateStatus('confirmed', 'Payment received via '.$gateway.'.');

            $this->loyaltyService->awardForOrder($order);
        });

        $this->notificationService->paymentSuccessful($order);
        $this->notificationService->orderStatusChanged($order, 'confirmed');

        return ['success' => true, 'message' => 'Order payment confirmed.'];
    }

    public function downloadInvoice(Order $order)
    {
        return view('marketplace.orders.invoice', compact('order'));
    }
}

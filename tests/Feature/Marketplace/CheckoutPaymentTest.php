<?php

namespace Tests\Feature\Marketplace;

use App\Models\Marketplace\CartItem;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductStatePrice;
use App\Models\Marketplace\Vendor;
use App\Models\NigerianState;
use App\Models\Payment;
use App\Models\User;
use App\Services\Marketplace\MarketplacePricingService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use ReflectionClass;
use Tests\TestCase;

class CheckoutPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;

    private Vendor $vendor;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetPricingCache();

        $this->buyer = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
            'wallet_balance' => 0,
        ]);

        $vendorUser = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $this->vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'name' => 'Checkout Store',
            'slug' => 'checkout-store-'.uniqid(),
            'business_name' => 'Checkout Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $category = Category::create([
            'name' => 'Grains '.uniqid(),
            'slug' => 'grains-'.uniqid(),
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'vendor_id' => $this->vendor->id,
            'category_id' => $category->id,
            'name' => 'Premium Rice 5kg',
            'slug' => 'premium-rice-'.uniqid(),
            'sku' => Product::generateSku(),
            'price' => 12000,
            'stock_quantity' => 20,
            'low_stock_threshold' => 3,
            'is_active' => true,
        ]);
    }

    private function resetPricingCache(): void
    {
        $class = new ReflectionClass(MarketplacePricingService::class);
        $class->setStaticPropertyValue('mapCache', []);
        $class->setStaticPropertyValue('nameCache', []);
        $class->setStaticPropertyValue('currentCache', null);
    }

    private function lagosState(): NigerianState
    {
        return NigerianState::create([
            'name' => 'Lagos',
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    private function addToCart(int $quantity = 2, string $state = 'Lagos'): void
    {
        $this->actingAs($this->buyer)
            ->withSession(['marketplace_state' => $state])
            ->postJson(route('marketplace.cart.add'), ['product_id' => $this->product->id, 'quantity' => $quantity])
            ->assertOk();
    }

    private function checkoutPayload(string $paymentMethod, array $overrides = []): array
    {
        return array_merge([
            'payment_method' => $paymentMethod,
            'delivery_type' => 'home_delivery',
            'delivery_state' => 'Lagos',
        ], $overrides);
    }

    private function placeOrder(string $paymentMethod, array $overrides = [])
    {
        return $this->actingAs($this->buyer)
            ->withSession(['marketplace_state' => 'Lagos'])
            ->post(route('marketplace.checkout.store'), $this->checkoutPayload($paymentMethod, $overrides));
    }

    // --------------------------------------------------- state pricing

    public function test_cart_line_totals_and_subtotal_use_state_pricing(): void
    {
        $this->withLagosPrice();

        $this->addToCart(2);

        $response = $this->actingAs($this->buyer)
            ->withSession(['marketplace_state' => 'Lagos'])
            ->get(route('marketplace.cart.index'))
            ->assertOk();

        $response->assertViewHas('subtotal', 23000.00);

        $item = $this->buyer->cartItems()->first();
        $this->assertNotNull($item);
        $this->assertEquals(23000.00, $item->line_total);
    }

    private function withLagosPrice(int $price = 11500): void
    {
        $state = $this->lagosState();

        ProductStatePrice::create([
            'product_id' => $this->product->id,
            'state_id' => $state->id,
            'price' => $price,
            'is_active' => true,
        ]);
    }

    // ------------------------------------------------- delivery types

    /**
     * @dataProvider premiumDeliveryTypes
     */
    public function test_premium_delivery_types_can_be_ordered(string $deliveryType): void
    {
        // express and same_day are priced and offered by the checkout form, so the
        // orders column has to accept them or the whole insert is rejected.
        $this->buyer->update(['wallet_balance' => 50000]);
        $this->addToCart(1);

        $this->placeOrder('cash', ['delivery_type' => $deliveryType]);

        $order = Order::first();

        $this->assertNotNull($order, "Order could not be placed with {$deliveryType} delivery.");
        $this->assertSame($deliveryType, $order->delivery_type);
    }

    public static function premiumDeliveryTypes(): array
    {
        return [
            'express' => ['express'],
            'same day' => ['same_day'],
        ];
    }

    public function test_the_orders_table_accepts_every_offered_delivery_type(): void
    {
        $offered = ['home_delivery', 'express', 'same_day', 'pickup', 'community_delivery'];

        foreach ($offered as $deliveryType) {
            $accepted = DB::table('marketplace_orders')->insertGetId([
                'user_id' => $this->buyer->id,
                'order_number' => 'FC-ENUM-'.strtoupper($deliveryType),
                'status' => 'pending',
                'total' => 100,
                'payment_method' => 'cash',
                'delivery_type' => $deliveryType,
            ]);

            $this->assertSame($deliveryType, DB::table('marketplace_orders')->where('id', $accepted)->value('delivery_type'));
        }
    }

    // ------------------------------------------------------- plain wallet

    public function test_wallet_order_pays_in_full_and_snapshots_the_state_price(): void
    {
        $this->withLagosPrice();

        $this->buyer->update(['wallet_balance' => 50000]);
        $this->addToCart(2);

        $this->placeOrder('wallet')->assertRedirect(route('marketplace.orders.show', Order::firstOrFail()->id));

        $order = Order::first();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame(23000.00, (float) $order->subtotal);
        $this->assertSame(1500.00, (float) $order->shipping_fee);
        $this->assertSame(25650.00, (float) $order->total);
        $this->assertSame(25650.00, (float) $order->wallet_amount);

        $this->assertDatabaseHas('marketplace_order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'vendor_id' => $this->vendor->id,
            'unit_price' => 11500.00,
            'quantity' => 2,
            'total_price' => 23000.00,
        ]);

        $this->assertEquals(24350.00, (float) $this->buyer->fresh()->wallet_balance);
        $this->assertSame(18, $this->product->fresh()->stock_quantity);
        $this->assertSame(0, CartItem::where('user_id', $this->buyer->id)->count());
    }

    public function test_wallet_order_is_rejected_when_balance_is_insufficient(): void
    {
        $this->buyer->update(['wallet_balance' => 5000]);
        $this->addToCart(1);

        $this->from(route('marketplace.checkout.index'))
            ->placeOrder('wallet')
            ->assertRedirect(route('marketplace.checkout.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('marketplace_orders', 0);
    }

    // ------------------------------------------------ wallet + paystack

    public function test_wallet_paystack_when_wallet_covers_total_never_touches_the_gateway(): void
    {
        $this->withLagosPrice();

        $this->buyer->update(['wallet_balance' => 50000]);
        $this->addToCart(2);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('initializePayment')->never();
        });

        $this->placeOrder('wallet_paystack', ['wallet_amount' => 25650])
            ->assertRedirect(route('marketplace.orders.show', Order::firstOrFail()->id));

        $order = Order::first();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame(25650.00, (float) $order->wallet_amount);
    }

    public function test_wallet_paystack_with_a_remainder_debits_wallet_and_redirects_to_the_gateway(): void
    {
        $this->withLagosPrice();

        $this->buyer->update(['wallet_balance' => 10000]);
        $this->addToCart(2);

        $payment = Payment::create([
            'user_id' => $this->buyer->id,
            'reference' => 'PAY-REMAINDER-1',
            'type' => 'marketplace_order',
            'amount' => 15650,
            'status' => 'pending',
            'payment_method' => 'paystack',
        ]);

        $this->mock(PaymentService::class, function ($mock) use ($payment) {
            $mock->shouldReceive('initializePayment')
                ->once()
                ->withArgs(function ($user, $type, $amount, $meta) {
                    return $type === 'marketplace_order'
                        && abs($amount - 15650.0) < 0.001
                        && ($meta['order_number'] ?? null) !== null;
                })
                ->andReturn([
                    'status' => 'success',
                    'payment_id' => $payment->id,
                    'authorization_url' => 'https://checkout.paystack.test/abc',
                    'reference' => 'PSK-REMAINDER-1',
                ]);
        });

        $this->placeOrder('wallet_paystack', ['wallet_amount' => 10000])
            ->assertRedirect('https://checkout.paystack.test/abc');

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertSame('partially_paid', $order->payment_status);
        $this->assertSame(10000.00, (float) $order->wallet_amount);
        $this->assertSame('PSK-REMAINDER-1', $order->payment_reference);
        $this->assertSame($payment->id, $order->payment_id);
        $this->assertEquals(15650.00, (float) ($order->meta['remaining_paystack_amount'] ?? 0));

        $this->assertEquals(0.0, (float) $this->buyer->fresh()->wallet_balance);
        $this->assertSame(18, $this->product->fresh()->stock_quantity);
    }

    public function test_wallet_paystack_gateway_failure_refunds_the_wallet_and_cancels_the_order(): void
    {
        $this->buyer->update(['wallet_balance' => 10000]);
        $this->addToCart(2);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('initializePayment')
                ->once()
                ->andReturn(['status' => 'error', 'message' => 'Paystack is unavailable.']);
        });

        $this->from(route('marketplace.checkout.index'))
            ->placeOrder('wallet_paystack', ['wallet_amount' => 10000])
            ->assertRedirect(route('marketplace.checkout.index'))
            ->assertSessionHas('error');

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertSame('cancelled', $order->status);
        // The order remains, but as a cancelled record with an audit trail and a
        // full refund — never a committed "ghost" order with no payment.
        $this->assertEquals(10000.0, (float) $this->buyer->fresh()->wallet_balance);
        $this->assertSame(20, $this->product->fresh()->stock_quantity);
        // Restoring stock must also unwind the sales counter, otherwise every
        // cancelled or refunded order permanently inflates "best sellers".
        $this->assertSame(0, (int) $this->product->fresh()->sold_count);

        $this->assertSame(2, DB::table('marketplace_wallet_transactions')->count());
        $this->assertSame('credit', DB::table('marketplace_wallet_transactions')->latest('id')->value('type'));
    }

    public function test_wallet_paystack_wallet_amount_cannot_exceed_the_balance(): void
    {
        $this->buyer->update(['wallet_balance' => 5000]);
        $this->addToCart(2);

        $this->from(route('marketplace.checkout.index'))
            ->placeOrder('wallet_paystack', ['wallet_amount' => 9000])
            ->assertRedirect(route('marketplace.checkout.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('marketplace_orders', 0);
    }

    // --------------------------------------------------- flutterwave

    public function test_flutterwave_order_redirects_to_flutterwave_and_confirms_on_callback(): void
    {
        config(['services.flutterwave.secret_key' => 'flw-test-key']);

        Http::fake([
            'api.flutterwave.com/v3/payments' => Http::response([
                'status' => 'success',
                'message' => 'Charge initiated',
                'data' => [
                    'id' => 1234,
                    'tx_ref' => 'FLW-REF-1',
                    'flw_ref' => 'FLWFREF1',
                    'amount' => 24500,
                    'link' => 'https://checkout.flutterwave.test/abc',
                ],
            ], 200),
            'api.flutterwave.com/v3/transactions/*/verify' => Http::response([
                'status' => 'success',
                'data' => [
                    'id' => 1234,
                    'tx_ref' => 'FLW-REF-1',
                    'status' => 'successful',
                    'amount' => 24500,
                    'currency' => 'NGN',
                ],
            ], 200),
        ]);

        $this->withLagosPrice();
        $this->addToCart(2);

        $this->placeOrder('flutterwave')
            ->assertRedirect('https://checkout.flutterwave.test/abc');

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertSame('flutterwave', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertNotNull($order->payment_reference);
        $this->assertNotNull($order->payment_id);

        $this->get(route('payment.callback').'?tx_ref='.$order->payment_reference.'&status=successful&transaction_id=1234')
            ->assertRedirect(route('marketplace.orders.show', $order->id));

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('flutterwave', $order->fresh()->payment_gateway);
    }

    public function test_flutterwave_initialize_posts_correct_payload(): void
    {
        config(['services.flutterwave.secret_key' => 'flw-test-key']);

        Http::fake([
            'api.flutterwave.com/v3/payments' => Http::response([
                'status' => 'success',
                'data' => ['link' => 'https://checkout.flutterwave.test/link', 'tx_ref' => 'FLW-REF-2'],
            ], 200),
        ]);

        $result = app(PaymentService::class)->initializePayment($this->buyer, 'membership', 5000, ['renewal' => true], 'flutterwave');

        $this->assertSame('success', $result['status']);
        $this->assertSame('https://checkout.flutterwave.test/link', $result['authorization_url']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.flutterwave.com/v3/payments'
                && $request->hasHeader('Authorization', 'Bearer flw-test-key')
                && $request['currency'] === 'NGN'
                && $request['amount'] == 5000;
        });

        $payment = Payment::where('payment_method', 'flutterwave')->first();
        $this->assertNotNull($payment);
        $this->assertSame('pending', $payment->status);
        $this->assertSame('membership', $payment->type);
    }

    // --------------------------------------------------------- mono

    public function test_mono_order_redirects_to_mono_and_confirms_on_callback(): void
    {
        config(['services.mono.secret_key' => 'mono-test-key']);

        Http::fake([
            'api.withmono.com/v2/payments/initiate' => Http::response([
                'status' => 'successful',
                'data' => [
                    'reference' => 'MONO-REF-1',
                    'mono_url' => 'https://checkout.withmono.test/abc',
                ],
            ], 200),
            'api.withmono.com/v2/payments/verify/*' => Http::response([
                'status' => 'successful',
                'data' => [
                    'reference' => 'MONO-REF-1',
                    'status' => 'successful',
                    'amount' => 2450000,
                ],
            ], 200),
        ]);

        $this->withLagosPrice();
        $this->addToCart(2);

        $this->placeOrder('mono')
            ->assertRedirect('https://checkout.withmono.test/abc');

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertSame('mono', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertNotNull($order->payment_reference);
        $this->assertNotNull($order->payment_id);

        $this->get(route('payment.callback').'?reference='.$order->payment_reference.'&status=successful')
            ->assertRedirect(route('marketplace.orders.show', $order->id));

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('mono', $order->fresh()->payment_gateway);
    }

    public function test_mono_initialize_sends_kobo_amounts_and_secret_header(): void
    {
        config(['services.mono.secret_key' => 'mono-test-key']);

        Http::fake([
            'api.withmono.com/v2/payments/initiate' => Http::response([
                'status' => 'successful',
                'data' => [
                    'reference' => 'MONO-REF-2',
                    'mono_url' => 'https://checkout.withmono.test/link',
                ],
            ], 200),
        ]);

        $result = app(PaymentService::class)->initializePayment($this->buyer, 'membership', 5000, [], 'mono');

        $this->assertSame('success', $result['status']);
        $this->assertSame('https://checkout.withmono.test/link', $result['authorization_url']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.withmono.com/v2/payments/initiate'
                && $request->hasHeader('mono-sec-key', 'mono-test-key')
                && $request['amount'] == 500000
                && $request['type'] === 'onetime-debit';
        });

        $payment = Payment::where('payment_method', 'mono')->first();
        $this->assertNotNull($payment);
        $this->assertSame('pending', $payment->status);
    }

    public function test_bank_transfer_checkout_applies_service_charge_and_redirects_to_order(): void
    {
        $this->withLagosPrice();
        $this->addToCart(2);

        $bankAccount = \App\Models\BankAccount::create([
            'bank_name' => 'Test Bank',
            'account_number' => '0123456789',
            'account_name' => 'FoodChain',
            'is_active' => true,
            'is_default' => true,
        ]);

        $response = $this->placeOrder('bank_transfer');

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        // subtotal 23000 -> 5% service charge = 1150; + delivery + (no coupon)
        $this->assertSame(23000.0, (float) $order->subtotal);
        $this->assertSame(0.05, (float) $order->service_charge_rate);
        $this->assertSame(1150.0, (float) $order->service_charge);
        $this->assertEqualsWithDelta((float) $order->subtotal + (float) $order->service_charge + (float) $order->shipping_fee, (float) $order->total, 0.01);

        $response->assertRedirect(route('marketplace.orders.show', $order->id).'?bank_transfer=1');
    }

    public function test_submit_bank_transfer_creates_pending_manual_payment(): void
    {
        $this->withLagosPrice();
        $this->addToCart(1);

        $bankAccount = \App\Models\BankAccount::create([
            'bank_name' => 'Test Bank',
            'account_number' => '0123456789',
            'account_name' => 'FoodChain',
            'is_active' => true,
            'is_default' => true,
        ]);

        $this->placeOrder('bank_transfer');

        $order = Order::first();

        $this->actingAs($this->buyer)
            ->post(route('marketplace.orders.bank-transfer', $order), [
                'sender_name' => 'Buyer Name',
            ])
            ->assertRedirect(route('marketplace.orders.show', $order));

        $this->assertDatabaseHas('manual_payments', [
            'marketplace_order_id' => $order->id,
            'status' => 'pending',
            'type' => 'marketplace_order',
        ]);
    }
}

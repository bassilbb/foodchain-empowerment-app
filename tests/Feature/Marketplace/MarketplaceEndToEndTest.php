<?php

namespace Tests\Feature\Marketplace;

use App\Models\Marketplace\Brand;
use App\Models\Marketplace\CartItem;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Coupon;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductStatePrice;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use App\Models\NigerianState;
use App\Models\User;
use App\Services\Marketplace\MarketplacePricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Tests\TestCase;

/**
 * One continuous journey through the storefront, exercised over HTTP the way a
 * real shopper does: browse -> cart -> checkout -> pay -> vendor fulfils ->
 * customer confirms -> vendor gets paid.
 *
 * Unit-level services are already covered elsewhere; the point of this file is
 * to prove the routes, middleware, controllers, services and views line up in
 * sequence, including the ownership and authorisation guards between actors.
 */
class MarketplaceEndToEndTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;

    private User $vendorUser;

    private User $rivalVendorUser;

    private Vendor $vendor;

    private Vendor $rivalVendor;

    private Product $product;

    private Product $rivalProduct;

    private NigerianState $lagos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetPricingCache();

        $this->lagos = NigerianState::create([
            'name' => 'Lagos',
            'code' => 'LA',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->buyer = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
            'wallet_balance' => 500000,
            'loyalty_points_balance' => 0,
            'state' => 'Lagos',
        ]);

        $this->vendorUser = $this->createVendorUser('E2E Farms');
        $this->rivalVendorUser = $this->createVendorUser('Rival Goods');

        $this->vendor = Vendor::create([
            'user_id' => $this->vendorUser->id,
            'name' => 'E2E Farms',
            'slug' => 'e2e-farms',
            'business_name' => 'E2E Farms Ltd',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $this->rivalVendor = Vendor::create([
            'user_id' => $this->rivalVendorUser->id,
            'name' => 'Rival Goods',
            'slug' => 'rival-goods',
            'business_name' => 'Rival Goods Ltd',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $category = Category::create([
            'name' => 'Grains',
            'slug' => 'grains',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Harvest Co',
            'slug' => 'harvest-co',
            'is_active' => true,
        ]);

        $this->product = $this->createProduct($this->vendor, $category, $brand, 'Premium Rice 5kg', 'premium-rice-5kg', 12000, 20);
        $this->rivalProduct = $this->createProduct($this->rivalVendor, $category, $brand, 'Rival Beans 2kg', 'rival-beans-2kg', 8000, 50);

        // Lagos-specific pricing: every assertion below is based on 11,500.
        ProductStatePrice::create([
            'product_id' => $this->product->id,
            'state_id' => $this->lagos->id,
            'price' => 11500,
            'is_active' => true,
        ]);
    }

    private function createVendorUser(string $name): User
    {
        return User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
            'first_name' => $name,
        ]);
    }

    private function createProduct(Vendor $vendor, Category $category, ?Brand $brand, string $name, string $slug, int $price, int $stock): Product
    {
        return Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'name' => $name,
            'slug' => $slug,
            'sku' => Product::generateSku(),
            'price' => $price,
            'stock_quantity' => $stock,
            'low_stock_threshold' => 3,
            'weight' => 5,
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
    }

    private function resetPricingCache(): void
    {
        $class = new ReflectionClass(MarketplacePricingService::class);
        $class->setStaticPropertyValue('mapCache', []);
        $class->setStaticPropertyValue('nameCache', []);
        $class->setStaticPropertyValue('currentCache', null);
    }

    private function asBuyer(array $session = ['marketplace_state' => 'Lagos'])
    {
        return $this->actingAs($this->buyer)->withSession($session);
    }

    /** Full purchase of one product, returning the created order. */
    private function buyWalletOrder(int $quantity = 2): Order
    {
        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => $quantity,
        ])->assertOk();

        $this->asBuyer()->post(route('marketplace.checkout.store'), [
            'payment_method' => 'wallet',
            'delivery_type' => 'home_delivery',
            'delivery_state' => 'Lagos',
            'delivery_address' => '12 Test Street',
            'delivery_city' => 'Lagos',
        ])->assertRedirect();

        return Order::latest('id')->firstOrFail();
    }

    // ------------------------------------------------- public browsing

    public function test_a_guest_can_browse_the_whole_storefront_without_an_account(): void
    {
        $this->get(route('marketplace.home'))->assertOk();
        $this->get(route('marketplace.shop'))->assertOk()->assertSee('Premium Rice 5kg');
        $this->get(route('marketplace.category', ['category' => 'grains']))->assertOk();
        $this->get(route('marketplace.brand', ['brand' => 'harvest-co']))->assertOk();
        $this->get(route('marketplace.vendors'))->assertOk()->assertSee('E2E Farms Ltd');
        $this->get(route('marketplace.vendor', ['vendor' => 'e2e-farms']))->assertOk();
        $this->get(route('marketplace.product', ['product' => 'premium-rice-5kg']))->assertOk();

        $this->get(route('marketplace.search', ['q' => 'Rice']))->assertOk()->assertSee('Premium Rice 5kg');
        $this->getJson(route('marketplace.autocomplete', ['q' => 'Ric']))->assertOk()->assertJsonStructure(['products', 'categories', 'brands']);

        // No live flash sale is seeded, so the page must degrade gracefully.
        $this->get(route('marketplace.flash-sale'))
            ->assertRedirect(route('marketplace.shop'))
            ->assertSessionHas('info');
    }

    public function test_unapproved_and_archived_products_are_not_reachable(): void
    {
        $pending = $this->createProduct($this->vendor, Category::first(), null, 'Pending Item', 'pending-item', 1000, 5);
        $pending->update(['approval_status' => 'pending']);

        $this->get(route('marketplace.product', ['product' => 'pending-item']))->assertNotFound();
        $this->get(route('marketplace.shop'))->assertOk()->assertDontSee('Pending Item');

        $this->product->update(['is_active' => false]);
        $this->get(route('marketplace.product', ['product' => 'premium-rice-5kg']))->assertNotFound();
    }

    public function test_guests_are_sent_to_login_instead_of_reaching_purchase_pages(): void
    {
        foreach ([
            route('marketplace.cart.index'),
            route('marketplace.checkout.index'),
            route('marketplace.orders.index'),
            route('marketplace.wallet.index'),
            route('marketplace.bulk.index'),
        ] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    // ------------------------------------------------------- state + cart

    public function test_choosing_a_state_is_persisted_and_reprices_the_catalog(): void
    {
        $this->get(route('marketplace.set-state', ['state' => 'lagos']))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->get(route('marketplace.product', ['product' => 'premium-rice-5kg']))
            ->assertOk()
            ->assertSee('11,500');
    }

    public function test_an_unknown_state_falls_back_instead_of_breaking_the_shop(): void
    {
        $this->get(route('marketplace.set-state', ['state' => 'Wakanda']))
            ->assertRedirect()
            ->assertSessionHas('info');

        $this->get(route('marketplace.shop'))->assertOk();
    }

    public function test_the_cart_supports_the_full_lifecycle(): void
    {
        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ])->assertOk()->assertJson(['success' => true]);

        $this->asBuyer()->getJson(route('marketplace.cart.count'))
            ->assertOk()
            ->assertJson(['count' => 2]);

        // Quantity updates clamp to available stock rather than overselling.
        $this->asBuyer()->postJson(route('marketplace.cart.update'), [
            'product_id' => $this->product->id,
            'quantity' => 50,
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertSame(20, (int) $this->buyer->cartItems()->first()->quantity);

        $this->asBuyer()->get(route('marketplace.cart.index'))
            ->assertOk()
            ->assertViewHas('subtotal', 230000.0);

        $this->asBuyer()->postJson(route('marketplace.cart.save-for-later'), [
            'product_id' => $this->product->id,
        ])->assertOk();

        $this->assertSame(0, $this->buyer->cartItems()->where('saved_for_later', false)->sum('quantity'));

        $this->asBuyer()->postJson(route('marketplace.cart.move-to-cart'), [
            'product_id' => $this->product->id,
        ])->assertOk();

        $this->assertSame(20, $this->buyer->cartItems()->where('saved_for_later', false)->sum('quantity'));

        $this->asBuyer()->postJson(route('marketplace.cart.remove'), [
            'product_id' => $this->product->id,
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertSame(0, CartItem::where('user_id', $this->buyer->id)->count());
    }

    public function test_an_out_of_stock_product_cannot_be_added(): void
    {
        $this->product->update(['stock_quantity' => 0]);

        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ])->assertOk()->assertJson(['success' => false]);
    }

    public function test_a_customer_cannot_touch_another_customers_cart(): void
    {
        $intruder = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->actingAs($intruder)->withSession(['marketplace_state' => 'Lagos'])
            ->getJson(route('marketplace.cart.count'))
            ->assertOk()
            ->assertJson(['count' => 0]);
    }

    // --------------------------------------------------------- checkout

    public function test_checkout_reflects_the_cart_and_prices_delivery(): void
    {
        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ])->assertOk();

        $this->asBuyer()->get(route('marketplace.checkout.index', ['delivery_type' => 'home_delivery', 'delivery_state' => 'Lagos']))
            ->assertOk()
            ->assertViewHas('data', function (array $data) {
                // 2 x 11,500 subtotal, and 1,500 base + (10kg - 5kg) x 200 weight
                // surcharge for shipping.
                return $data['subtotal'] === 23000.00
                    && $data['shipping_fee'] === 2500.00
                    && $data['total'] === 25500.00
                    && $data['points_to_earn'] === 255;
            });

        $this->asBuyer()->postJson(route('marketplace.checkout.estimate'), [
            'state' => 'Lagos',
            'delivery_type' => 'home_delivery',
        ])->assertOk()->assertJsonStructure(['delivery_fee', 'estimated_days', 'estimated_delivery_date', 'total']);
    }

    public function test_an_empty_cart_sends_the_customer_back_to_the_cart(): void
    {
        $this->asBuyer()->get(route('marketplace.checkout.index'))
            ->assertRedirect(route('marketplace.cart.index'))
            ->assertSessionHas('error');
    }

    public function test_a_coupon_is_validated_and_discounted_at_checkout(): void
    {
        $coupon = Coupon::create([
            'code' => 'E2E10',
            'name' => 'Ten percent off',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 5000,
            'usage_limit' => 100,
            'used_count' => 0,
            'per_user_limit' => 2,
            'is_active' => true,
        ]);

        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ])->assertOk();

        $this->asBuyer()->postJson(route('marketplace.checkout.apply-coupon'), ['coupon_code' => 'E2E10'])
            ->assertOk()
            ->assertJson(['success' => true, 'discount' => 2300.0]);

        $this->asBuyer()->postJson(route('marketplace.checkout.apply-coupon'), ['coupon_code' => 'NOPE'])
            ->assertOk()
            ->assertJson(['success' => false]);

        $this->asBuyer()->get(route('marketplace.checkout.index', ['coupon_code' => 'E2E10', 'delivery_state' => 'Lagos']))
            ->assertOk()
            ->assertViewHas('data', fn (array $data) => $data['coupon_discount'] === 2300.0);

        $this->asBuyer()->post(route('marketplace.checkout.store'), [
            'payment_method' => 'wallet',
            'delivery_type' => 'home_delivery',
            'delivery_state' => 'Lagos',
            'coupon_code' => 'E2E10',
        ])->assertRedirect();

        $order = Order::latest('id')->firstOrFail();

        $this->assertSame(2300.00, (float) $order->coupon_discount);
        $this->assertSame(23200.00, (float) $order->total);
        $this->assertSame(1, (int) $coupon->fresh()->used_count);
    }

    public function test_an_unexpired_coupon_is_rejected(): void
    {
        Coupon::create([
            'code' => 'DEAD',
            'name' => 'Expired',
            'type' => 'fixed',
            'value' => 500,
            'min_order_amount' => 0,
            'usage_limit' => 10,
            'used_count' => 0,
            'per_user_limit' => 1,
            'expires_at' => now()->subDay(),
            'is_active' => true,
        ]);

        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->asBuyer()->postJson(route('marketplace.checkout.apply-coupon'), ['coupon_code' => 'DEAD'])
            ->assertOk()
            ->assertJson(['success' => false]);
    }

    // ------------------------------------------- purchase to fulfilment

    public function test_the_full_purchase_fulfilment_and_payout_journey(): void
    {
        // 1. The shopper pays from the wallet.
        $order = $this->buyWalletOrder(2);

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('paid', $order->status);
        $this->assertSame(25500.00, (float) $order->total);
        $this->assertEquals(474500.0, (float) $this->buyer->fresh()->wallet_balance);
        $this->assertSame(18, $this->product->fresh()->stock_quantity);
        $this->assertSame(255, (int) $this->buyer->fresh()->loyalty_points_balance);
        $this->assertSame(0, CartItem::where('user_id', $this->buyer->id)->count());

        $item = $order->items()->firstOrFail();
        $this->assertSame('pending', $item->status);

        // 2. The shopper can see it, and nobody else can.
        $this->asBuyer()->get(route('marketplace.orders.index'))->assertOk()->assertSee($order->order_number);
        $this->asBuyer()->get(route('marketplace.orders.show', $order))->assertOk();
        $this->asBuyer()->get(route('marketplace.orders.invoice', $order))->assertOk();

        $this->actingAs($this->rivalVendorUser)
            ->get(route('marketplace.orders.show', $order))
            ->assertForbidden();

        // 3. The vendor sees only their allocated line and cannot self-deliver.
        $this->actingAs($this->vendorUser)->get(route('vendor.orders.index'))
            ->assertOk()
            ->assertViewHas('items', fn ($items) => $items->count() === 1 && $items->first()->id === $item->id);

        $this->actingAs($this->vendorUser)
            ->from(route('vendor.orders.index'))
            ->post(route('vendor.orders.status', $item), ['status' => 'delivered'])
            ->assertSessionHasErrors('status');

        $this->assertSame('pending', $item->fresh()->status);

        // 4. The vendor works the line up to dispatched.
        foreach (['confirmed', 'processing', 'packed', 'shipped'] as $status) {
            $this->actingAs($this->vendorUser)
                ->from(route('vendor.orders.index'))
                ->post(route('vendor.orders.status', $item), ['status' => $status])
                ->assertRedirect(route('vendor.orders.index'))
                ->assertSessionHas('success');

            $this->assertSame($status, $item->fresh()->status);
        }

        // The last item shipping rolls the parent order up to shipped.
        $this->assertSame('shipped', $order->fresh()->status);

        $this->actingAs($this->vendorUser)->get(route('vendor.orders.packing-slips'))->assertOk();

        // 5. The shopper confirms delivery; items cascade so earnings unlock.
        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.confirm-delivery', $order))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('delivered', $order->fresh()->status);
        $this->assertSame('delivered', $item->fresh()->status);
        $this->assertNotNull($order->fresh()->delivered_at);

        // 6. The vendor is paid: 23,000 gross less 10% commission.
        $this->actingAs($this->vendorUser)->get(route('vendor.payouts.index'))
            ->assertOk()
            ->assertViewHas('summary', function (array $summary) {
                return $summary['gross_sales'] === 23000.0
                    && $summary['commission_amount'] === 2300.0
                    && $summary['net_earnings'] === 20700.0
                    && $summary['available'] === 20700.0;
            });

        // Below the minimum is rejected.
        $this->actingAs($this->vendorUser)
            ->from(route('vendor.payouts.index'))
            ->post(route('vendor.payouts.store'), $this->payoutPayload(1000))
            ->assertRedirect(route('vendor.payouts.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, VendorPayout::where('vendor_id', $this->vendor->id)->count());

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.store'), $this->payoutPayload(15000))
            ->assertRedirect(route('vendor.payouts.index'))
            ->assertSessionHas('success');

        $this->assertSame(1, VendorPayout::where('vendor_id', $this->vendor->id)->count());

        // The remainder stays available; the request is reserved, not lost.
        $this->actingAs($this->vendorUser)->get(route('vendor.payouts.index'))
            ->assertViewHas('summary', fn (array $summary) => $summary['pending'] === 15000.0 && $summary['available'] === 5700.0);

        // 7. The shopper can leave a review on the delivered item.
        $this->asBuyer()->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.review', [$order, $item]), [
                'rating' => 5,
                'title' => 'Great rice',
                'comment' => 'Arrived quickly and well packed.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('marketplace_reviews', [
            'order_item_id' => $item->id,
            'rating' => 5,
            'is_approved' => false,
        ]);
    }

    private function payoutPayload(int $amount): array
    {
        return [
            'amount' => $amount,
            'payout_method' => 'bank_transfer',
            'account_name' => 'E2E Farms Ltd',
            'account_number' => '0123456789',
            'bank_name' => 'Test Bank',
        ];
    }

    public function test_a_vendor_cannot_payout_more_than_they_earned(): void
    {
        $order = $this->buyWalletOrder(2);
        $item = $order->items()->firstOrFail();
        $order->updateStatus('delivered', 'E2E test delivery.');

        $this->actingAs($this->vendorUser)
            ->from(route('vendor.payouts.index'))
            ->post(route('vendor.payouts.store'), $this->payoutPayload(999999))
            ->assertRedirect(route('vendor.payouts.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, VendorPayout::where('vendor_id', $this->vendor->id)->count());
    }

    public function test_a_vendor_cannot_touch_another_vendors_order_item(): void
    {
        $order = $this->buyWalletOrder(1);
        $item = $order->items()->firstOrFail();

        $this->actingAs($this->rivalVendorUser)
            ->post(route('vendor.orders.status', $item), ['status' => 'shipped'])
            ->assertNotFound();

        $this->assertSame('pending', $item->fresh()->status);
    }

    public function test_a_customers_order_pages_are_closed_to_other_customers(): void
    {
        $order = $this->buyWalletOrder(1);

        $intruder = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $this->actingAs($intruder)->get(route('marketplace.orders.show', $order))->assertForbidden();
        $this->actingAs($intruder)->get(route('marketplace.orders.invoice', $order))->assertForbidden();
        $this->actingAs($intruder)->post(route('marketplace.orders.cancel', $order))->assertForbidden();
        $this->actingAs($intruder)->post(route('marketplace.orders.confirm-delivery', $order))->assertForbidden();

        $this->actingAs($intruder)->get(route('marketplace.orders.index'))
            ->assertOk()
            ->assertDontSee($order->order_number);
    }

    public function test_a_plain_customer_cannot_open_the_vendor_centre(): void
    {
        $this->asBuyer()->get(route('vendor.dashboard'))->assertForbidden();
        $this->asBuyer()->get(route('vendor.orders.index'))->assertForbidden();
        $this->asBuyer()->get(route('vendor.payouts.index'))->assertForbidden();
    }

    public function test_a_pending_vendor_is_held_at_the_door_until_approved(): void
    {
        $applicant = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        Vendor::create([
            'user_id' => $applicant->id,
            'name' => 'Waiting Store',
            'slug' => 'waiting-store',
            'business_name' => 'Waiting Store Ltd',
            'status' => 'pending',
            'commission_rate' => 10,
        ]);

        $this->actingAs($applicant)->get(route('vendor.dashboard'))
            ->assertRedirect(route('account.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_a_suspended_vendor_is_locked_out(): void
    {
        $this->vendor->update(['status' => 'suspended']);

        $this->actingAs($this->vendorUser)->get(route('vendor.dashboard'))->assertForbidden();
    }

    // --------------------------------------------- cancellation and return

    public function test_cancelling_a_paid_order_refunds_the_wallet_and_restocks(): void
    {
        $order = $this->buyWalletOrder(2);

        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.cancel', $order), ['reason' => 'Changed my mind.'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertSame('cancelled', $order->status);
        $this->assertNotNull($order->cancelled_at);
        $this->assertSame('cancelled', $order->items()->firstOrFail()->status);
        $this->assertSame(20, $this->product->fresh()->stock_quantity);
        $this->assertEquals(500000.0, (float) $this->buyer->fresh()->wallet_balance);
        $this->assertSame(0, (int) $this->buyer->fresh()->loyalty_points_balance);

        // The cancelled sale must not count as revenue anywhere.
        $this->assertSame(0, OrderItem::where('status', 'delivered')->count());
    }

    public function test_a_delivered_order_can_no_longer_be_cancelled(): void
    {
        $order = $this->buyWalletOrder(1);
        $order->updateStatus('delivered', 'E2E test delivery.');

        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.cancel', $order))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('delivered', $order->fresh()->status);
    }

    public function test_delivery_can_only_be_confirmed_once_shipped(): void
    {
        $order = $this->buyWalletOrder(1);

        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.confirm-delivery', $order))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_a_return_request_is_raised_only_for_delivered_orders(): void
    {
        $order = $this->buyWalletOrder(1);

        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.request-return', $order), ['reason' => 'Bag was short'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $order->updateStatus('delivered', 'E2E test delivery.');

        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.request-return', $order), ['reason' => 'The bag was only 4kg, not 5kg.'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue((bool) $order->fresh()->meta['return_requested']);
    }

    public function test_a_return_request_needs_a_real_reason(): void
    {
        $order = $this->buyWalletOrder(1);
        $order->updateStatus('delivered', 'E2E test delivery.');

        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.request-return', $order), ['reason' => 'bad'])
            ->assertSessionHasErrors('reason');
    }

    public function test_reordering_refills_the_cart_but_skips_sold_out_lines(): void
    {
        $order = $this->buyWalletOrder(2);
        $order->updateStatus('delivered', 'E2E test delivery.');

        $this->product->update(['stock_quantity' => 0]);

        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.reorder', $order))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->product->update(['stock_quantity' => 10]);

        $this->asBuyer()
            ->from(route('marketplace.orders.show', $order))
            ->post(route('marketplace.orders.reorder', $order))
            ->assertRedirect(route('marketplace.cart.index'))
            ->assertSessionHas('success');

        $this->assertSame(2, (int) $this->buyer->cartItems()->sum('quantity'));
    }

    // --------------------------------------------------------- validation

    public function test_checkout_rejects_unknown_payment_and_delivery_methods(): void
    {
        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->asBuyer()
            ->post(route('marketplace.checkout.store'), [
                'payment_method' => 'crypto',
                'delivery_type' => 'teleport',
                'delivery_state' => 'Lagos',
            ])
            ->assertSessionHasErrors(['payment_method', 'delivery_type']);

        $this->assertDatabaseCount('marketplace_orders', 0);
    }

    public function test_stock_is_never_oversold_across_two_buyers(): void
    {
        $this->product->update(['stock_quantity' => 1]);

        $second = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
            'wallet_balance' => 500000,
        ]);

        // Both shoppers get the last unit into their cart while it is still
        // available, then race for it at checkout.
        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->actingAs($second)->withSession(['marketplace_state' => 'Lagos'])
            ->postJson(route('marketplace.cart.add'), [
                'product_id' => $this->product->id,
                'quantity' => 1,
            ])->assertOk();

        $this->buyWalletOrder(1);
        $this->assertSame(0, $this->product->fresh()->stock_quantity);

        $this->actingAs($second)->withSession(['marketplace_state' => 'Lagos'])
            ->from(route('marketplace.checkout.index'))
            ->post(route('marketplace.checkout.store'), [
                'payment_method' => 'wallet',
                'delivery_type' => 'home_delivery',
                'delivery_state' => 'Lagos',
            ])
            ->assertRedirect(route('marketplace.checkout.index'))
            ->assertSessionHas('error');

        $this->assertSame(1, Order::count());
        // The loser must not have been charged, charged points, or pushed the
        // stock counter below zero.
        $this->assertSame(0, $this->product->fresh()->stock_quantity);
        $this->assertEquals(500000.0, (float) $second->fresh()->wallet_balance);
        $this->assertSame(0, (int) $second->fresh()->loyalty_points_balance);
    }

    public function test_an_item_delisted_while_in_the_cart_cannot_be_checked_out(): void
    {
        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ])->assertOk();

        // The vendor pulls the listing while the shopper is still deciding.
        $this->product->update(['is_active' => false]);

        $this->asBuyer()
            ->from(route('marketplace.checkout.index'))
            ->post(route('marketplace.checkout.store'), [
                'payment_method' => 'wallet',
                'delivery_type' => 'home_delivery',
                'delivery_state' => 'Lagos',
            ])
            ->assertRedirect(route('marketplace.checkout.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('marketplace_orders', 0);
        $this->assertEquals(500000.0, (float) $this->buyer->fresh()->wallet_balance);
        $this->assertSame(20, $this->product->fresh()->stock_quantity);
    }

    public function test_checkout_warns_before_paying_about_lines_that_cannot_be_bought(): void
    {
        $this->asBuyer()->postJson(route('marketplace.cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ])->assertOk();

        $this->asBuyer()->get(route('marketplace.checkout.index'))
            ->assertOk()
            ->assertViewHas('data', fn (array $data) => $data['unavailable_items'] === []);

        // Stock drops below the cart quantity behind the shopper's back.
        $this->product->update(['stock_quantity' => 1]);

        $this->asBuyer()->get(route('marketplace.checkout.index'))
            ->assertOk()
            ->assertSee('can no longer be bought')
            ->assertViewHas('data', fn (array $data) => $data['unavailable_items'] === ['Premium Rice 5kg']);
    }

    public function test_a_vendor_payout_cannot_be_cancelled_after_an_admin_acts_on_it(): void
    {
        $order = $this->buyWalletOrder(2);
        $order->updateStatus('delivered', 'E2E test delivery.');

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.store'), $this->payoutPayload(10000))
            ->assertRedirect(route('vendor.payouts.index'));

        $payout = VendorPayout::where('vendor_id', $this->vendor->id)->firstOrFail();
        $payout->update(['status' => 'paid']);

        $this->actingAs($this->vendorUser)
            ->from(route('vendor.payouts.index'))
            ->post(route('vendor.payouts.cancel', $payout))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('paid', $payout->fresh()->status);
    }

    public function test_a_vendor_cannot_cancel_another_vendors_payout(): void
    {
        $order = $this->buyWalletOrder(2);
        $order->updateStatus('delivered', 'E2E test delivery.');

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.store'), $this->payoutPayload(10000))
            ->assertRedirect(route('vendor.payouts.index'));

        $payout = VendorPayout::where('vendor_id', $this->vendor->id)->firstOrFail();

        $this->actingAs($this->rivalVendorUser)
            ->post(route('vendor.payouts.cancel', $payout))
            ->assertNotFound();

        $this->assertSame('requested', $payout->fresh()->status);
    }

    public function test_the_payout_ledger_records_the_commission_snapshot(): void
    {
        $order = $this->buyWalletOrder(2);
        $order->updateStatus('delivered', 'E2E test delivery.');

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.store'), $this->payoutPayload(10000))
            ->assertRedirect(route('vendor.payouts.index'));

        $payout = VendorPayout::where('vendor_id', $this->vendor->id)->firstOrFail();

        $this->assertSame('requested', $payout->status);
        $this->assertSame(10000.00, (float) $payout->amount);
        $this->assertSame(23000.00, (float) $payout->gross_sales);
        $this->assertSame(2300.00, (float) $payout->commission_amount);
        $this->assertSame(10.00, (float) $payout->commission_rate);
        $this->assertNotEmpty($payout->reference);
        $this->assertNotNull($payout->requested_at);
        $this->assertSame(1, DB::table('marketplace_vendor_payouts')->where('vendor_id', $this->vendor->id)->count());
    }
}

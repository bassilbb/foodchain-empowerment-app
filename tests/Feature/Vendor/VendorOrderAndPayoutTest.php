<?php

namespace Tests\Feature\Vendor;

use App\Models\Marketplace\Category;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use App\Models\User;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Services\Marketplace\VendorEarningsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorOrderAndPayoutTest extends TestCase
{
    use RefreshDatabase;

    private User $vendorUser;

    private Vendor $vendor;

    private Vendor $otherVendor;

    private User $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        [$this->vendorUser, $this->vendor] = $this->makeVendor();
        [, $this->otherVendor] = $this->makeVendor();

        $this->buyer = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);
    }

    /** @return array{0: User, 1: Vendor} */
    private function makeVendor(array $overrides = []): array
    {
        $user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $vendor = Vendor::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Store ' . uniqid(),
            'slug' => 'store-' . uniqid(),
            'business_name' => 'Business ' . uniqid(),
            'status' => 'approved',
            'commission_rate' => 10,
        ], $overrides));

        return [$user, $vendor];
    }

    /**
     * Create an order item allocated to a vendor.
     */
    private function orderItemFor(Vendor $vendor, string $itemStatus = 'pending', float $total = 10000, array $orderOverrides = []): OrderItem
    {
        $order = Order::create(array_merge([
            'user_id' => $this->buyer->id,
            'order_number' => 'FC-V-' . uniqid(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => $total,
            'total' => $total,
        ], $orderOverrides));

        $category = Category::create(['name' => 'C ' . uniqid(), 'slug' => 'c-' . uniqid(), 'is_active' => true]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Product ' . uniqid(),
            'slug' => 'p-' . uniqid(),
            'sku' => Product::generateSku(),
            'price' => $total,
            'stock_quantity' => 50,
            'is_active' => true,
        ]);

        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'unit_price' => $total,
            'quantity' => 1,
            'total_price' => $total,
            'status' => $itemStatus,
        ]);
    }

    // -------------------------------------------------- order visibility

    public function test_vendor_sees_only_their_own_order_items(): void
    {
        $mine = $this->orderItemFor($this->vendor);
        $theirs = $this->orderItemFor($this->otherVendor);

        $response = $this->actingAs($this->vendorUser)->get(route('vendor.orders.index'));

        $response->assertOk();
        $response->assertViewHas('items', function ($items) use ($mine, $theirs) {
            return $items->contains(fn ($i) => $i->id === $mine->id)
                && ! $items->contains(fn ($i) => $i->id === $theirs->id);
        });
    }

    public function test_order_search_cannot_leak_another_vendors_items(): void
    {
        $theirs = $this->orderItemFor($this->otherVendor);
        $theirName = $theirs->product_name;

        $response = $this->actingAs($this->vendorUser)
            ->get(route('vendor.orders.index', ['q' => $theirName]));

        $response->assertOk();
        $response->assertViewHas('items', fn($items) => $items->total() === 0);
    }

    // ------------------------------------------------------- status rules

    public function test_vendor_can_accept_an_allocated_item(): void
    {
        $item = $this->orderItemFor($this->vendor, 'pending');

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.orders.status', $item), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertSame('confirmed', $item->fresh()->status);
    }

    public function test_vendor_cannot_set_an_item_to_delivered(): void
    {
        $item = $this->orderItemFor($this->vendor, 'shipped');

        // Delivery is confirmed by the customer or an admin — a vendor must not be
        // able to trigger their own payout.
        $this->actingAs($this->vendorUser)
            ->post(route('vendor.orders.status', $item), ['status' => 'delivered'])
            ->assertSessionHasErrors('status');

        $this->assertSame('shipped', $item->fresh()->status);
    }

    public function test_vendor_cannot_change_another_vendors_order_item(): void
    {
        $theirs = $this->orderItemFor($this->otherVendor, 'pending');

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.orders.status', $theirs), ['status' => 'confirmed'])
            ->assertNotFound();

        $this->assertSame('pending', $theirs->fresh()->status);
    }

    public function test_order_rolls_up_to_shipped_when_all_items_ship(): void
    {
        $first = $this->orderItemFor($this->vendor, 'packed');

        // A second item on the same order, belonging to the same vendor.
        $sibling = OrderItem::create([
            'order_id' => $first->order_id,
            'product_id' => $first->product_id,
            'vendor_id' => $this->vendor->id,
            'product_name' => 'Sibling item',
            'unit_price' => 100,
            'quantity' => 1,
            'total_price' => 100,
            'status' => 'packed',
        ]);

        $this->actingAs($this->vendorUser)->post(route('vendor.orders.status', $first), ['status' => 'shipped']);
        $this->actingAs($this->vendorUser)->post(route('vendor.orders.status', $sibling), ['status' => 'shipped']);

        $this->assertSame('shipped', $first->order->fresh()->status);
    }

    // ------------------------------------------------ delivery cascade

    public function test_customer_confirming_delivery_marks_the_items_delivered(): void
    {
        $item = $this->orderItemFor($this->vendor, 'shipped', 10000);
        $item->order->update(['status' => 'shipped']);

        $this->actingAs($this->buyer)
            ->post(route('account.orders.confirm-delivery', $item->order_id))
            ->assertRedirect();

        $this->assertSame('delivered', $item->order->fresh()->status);
        $this->assertSame(
            'delivered',
            $item->fresh()->status,
            'An order cannot be delivered while its items still read as shipped.'
        );
    }

    public function test_delivering_an_order_unlocks_the_vendor_earnings(): void
    {
        $item = $this->orderItemFor($this->vendor, 'shipped', 20000);
        $order = $item->order;
        $order->update(['status' => 'shipped']);

        $earnings = app(VendorEarningsService::class);
        // max(0, ...) returns int 0 for a zero result, so compare loosely here.
        $this->assertEquals(0, $earnings->summary($this->vendor)['available']);

        $this->actingAs($this->buyer)->post(route('account.orders.confirm-delivery', $order->id));

        $summary = $earnings->summary($this->vendor);

        $this->assertSame(20000.0, $summary['gross_sales']);
        $this->assertSame(18000.0, $summary['available'], 'A delivered order must become withdrawable.');
    }

    public function test_cancelling_an_order_removes_its_items_from_earnings(): void
    {
        $item = $this->orderItemFor($this->vendor, 'delivered', 20000);
        $earnings = app(VendorEarningsService::class);

        $this->assertSame(20000.0, $earnings->summary($this->vendor)['gross_sales']);

        $item->order->updateStatus('cancelled', 'Cancelled by an administrator.');

        $this->assertSame(0.0, $earnings->summary($this->vendor)['gross_sales']);
        $this->assertSame('cancelled', $item->fresh()->status);
    }

    public function test_a_vendor_cannot_mark_an_item_delivered(): void
    {
        $item = $this->orderItemFor($this->vendor, 'shipped');

        // Delivered is what releases earnings, so only an administrator may set it.
        $this->actingAs($this->vendorUser)
            ->post(route('vendor.orders.status', $item), ['status' => 'delivered'])
            ->assertSessionHasErrors('status');

        $this->assertSame('shipped', $item->fresh()->status);
        $this->assertEquals(0, app(VendorEarningsService::class)->summary($this->vendor)['available']);
    }

    public function test_the_orders_page_only_offers_allowed_statuses(): void
    {
        $this->orderItemFor($this->vendor, 'shipped');

        $response = $this->actingAs($this->vendorUser)->get(route('vendor.orders.index'));

        $response->assertOk();
        $response->assertViewHas('allowedStatuses', VendorOrderController::VENDOR_ALLOWED_STATUSES);
        $response->assertDontSee('<option value="delivered"', false);
    }

    /**
     * /marketplace/vendor used to be a full duplicate centre. It is now a set of
     * redirects onto the canonical Vendor Centre, so old links still resolve.
     */
    public function test_the_legacy_vendor_urls_redirect_to_the_vendor_centre(): void
    {
        $redirects = [
            route('marketplace.vendor.dashboard') => route('vendor.dashboard'),
            route('marketplace.vendor.products') => route('vendor.products.index'),
            route('marketplace.vendor.products.create') => route('vendor.products.create'),
            route('marketplace.vendor.inventory') => route('vendor.inventory'),
            route('marketplace.vendor.orders') => route('vendor.orders.index'),
            route('marketplace.vendor.packing-slips') => route('vendor.orders.packing-slips'),
            route('marketplace.vendor.reports') => route('vendor.reports'),
        ];

        foreach ($redirects as $from => $to) {
            $this->actingAs($this->vendorUser)->get($from)->assertRedirect($to);
        }

        // The legacy write endpoints are gone, not merely redirected.
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('marketplace.vendor.orders.status'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('marketplace.vendor.products.store'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('marketplace.vendor.products.delete'));
    }

    // ------------------------------------------------------ earnings math

    public function test_earnings_are_recognised_only_for_delivered_items(): void
    {
        $this->orderItemFor($this->vendor, 'shipped', 10000);
        $this->orderItemFor($this->vendor, 'pending', 5000);

        $summary = app(VendorEarningsService::class)->summary($this->vendor);

        $this->assertSame(0.0, $summary['gross_sales'], 'Undelivered items must not count as earnings.');
    }

    public function test_commission_is_applied_from_the_vendors_own_rate(): void
    {
        $this->orderItemFor($this->vendor, 'delivered', 10000);

        $summary = app(VendorEarningsService::class)->summary($this->vendor);

        $this->assertSame(10000.0, $summary['gross_sales']);
        $this->assertSame(10.0, $summary['commission_rate']);
        $this->assertSame(1000.0, $summary['commission_amount']);
        $this->assertSame(9000.0, $summary['net_earnings']);
        $this->assertSame(9000.0, $summary['available']);
    }

    public function test_a_different_commission_rate_changes_the_net_earnings(): void
    {
        [, $vendor15] = $this->makeVendor(['commission_rate' => 15]);

        $this->orderItemFor($vendor15, 'delivered', 20000);

        $summary = app(VendorEarningsService::class)->summary($vendor15);

        $this->assertSame(3000.0, $summary['commission_amount']);
        $this->assertSame(17000.0, $summary['net_earnings']);
    }

    public function test_earnings_do_not_include_another_vendors_sales(): void
    {
        $this->orderItemFor($this->vendor, 'delivered', 10000);
        $this->orderItemFor($this->otherVendor, 'delivered', 999999);

        $summary = app(VendorEarningsService::class)->summary($this->vendor);

        $this->assertSame(10000.0, $summary['gross_sales'], 'Another vendor\'s sales must never be included.');
    }

    public function test_vendor_cannot_see_another_vendors_earnings_page_data(): void
    {
        $this->orderItemFor($this->otherVendor, 'delivered', 500000);

        $response = $this->actingAs($this->vendorUser)->get(route('vendor.payouts.index'));

        $response->assertOk();
        $response->assertViewHas('summary', fn($summary) => $summary['gross_sales'] === 0.0);
    }

    // -------------------------------------------------------- payout flow

    public function test_vendor_can_request_a_payout_within_available_balance(): void
    {
        $this->orderItemFor($this->vendor, 'delivered', 100000);

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.store'), [
                'amount' => 50000,
                'payout_method' => 'bank_transfer',
                'account_name' => 'Test Store',
                'account_number' => '0123456789',
                'bank_name' => 'Test Bank',
            ])
            ->assertRedirect(route('vendor.payouts.index'));

        $this->assertDatabaseHas('marketplace_vendor_payouts', [
            'vendor_id' => $this->vendor->id,
            'status' => 'requested',
        ]);
    }

    public function test_payout_request_is_capped_at_the_available_balance(): void
    {
        $this->orderItemFor($this->vendor, 'delivered', 10000);
        // Net = 9000 after 10% commission.

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.store'), [
                'amount' => 50000,
                'payout_method' => 'bank_transfer',
                'account_name' => 'Test Store',
                'account_number' => '0123456789',
                'bank_name' => 'Test Bank',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('marketplace_vendor_payouts', 0);
    }

    public function test_a_pending_payout_reduces_the_available_balance(): void
    {
        $this->orderItemFor($this->vendor, 'delivered', 100000);

        $earnings = app(VendorEarningsService::class);
        $this->assertSame(90000.0, $earnings->summary($this->vendor)['available']);

        VendorPayout::create([
            'vendor_id' => $this->vendor->id,
            'reference' => VendorPayout::generateReference(),
            'amount' => 40000,
            'status' => 'requested',
            'requested_at' => now(),
        ]);

        $after = $earnings->summary($this->vendor);
        $this->assertSame(40000.0, $after['pending']);
        $this->assertSame(50000.0, $after['available'], 'Requesting a payout must reserve that money.');
    }

    public function test_a_paid_payout_reduces_the_available_balance(): void
    {
        $this->orderItemFor($this->vendor, 'delivered', 100000);

        VendorPayout::create([
            'vendor_id' => $this->vendor->id,
            'reference' => VendorPayout::generateReference(),
            'amount' => 90000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $summary = app(VendorEarningsService::class)->summary($this->vendor);

        $this->assertSame(90000.0, $summary['paid']);
        // max(0, ...) returns int 0 for a zero result, so compare loosely here.
        $this->assertEquals(0, $summary['available']);
    }

    public function test_available_balance_never_goes_negative(): void
    {
        $this->orderItemFor($this->vendor, 'delivered', 10000);

        // Over-payout relative to earnings.
        VendorPayout::create([
            'vendor_id' => $this->vendor->id,
            'reference' => VendorPayout::generateReference(),
            'amount' => 50000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $summary = app(VendorEarningsService::class)->summary($this->vendor);

        $this->assertGreaterThanOrEqual(0, $summary['available']);
        $this->assertEquals(0, $summary['available']);
    }

    public function test_payout_below_the_minimum_is_rejected(): void
    {
        $this->orderItemFor($this->vendor, 'delivered', 100000);

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.store'), [
                'amount' => 100,
                'payout_method' => 'bank_transfer',
                'account_name' => 'Test Store',
                'account_number' => '0123456789',
                'bank_name' => 'Test Bank',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('marketplace_vendor_payouts', 0);
    }

    public function test_vendor_cannot_cancel_another_vendors_payout(): void
    {
        $theirs = VendorPayout::create([
            'vendor_id' => $this->otherVendor->id,
            'reference' => VendorPayout::generateReference(),
            'amount' => 5000,
            'status' => 'requested',
        ]);

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.cancel', $theirs))
            ->assertNotFound();

        $this->assertSame('requested', $theirs->fresh()->status);
    }

    public function test_vendor_can_cancel_their_own_requested_payout(): void
    {
        $mine = VendorPayout::create([
            'vendor_id' => $this->vendor->id,
            'reference' => VendorPayout::generateReference(),
            'amount' => 5000,
            'status' => 'requested',
        ]);

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.cancel', $mine))
            ->assertRedirect();

        $this->assertSame('cancelled', $mine->fresh()->status);
    }

    public function test_vendor_cannot_cancel_an_already_paid_payout(): void
    {
        $paid = VendorPayout::create([
            'vendor_id' => $this->vendor->id,
            'reference' => VendorPayout::generateReference(),
            'amount' => 5000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->actingAs($this->vendorUser)
            ->post(route('vendor.payouts.cancel', $paid))
            ->assertSessionHas('error');

        $this->assertSame('paid', $paid->fresh()->status);
    }
}

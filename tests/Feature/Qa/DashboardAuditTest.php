<?php

namespace Tests\Feature\Qa;

use App\Models\AdminUser;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use App\Models\NigerianState;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phases 12–16 QA audit.
 *
 * Three things are proven here, all against real HTTP requests:
 *  1. Dashboard query counts stay flat as row counts grow (no N+1 — Section 25).
 *  2. The Step 30 authorization matrix: id probing across all three dashboards.
 *  3. State-based pricing is preserved on historical orders (Section 26).
 */
class DashboardAuditTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        return User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);
    }

    /** @return array{0: User, 1: Vendor} */
    private function vendor(): array
    {
        $user = $this->customer();

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => 'Store ' . uniqid(),
            'slug' => 'store-' . uniqid(),
            'business_name' => 'Business ' . uniqid(),
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        return [$user, $vendor];
    }

    private function category(): Category
    {
        return Category::create([
            'name' => 'Cat ' . uniqid(),
            'slug' => 'cat-' . uniqid(),
            'is_active' => true,
        ]);
    }

    private function productFor(Vendor $vendor, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $this->category()->id,
            'name' => 'Rice 5kg',
            'slug' => 'rice-' . uniqid(),
            'sku' => Product::generateSku(),
            'price' => 5000,
            'stock_quantity' => 20,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ], $overrides));
    }

    private function orderFor(User $user, array $attributes = []): Order
    {
        return Order::create(array_merge([
            'user_id' => $user->id,
            'order_number' => 'FC-' . uniqid(),
            'status' => 'delivered',
            'payment_status' => 'paid',
            'subtotal' => 5000,
            'total' => 5000,
        ], $attributes));
    }

    private function orderItemFor(Order $order, Product $product, array $attributes = []): OrderItem
    {
        return OrderItem::create(array_merge([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'vendor_id' => $product->vendor_id,
            'product_name' => $product->name,
            'unit_price' => 5000,
            'quantity' => 1,
            'total_price' => 5000,
            'status' => 'delivered',
        ], $attributes));
    }

    // ---------------------------------------------------------------------
    // Phase 15 — query-count scaling (N+1 guard)
    // ---------------------------------------------------------------------

    /**
     * The customer dashboard must not issue more queries as the customer's
     * order history grows: counters are aggregated, not looped.
     */
    public function test_customer_dashboard_query_count_does_not_grow_with_orders(): void
    {
        $user = $this->customer();

        $small = $this->countQueriesFor(fn() => $this
            ->actingAs($user)
            ->get(route('account.dashboard'))
            ->assertOk());

        // Now give the same customer 30 orders, each with an item.
        [$vendorUser, $vendor] = $this->vendor();
        $product = $this->productFor($vendor);

        for ($i = 0; $i < 30; $i++) {
            $this->orderItemFor($this->orderFor($user), $product);
        }

        $large = $this->countQueriesFor(fn() => $this
            ->actingAs($user)
            ->get(route('account.dashboard'))
            ->assertOk());

        // Allow a small constant delta for the recent-orders eager load, but the
        // count must NOT scale with the number of orders (which would be N+1).
        $this->assertLessThanOrEqual(
            $small + 3,
            $large,
            "Dashboard ran {$large} queries with 30 orders vs {$small} with none — likely N+1."
        );
    }

    /**
     * The vendor dashboard aggregates products, order items and payouts. Its
     * query count must not grow with the vendor's sales volume.
     */
    public function test_vendor_dashboard_query_count_does_not_grow_with_sales(): void
    {
        [$vendorUser, $vendor] = $this->vendor();
        $product = $this->productFor($vendor);

        $small = $this->countQueriesFor(fn() => $this
            ->actingAs($vendorUser)
            ->get(route('vendor.dashboard'))
            ->assertOk());

        for ($i = 0; $i < 30; $i++) {
            $this->orderItemFor($this->orderFor($vendorUser), $product);
        }

        $large = $this->countQueriesFor(fn() => $this
            ->actingAs($vendorUser)
            ->get(route('vendor.dashboard'))
            ->assertOk());

        $this->assertLessThanOrEqual(
            $small + 3,
            $large,
            "Vendor dashboard ran {$large} queries with 30 sales vs {$small} with none — likely N+1."
        );
    }

    /** Count the DB queries executed while running the given closure. */
    private function countQueriesFor(callable $callback): int
    {
        $count = 0;
        DB::listen(function () use (&$count) {
            $count++;
        });

        $callback();

        return $count;
    }

    // ---------------------------------------------------------------------
    // Phase 14 / Step 30 — authorization matrix (id probing)
    // ---------------------------------------------------------------------

    public function test_customer_cannot_open_another_customers_order(): void
    {
        $me = $this->customer();
        $other = $this->customer();
        $order = $this->orderFor($other);

        $this->actingAs($me)
            ->get(route('account.orders.show', $order))
            ->assertNotFound();
    }

    public function test_customer_cannot_cancel_another_customers_order(): void
    {
        $me = $this->customer();
        $other = $this->customer();
        $order = $this->orderFor($other, ['status' => 'pending', 'payment_status' => 'unpaid']);

        $this->actingAs($me)
            ->post(route('account.orders.cancel', $order), ['reason' => 'nope'])
            ->assertNotFound();

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_customer_cannot_touch_another_customers_address(): void
    {
        $me = $this->customer();
        $other = $this->customer();

        $address = UserAddress::create([
            'user_id' => $other->id,
            'full_name' => 'Other Person',
            'phone' => '08000000000',
            'address' => '1 Test Street',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'is_default' => true,
        ]);

        $this->actingAs($me)
            ->put(route('account.addresses.update', $address), [
                'full_name' => 'Hacked',
                'phone' => '08000000000',
                'address' => '1 Test Street',
                'city' => 'Lagos',
                'state' => 'Lagos',
            ])
            ->assertNotFound();

        $this->assertSame('Other Person', $address->fresh()->full_name);
    }

    public function test_plain_customer_cannot_reach_any_vendor_route(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer)
            ->get(route('vendor.dashboard'))
            ->assertForbidden();
    }

    public function test_vendor_cannot_open_another_vendors_product(): void
    {
        [, $vendor] = $this->vendor();
        [, $otherVendor] = $this->vendor();
        $otherProduct = $this->productFor($otherVendor);

        // The vendor user of the first store probing the second store's product.
        $probe = Vendor::where('id', $vendor->id)->first()->user;

        $this->actingAs($probe)
            ->get(route('vendor.products.edit', $otherProduct))
            ->assertNotFound();
    }

    public function test_vendor_cannot_update_another_vendors_product(): void
    {
        [, $vendor] = $this->vendor();
        [, $otherVendor] = $this->vendor();
        $otherProduct = $this->productFor($otherVendor, ['name' => 'Original Name']);

        $probe = Vendor::where('id', $vendor->id)->first()->user;

        $this->actingAs($probe)
            ->put(route('vendor.products.update', $otherProduct), [
                'name' => 'Hijacked',
                'category_id' => $otherProduct->category_id,
                'price' => 100,
                'stock_quantity' => 1,
            ])
            ->assertNotFound();

        $this->assertSame('Original Name', $otherProduct->fresh()->name);
    }

    public function test_vendor_cannot_change_another_vendors_order_item(): void
    {
        [, $vendor] = $this->vendor();
        [, $otherVendor] = $this->vendor();

        $otherProduct = $this->productFor($otherVendor);
        $item = $this->orderItemFor($this->orderFor($this->customer()), $otherProduct, ['status' => 'pending']);

        $probe = Vendor::where('id', $vendor->id)->first()->user;

        $this->actingAs($probe)
            ->post(route('vendor.orders.status', $item), ['status' => 'shipped'])
            ->assertNotFound();

        $this->assertSame('pending', $item->fresh()->status);
    }

    public function test_vendor_cannot_cancel_another_vendors_payout(): void
    {
        [, $otherVendor] = $this->vendor();
        [, $vendor] = $this->vendor();

        $payout = VendorPayout::create([
            'vendor_id' => $otherVendor->id,
            'reference' => 'PO-' . uniqid(),
            'amount' => 1000,
            'status' => 'requested',
        ]);

        $probe = Vendor::where('id', $vendor->id)->first()->user;

        $this->actingAs($probe)
            ->post(route('vendor.payouts.cancel', $payout))
            ->assertNotFound();
    }

    public function test_guest_cannot_reach_any_dashboard(): void
    {
        $this->get(route('account.dashboard'))->assertRedirect(route('login'));
        $this->get(route('vendor.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    // ---------------------------------------------------------------------
    // Section 26 — state-based pricing is preserved on historical orders
    // ---------------------------------------------------------------------

    /**
     * An order item stores the unit_price actually charged. Changing the
     * product's price (or its state override) afterwards must NOT alter the
     * historical order or its invoice total.
     */
    public function test_historical_order_prices_are_not_recalculated(): void
    {
        [$vendorUser, $vendor] = $this->vendor();
        $product = $this->productFor($vendor, ['price' => 5000]);

        $lagos = NigerianState::create(['name' => 'Lagos', 'slug' => 'lagos-' . uniqid(), 'is_active' => true]);

        // At purchase time the Lagos price was 6,500.
        $order = $this->orderFor($vendorUser, [
            'subtotal' => 13000,
            'total' => 13000,
            'delivery_state' => 'Lagos',
        ]);

        $item = $this->orderItemFor($order, $product, [
            'unit_price' => 6500,
            'quantity' => 2,
            'total_price' => 13000,
        ]);

        // Later, the product price and the state override both change.
        $product->update(['price' => 9999]);
        \App\Models\Marketplace\ProductStatePrice::create([
            'product_id' => $product->id,
            'state_id' => $lagos->id,
            'price' => 7777,
            'is_active' => true,
        ]);

        $this->actingAs($vendorUser)
            ->get(route('account.orders.show', $order))
            ->assertOk()
            ->assertViewHas('order', fn($viewOrder) => (float) $viewOrder->total === 13000.0);

        // The line item must still carry the original charged price.
        $this->assertSame('6500.00', $item->fresh()->unit_price);
        $this->assertSame('13000.00', $item->fresh()->total_price);
        $this->assertSame('13000.00', $order->fresh()->total);
    }

    // ---------------------------------------------------------------------
    // Phase 10 / 18 — admin marketplace activity is real and reachable
    // ---------------------------------------------------------------------

    public function test_admin_marketplace_dashboard_reflects_real_row_counts(): void
    {
        $admin = AdminUser::create([
            'name' => 'QA Admin',
            'email' => 'qa-audit@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        [$vendorUser, $vendor] = $this->vendor();
        $this->orderFor($vendorUser);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.marketplace.dashboard'))
            ->assertOk()
            ->assertViewHas('orderCount', 1)
            ->assertViewHas('vendorCount', 1)
            ->assertViewHas('productCount', 0);
    }

    public function test_admin_marketplace_reports_render(): void
    {
        $admin = AdminUser::create([
            'name' => 'QA Admin',
            'email' => 'qa-audit-2@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.marketplace.reports'))
            ->assertOk();
    }

    // ---------------------------------------------------------------------
    // Phase 5 — customer notifications & wishlist ownership
    // ---------------------------------------------------------------------

    public function test_customer_cannot_mark_another_customers_notification_as_read(): void
    {
        $me = $this->customer();
        $other = $this->customer();

        $notification = Notification::create([
            'user_id' => $other->id,
            'type' => 'order',
            'title' => 'Private',
            'message' => 'Secret',
            'is_read' => false,
        ]);

        $this->actingAs($me)
            ->post(route('account.notifications.read', $notification))
            ->assertNotFound();

        $this->assertFalse((bool) $notification->fresh()->is_read);
    }
}

<?php

namespace Tests\Feature\Account;

use App\Models\Marketplace\Category;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    private function activeUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ], $attributes));
    }

    private function orderFor(User $user, array $attributes = []): Order
    {
        return Order::create(array_merge([
            'user_id' => $user->id,
            'order_number' => 'FC-' . strtoupper(uniqid()),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 1000,
            'shipping_fee' => 500,
            'total' => 1500,
        ], $attributes));
    }

    private function productFor(?Vendor $vendor = null): Product
    {
        $vendor ??= Vendor::create([
            'name' => 'Test Vendor',
            'slug' => 'v-' . uniqid(),
            'business_name' => 'Test Vendor Ltd',
            'status' => 'approved',
        ]);

        $category = Category::create(['name' => 'Grains', 'slug' => 'grains-' . uniqid(), 'is_active' => true]);

        return Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Bag of Rice',
            'slug' => 'rice-' . uniqid(),
            'sku' => Product::generateSku(),
            'price' => 1000,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);
    }

    private function itemFor(Order $order, Product $product, array $attributes = []): OrderItem
    {
        return OrderItem::create(array_merge([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'vendor_id' => $product->vendor_id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'unit_price' => 1000,
            'quantity' => 2,
            'total_price' => 2000,
            'status' => 'pending',
        ], $attributes));
    }

    // ---------------------------------------------------------------------
    // Access control
    // ---------------------------------------------------------------------

    public function test_guest_cannot_access_order_pages(): void
    {
        $this->get(route('account.orders.index'))->assertRedirect(route('login'));
    }

    public function test_customer_sees_their_own_order_list(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['status' => 'delivered', 'payment_status' => 'paid']);

        $this->actingAs($user)
            ->get(route('account.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $me = $this->activeUser();
        $other = $this->activeUser();
        $foreignOrder = $this->orderFor($other);

        // IDOR probe: direct URL to a foreign order must 404, not reveal data.
        $this->actingAs($me)
            ->get(route('account.orders.show', $foreignOrder))
            ->assertNotFound();
    }

    public function test_customer_cannot_cancel_another_customers_order(): void
    {
        $me = $this->activeUser();
        $other = $this->activeUser();
        $foreignOrder = $this->orderFor($other, ['status' => 'pending']);

        $this->actingAs($me)
            ->post(route('account.orders.cancel', $foreignOrder))
            ->assertNotFound();

        $this->assertSame('pending', $foreignOrder->fresh()->status);
    }

    public function test_customer_cannot_reorder_another_customers_order(): void
    {
        $me = $this->activeUser();
        $other = $this->activeUser();
        $foreignOrder = $this->orderFor($other, ['status' => 'delivered']);

        $this->actingAs($me)
            ->post(route('account.orders.reorder', $foreignOrder))
            ->assertNotFound();
    }

    public function test_customer_cannot_download_another_customers_invoice(): void
    {
        $me = $this->activeUser();
        $other = $this->activeUser();
        $foreignOrder = $this->orderFor($other);

        $this->actingAs($me)
            ->get(route('account.orders.invoice', $foreignOrder))
            ->assertNotFound();
    }

    public function test_customer_cannot_review_an_item_on_another_customers_order(): void
    {
        $me = $this->activeUser();
        $other = $this->activeUser();
        $foreignOrder = $this->orderFor($other, ['status' => 'delivered']);
        $item = $this->itemFor($foreignOrder, $this->productFor());

        $this->actingAs($me)
            ->post(route('account.orders.review', [$foreignOrder, $item]), ['rating' => 5])
            ->assertNotFound();
    }

    public function test_customer_cannot_review_an_item_belonging_to_a_different_order(): void
    {
        $user = $this->activeUser();
        $orderA = $this->orderFor($user, ['status' => 'delivered']);
        $orderB = $this->orderFor($user, ['status' => 'delivered']);
        $itemOnB = $this->itemFor($orderB, $this->productFor());

        // Item belongs to order B, but is addressed through order A.
        $this->actingAs($user)
            ->post(route('account.orders.review', [$orderA, $itemOnB]), ['rating' => 5])
            ->assertNotFound();
    }

    // ---------------------------------------------------------------------
    // Filters & search
    // ---------------------------------------------------------------------

    public function test_order_list_filters_by_status_group(): void
    {
        $user = $this->activeUser();
        $pending = $this->orderFor($user, ['status' => 'pending']);
        $delivered = $this->orderFor($user, ['status' => 'delivered']);

        $this->actingAs($user)
            ->get(route('account.orders.index', ['status' => 'delivered']))
            ->assertOk()
            ->assertSee($delivered->order_number)
            ->assertDontSee($pending->order_number);
    }

    public function test_order_list_searches_by_order_number_and_product_name(): void
    {
        $user = $this->activeUser();

        $target = $this->orderFor($user, ['order_number' => 'FC-SEARCHTARGET']);
        $this->itemFor($target, $this->productFor());

        $other = $this->orderFor($user, ['order_number' => 'FC-OTHERORDER']);

        // By order number
        $this->actingAs($user)
            ->get(route('account.orders.index', ['q' => 'SEARCHTARGET']))
            ->assertOk()
            ->assertSee('FC-SEARCHTARGET')
            ->assertDontSee('FC-OTHERORDER');

        // By purchased product name
        $this->actingAs($user)
            ->get(route('account.orders.index', ['q' => 'Bag of Rice']))
            ->assertOk()
            ->assertSee('FC-SEARCHTARGET')
            ->assertDontSee('FC-OTHERORDER');
    }

    public function test_order_list_filters_by_date_range(): void
    {
        $user = $this->activeUser();

        // created_at is not fillable, so it must be forced after creation.
        $old = $this->orderFor($user, ['order_number' => 'FC-OLDORDER']);
        $old->forceFill(['created_at' => now()->subMonths(3)])->save();

        $recent = $this->orderFor($user, ['order_number' => 'FC-RECENTORDER']);
        $recent->forceFill(['created_at' => now()->subDay()])->save();

        $this->actingAs($user)
            ->get(route('account.orders.index', [
                'from' => now()->subDays(7)->toDateString(),
                'to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('FC-RECENTORDER')
            ->assertDontSee('FC-OLDORDER');
    }

    public function test_order_list_never_leaks_another_customers_orders_in_search(): void
    {
        $me = $this->activeUser();
        $other = $this->activeUser();
        $foreign = $this->orderFor($other, ['order_number' => 'FC-FOREIGNSECRET']);

        $this->actingAs($me)
            ->get(route('account.orders.index', ['q' => 'FOREIGNSECRET']))
            ->assertOk()
            ->assertDontSee('FC-FOREIGNSECRET');
    }

    public function test_order_list_ignores_an_unknown_status_group(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user);

        // An unrecognised status must not error or empty the list.
        $this->actingAs($user)
            ->get(route('account.orders.index', ['status' => 'not-a-real-status']))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    // ---------------------------------------------------------------------
    // Financial integrity
    // ---------------------------------------------------------------------

    public function test_order_detail_displays_the_recorded_financial_breakdown(): void
    {
        $user = $this->activeUser();

        $order = $this->orderFor($user, [
            'subtotal' => 2000,
            'shipping_fee' => 500,
            'coupon_code' => 'SAVE10',
            'coupon_discount' => 200,
            'total' => 2300,
        ]);
        $this->itemFor($order, $this->productFor());

        $this->actingAs($user)
            ->get(route('account.orders.show', $order))
            ->assertOk()
            ->assertSee('2,000.00')   // subtotal, as recorded
            ->assertSee('500.00')     // delivery fee
            ->assertSee('200.00')     // coupon discount
            ->assertSee('2,300.00')   // grand total, as recorded
            ->assertSee('SAVE10');
    }

    public function test_order_detail_preserves_historical_unit_price_not_current_product_price(): void
    {
        $user = $this->activeUser();
        $product = $this->productFor();

        // Order was placed at ₦1,000 but the product now costs ₦9,999.
        $order = $this->orderFor($user, ['subtotal' => 2000, 'total' => 2000]);
        $this->itemFor($order, $product, ['unit_price' => 1000, 'total_price' => 2000]);

        $product->update(['price' => 9999, 'discount_price' => null]);

        $this->actingAs($user)
            ->get(route('account.orders.show', $order))
            ->assertOk()
            ->assertSee('1,000.00')                       // historic unit price
            ->assertDontSee('9,999');                     // never recalculated
    }

    public function test_invoice_is_scoped_and_renders_recorded_totals(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['subtotal' => 2000, 'shipping_fee' => 500, 'total' => 2500]);
        $this->itemFor($order, $this->productFor());

        $this->actingAs($user)
            ->get(route('account.orders.invoice', $order))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('2,500.00');
    }

    // ---------------------------------------------------------------------
    // Status timeline
    // ---------------------------------------------------------------------

    public function test_order_detail_shows_timeline_reflecting_actual_status_history(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['status' => 'shipped']);
        $order->updateStatus('pending', 'Order placed.');
        $order->updateStatus('confirmed', 'Payment received.');
        $order->updateStatus('shipped', 'Handed to courier.');

        $response = $this->actingAs($user)->get(route('account.orders.show', $order));

        $response->assertOk()
            ->assertSee('Order Placed')
            ->assertSee('Shipped');

        // Every timeline stage must correspond to a real order status.
        $response->assertViewHas('timeline', function ($timeline) {
            return collect($timeline)->every(fn($s) => array_key_exists($s['key'], Order::STATUSES))
                && collect($timeline)->firstWhere('key', 'shipped')['done'] === true
                && collect($timeline)->firstWhere('key', 'delivered')['done'] === false;
        });
    }

    public function test_cancelled_order_timeline_short_circuits_to_terminal_state(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['status' => 'cancelled']);
        $order->updateStatus('pending', 'Order placed.');
        $order->updateStatus('cancelled', 'Changed my mind.');

        $this->actingAs($user)
            ->get(route('account.orders.show', $order))
            ->assertViewHas('timeline', function ($timeline) {
                $keys = collect($timeline)->pluck('key');
                return $keys->count() === 2 && $keys->last() === 'cancelled';
            });
    }

    // ---------------------------------------------------------------------
    // Actions
    // ---------------------------------------------------------------------

    public function test_customer_can_cancel_their_own_pending_order(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['status' => 'pending']);
        $this->itemFor($order, $this->productFor());

        $this->actingAs($user)
            ->post(route('account.orders.cancel', $order), ['reason' => 'Ordered by mistake'])
            ->assertRedirect();

        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_customer_cannot_cancel_an_order_that_is_already_shipped(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['status' => 'shipped']);

        $this->actingAs($user)
            ->post(route('account.orders.cancel', $order))
            ->assertRedirect();

        // Business rule: shipped orders are no longer cancellable.
        $this->assertSame('shipped', $order->fresh()->status);
    }

    public function test_customer_can_only_review_after_delivery(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['status' => 'pending']);
        $item = $this->itemFor($order, $this->productFor());

        $this->actingAs($user)
            ->post(route('account.orders.review', [$order, $item]), ['rating' => 5])
            ->assertRedirect();

        $this->assertDatabaseCount('marketplace_reviews', 0);
        $this->assertNull($item->fresh()->review_id);
    }

    public function test_customer_can_review_a_delivered_item(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['status' => 'delivered']);
        $item = $this->itemFor($order, $this->productFor());

        $this->actingAs($user)
            ->post(route('account.orders.review', [$order, $item]), [
                'rating' => 4,
                'title' => 'Good rice',
                'comment' => 'Arrived quickly and well packaged.',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('marketplace_reviews', 1);
        $this->assertNotNull($item->fresh()->review_id);
    }

    public function test_review_rating_is_validated(): void
    {
        $user = $this->activeUser();
        $order = $this->orderFor($user, ['status' => 'delivered']);
        $item = $this->itemFor($order, $this->productFor());

        $this->actingAs($user)
            ->post(route('account.orders.review', [$order, $item]), ['rating' => 99])
            ->assertSessionHasErrors('rating');
    }
}

<?php

namespace Tests\Feature\Account;

use App\Models\Marketplace\Order;
use App\Models\Marketplace\SavedProduct;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
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
            'order_number' => 'FC-TEST-' . uniqid(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 1000,
            'total' => 1000,
        ], $attributes));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_customer_can_access_dashboard(): void
    {
        $user = $this->activeUser();

        $this->actingAs($user)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSee('Total Orders')
            ->assertSee('Wishlist');
    }

    public function test_dashboard_shows_empty_state_when_customer_has_no_orders(): void
    {
        $user = $this->activeUser();

        $this->actingAs($user)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSee('No orders yet')
            ->assertSee('Start shopping to see your orders here.');
    }

    public function test_dashboard_counts_only_the_authenticated_customers_orders(): void
    {
        $me = $this->activeUser();
        $other = $this->activeUser();

        $this->orderFor($me, ['status' => 'delivered', 'payment_status' => 'paid']);
        $this->orderFor($me, ['status' => 'cancelled']);
        $this->orderFor($other, ['status' => 'delivered', 'payment_status' => 'paid']);

        $response = $this->actingAs($me)->get(route('account.dashboard'));

        $response->assertOk();

        // The other customer's delivered order must never be visible.
        $response->assertViewHas('recentOrders', function ($orders) use ($other) {
            return $orders->every(fn($o) => $o->user_id !== $other->id);
        });

        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 2
                && $stats['delivered'] === 1
                && $stats['cancelled'] === 1;
        });
    }

    public function test_lifetime_spend_excludes_cancelled_and_refunded_orders(): void
    {
        $user = $this->activeUser();

        $this->orderFor($user, ['status' => 'delivered', 'total' => 5000]);
        $this->orderFor($user, ['status' => 'cancelled', 'total' => 3000]);
        $this->orderFor($user, ['status' => 'refunded', 'total' => 2000]);

        $this->actingAs($user)
            ->get(route('account.dashboard'))
            ->assertViewHas('stats', fn($stats) => (float) $stats['lifetime_spend'] === 5000.0);
    }

    private function wishlistItem(User $user): SavedProduct
    {
        // No marketplace factory exists yet, so build the required rows explicitly.
        $vendor = \App\Models\Marketplace\Vendor::create([
            'user_id' => $user->id,
            'name' => 'Test Vendor',
            'slug' => 'test-vendor-'.uniqid(),
            'business_name' => 'Test Vendor Ltd',
            'status' => 'approved',
        ]);

        $category = \App\Models\Marketplace\Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category-'.uniqid(),
            'is_active' => true,
        ]);

        $product = \App\Models\Marketplace\Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product-'.uniqid(),
            'sku' => \App\Models\Marketplace\Product::generateSku(),
            'price' => 1500,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        return SavedProduct::create(['user_id' => $user->id, 'product_id' => $product->id]);
    }

    public function test_wishlist_and_unread_notification_counts_are_scoped_to_the_user(): void
    {
        $me = $this->activeUser();
        $other = $this->activeUser();

        $this->wishlistItem($me);
        $this->wishlistItem($other);

        Notification::create(['user_id' => $me->id, 'type' => 'order', 'title' => 'A', 'message' => 'm', 'is_read' => false]);
        Notification::create(['user_id' => $me->id, 'type' => 'order', 'title' => 'B', 'message' => 'm', 'is_read' => true]);
        Notification::create(['user_id' => $other->id, 'type' => 'order', 'title' => 'C', 'message' => 'm', 'is_read' => false]);

        $this->actingAs($me)
            ->get(route('account.dashboard'))
            ->assertViewHas('stats', function ($stats) {
                return $stats['wishlist'] === 1 && $stats['unread_notifications'] === 1;
            });
    }

    public function test_legacy_dashboard_route_redirects_to_the_new_customer_dashboard(): void
    {
        $user = $this->activeUser();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('account.dashboard'));
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\MembershipTier;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Store reporting. Month bucketing used to rely on DATE_FORMAT(), which is
 * MySQL-only and raised a 500 on SQLite, and the leaderboards were ranked by
 * product counts rather than by actual sales. These tests pin both fixes.
 */
class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    private function qaAdmin(): AdminUser
    {
        return AdminUser::create([
            'name' => 'QA Admin',
            'email' => 'qa-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_every_store_report_page_loads(): void
    {
        $admin = $this->qaAdmin();

        foreach (
            [
                'admin.reports.overview',
                'admin.reports.sales',
                'admin.reports.customers',
                'admin.reports.products',
                'admin.reports.vendors',
                'admin.reports.payouts',
            ] as $route
        ) {
            $this->actingAs($admin, 'admin')
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_the_legacy_marketplace_reports_url_still_serves_the_sales_report(): void
    {
        $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.marketplace.reports'))
            ->assertOk()
            ->assertSee('Sales Report', false);
    }

    public function test_retired_food_programme_reports_are_gone(): void
    {
        $admin = $this->qaAdmin();

        foreach (['users', 'payments', 'inventory', 'distributions'] as $path) {
            $this->actingAs($admin, 'admin')
                ->get("/admin/reports/$path")
                ->assertNotFound();
        }
    }

    public function test_reports_overview_revenue_excludes_cancelled_and_refunded_orders(): void
    {
        $this->seedStore();

        $html = $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.overview'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('103,000.00', $this->statTile($html, 'Lifetime Revenue'));
    }

    public function test_sales_report_excludes_voided_orders_from_gross_sales(): void
    {
        $this->seedStore();

        $html = $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.sales'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('103,000.00', $this->statTile($html, 'Gross Sales'));
    }

    public function test_earning_scope_keeps_every_status_except_cancelled_and_refunded(): void
    {
        $this->seedStore();

        $this->assertSame(2, Order::earning()->count());
        $this->assertSame(3, Order::count());
    }

    public function test_gross_sales_keeps_refunded_orders_so_gross_minus_refunds_is_net(): void
    {
        $this->seedStore();

        $refunded = $this->order(User::factory()->create(), 'refunded', 20000);
        $refunded->update(['refund_amount' => 20000]);

        $html = $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.sales'))
            ->assertOk()
            ->getContent();

        // 103,000 recognised plus the refunded order at its full 20,000 value,
        // from which the refund is then deducted exactly once.
        $this->assertStringContainsString('123,000.00', $this->statTile($html, 'Gross Sales'));
        $this->assertStringContainsString('20,000.00', $this->statTile($html, 'Refunds'));
        $this->assertStringContainsString('103,000.00', $this->statTile($html, 'Net Sales'));
    }

    public function test_first_time_buyers_exclude_anyone_who_ordered_before_the_window(): void
    {
        $this->seedStore();

        $returning = User::factory()->create(['first_name' => 'Return', 'last_name' => 'Shopper']);
        $this->order($returning, 'delivered', 5000, now()->subDays(60));
        $this->order($returning, 'delivered', 5000, now()->subDays(5));

        $firstTimer = User::factory()->create(['first_name' => 'First', 'last_name' => 'Timer']);
        $this->order($firstTimer, 'delivered', 5000, now()->subDays(3));

        $html = $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.customers'))
            ->assertOk()
            ->getContent();

        // "Big Spender" and "First Timer" are first-time; "Return Shopper" is not.
        $this->assertSame('2', $this->rowValue($html, 'First-time buyers in the last 30 days'));
    }

    public function test_sales_state_breakdown_excludes_voided_orders(): void
    {
        $this->seedStore();

        $voided = $this->order(User::factory()->create(), 'cancelled', 75000);
        $voided->update(['delivery_state' => 'Voided State']);

        $html = $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.sales'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('Voided State', $html);
    }

    public function test_vendor_commission_figures_report_commission_not_gross_payout(): void
    {
        $vendor = Vendor::create([
            'user_id' => User::factory()->create()->id,
            'name' => 'Commission Store',
            'slug' => 'commission-store-'.uniqid(),
            'business_name' => 'Commission Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        VendorPayout::create([
            'vendor_id' => $vendor->id,
            'reference' => 'PAY-'.uniqid(),
            'amount' => 90000,
            'commission_amount' => 9000,
            'status' => 'approved',
        ]);

        $html = $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.vendors'))
            ->assertOk()
            ->getContent();

        $owed = $this->statTile($html, 'Owed (unpaid payouts)');

        $this->assertStringContainsString('9,000.00', $owed);
        $this->assertStringNotContainsString('90,000.00', $owed);
    }

    public function test_sales_report_accepts_a_valid_range_and_falls_back_on_nonsense(): void
    {
        $admin = $this->qaAdmin();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.reports.sales', ['days' => 7]))
            ->assertOk();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.reports.sales', ['days' => 99999]))
            ->assertOk();
    }

    public function test_customers_report_ranks_buyers_by_marketplace_spend(): void
    {
        $this->seedStore();

        $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.customers'))
            ->assertOk()
            ->assertSee('Big Spender', false)
            ->assertSee('103,000.00', false);
    }

    public function test_category_leaderboard_is_ranked_by_revenue_not_product_count(): void
    {
        $this->seedStore();

        $html = $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.products'))
            ->assertOk()
            ->getContent();

        // "Premium Goods" holds a single product but earns 100,000, while
        // "Bulk Goods" holds three products that together earn 3,000.
        $premium = strpos($html, 'Premium Goods');
        $bulk = strpos($html, 'Bulk Goods');

        $this->assertNotFalse($premium, 'Premium Goods should appear in the category report.');
        $this->assertNotFalse($bulk, 'Bulk Goods should appear in the category report.');
        $this->assertLessThan(
            $bulk,
            $premium,
            'Category performance must be ordered by revenue, not product count.'
        );
    }

    public function test_reports_hub_links_to_every_store_report(): void
    {
        $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.reports.overview'))
            ->assertOk()
            ->assertSee(route('admin.reports.sales'), false)
            ->assertSee(route('admin.reports.customers'), false)
            ->assertSee(route('admin.reports.products'), false)
            ->assertSee(route('admin.reports.vendors'), false)
            ->assertSee(route('admin.reports.payouts'), false);
    }

    public function test_dashboard_revenue_agrees_with_the_earning_scope(): void
    {
        $this->seedStore();

        $html = $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('103,000.00', $this->statTile($html, 'Total Revenue'));
    }

    public function test_membership_tier_pricing_route_is_not_shadowed_by_the_resource_route(): void
    {
        $tier = MembershipTier::create([
            'name' => 'Bronze',
            'slug' => 'bronze',
            'annual_fee' => 5000,
        ]);

        $this->actingAs($this->qaAdmin(), 'admin')
            ->getJson(route('admin.membership-tiers.pricing', ['tier_id' => $tier->id]))
            ->assertOk()
            ->assertJsonPath('annual_subscription.amount', 5000);
    }

    /**
     * Read the value of a stat tile by its label. Asserting on the whole page
     * is unreliable because voided orders legitimately appear in status
     * breakdowns that are meant to be exhaustive.
     */
    private function statTile(string $html, string $label): string
    {
        preg_match(
            '/stat-label">'.preg_quote($label, '/').'[^<]*<\/p>\s*<p class="stat-value">([^<]*)<\/p>/',
            $html,
            $matches
        );

        $this->assertArrayHasKey(1, $matches, "No stat tile labelled \"{$label}\" was rendered.");

        return $matches[1];
    }

    /**
     * Two delivered orders totalling 103,000 and one cancelled order worth
     * 150,000. The cancelled order must never appear in sales or revenue
     * figures.
     */
    private function seedStore(): void
    {
        $vendorUser = User::factory()->create();
        $buyer = User::factory()->create(['first_name' => 'Big', 'last_name' => 'Spender']);

        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'name' => 'Report Store',
            'slug' => 'report-store-'.uniqid(),
            'business_name' => 'Report Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $premium = Category::create(['name' => 'Premium Goods', 'slug' => 'premium-'.uniqid()]);
        $bulk = Category::create(['name' => 'Bulk Goods', 'slug' => 'bulk-'.uniqid()]);

        $premiumProduct = $this->product($vendor, $premium, 'Premium Item', 50000);

        $bulkProducts = [
            $this->product($vendor, $bulk, 'Bulk Item One', 1000),
            $this->product($vendor, $bulk, 'Bulk Item Two', 1000),
            $this->product($vendor, $bulk, 'Bulk Item Three', 1000),
        ];

        $delivered = $this->order($buyer, 'delivered', 100000);
        $this->item($delivered, $premiumProduct, $vendor, 2, 50000, 100000);

        $bulkOrder = $this->order($buyer, 'delivered', 3000);
        foreach ($bulkProducts as $index => $bulkProduct) {
            $this->item($bulkOrder, $bulkProduct, $vendor, 1, 1000, 1000);
        }

        $this->order($buyer, 'cancelled', 150000);
    }

    private function product(Vendor $vendor, Category $category, string $name, float $price): Product
    {
        return Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str($name)->slug().'-'.uniqid(),
            'sku' => 'SKU-'.uniqid(),
            'price' => $price,
            'stock_quantity' => 100,
        ]);
    }

    private function item(Order $order, Product $product, Vendor $vendor, int $quantity, float $unitPrice, float $total): OrderItem
    {
        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'product_name' => $product->name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'total_price' => $total,
        ]);
    }

    private function order(User $user, string $status, float $total, ?Carbon $createdAt = null): Order
    {
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => Order::generateOrderNumber(),
            'status' => $status,
            'subtotal' => $total,
            'total' => $total,
            'payment_status' => 'paid',
        ]);

        if ($createdAt) {
            $order->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();
        }

        return $order;
    }

    /**
     * Read a value from a label/value table row.
     */
    private function rowValue(string $html, string $label): string
    {
        preg_match(
            '/'.preg_quote($label, '/').'<\/td>\s*<td[^>]*>([^<]*)</',
            $html,
            $matches
        );

        $this->assertArrayHasKey(1, $matches, "No table row labelled \"{$label}\" was rendered.");

        return trim($matches[1]);
    }
}

<?php

namespace Tests\Feature\Qa;

use App\Models\AdminUser;
use App\Models\Branch;
use App\Models\Department;
use App\Models\FoodItem;
use App\Models\FoodPackage;
use App\Models\MembershipTier;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\FlashSale;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use App\Models\PickupLocation;
use App\Models\Post;
use App\Models\Staff;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rendering smoke test for the admin area.
 *
 * Every admin GET route is requested as a super admin and the response is
 * checked for two classes of defect that a status code alone will not catch:
 * a PHP error leaking into the markup, and unrendered Blade/JS fragments.
 */
class AdminPageRenderTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    /** @var array<string, int|string> resolved route parameters */
    private array $ids = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::create([
            'name' => 'QA Admin',
            'email' => 'qa-render@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    /**
     * The leak patterns below are what a silently broken view looks like: the
     * request still returns 200 because the exception happened mid-render.
     */
    private const LEAKS = [
        'Whoops',
        'SQLSTATE',
        'Undefined variable',
        'Call to a member function',
        'Call to undefined method',
        'Too few arguments',
        'htmlspecialchars(): Argument',
        'ParseError',
    ];

    private function assertClean(string $page, string $html): void
    {
        foreach (self::LEAKS as $needle) {
            $this->assertStringNotContainsString(
                $needle,
                $html,
                "{$page} leaked \"{$needle}\" into the rendered page."
            );
        }

        // Unrendered template directives or stray PHP tags.
        $this->assertDoesNotMatchRegularExpression(
            '/@endsection|@extends\(|<\?php/',
            $html,
            "{$page} contains unrendered template markup."
        );

        // The layout must always be present, otherwise the page is a fragment.
        $this->assertStringContainsString('</html>', $html, "{$page} did not render the full layout.");
    }

    /**
     * Seed one record of everything the parameterised admin routes bind to, so
     * detail pages are exercised with real data rather than empty shells.
     */
    private function seedForDetailPages(): void
    {
        $buyer = $this->customer();

        $vendor = Vendor::create([
            'user_id' => $buyer->id,
            'name' => 'Render Store',
            'slug' => 'render-store-'.uniqid(),
            'business_name' => 'Render Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $category = Category::create([
            'name' => 'Render Cat',
            'slug' => 'render-cat-'.uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Render Rice',
            'slug' => 'render-rice-'.uniqid(),
            'sku' => Product::generateSku(),
            'price' => 5000,
            'stock_quantity' => 25,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'FC-'.uniqid(),
            'status' => 'delivered',
            'payment_status' => 'paid',
            'subtotal' => 5000,
            'total' => 5000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'product_name' => $product->name,
            'unit_price' => 5000,
            'quantity' => 1,
            'total_price' => 5000,
            'status' => 'delivered',
        ]);

        $tier = MembershipTier::create([
            'name' => 'Render Tier',
            'slug' => 'render-tier-'.uniqid(),
            'price' => 1000,
            'monthly_savings' => 50,
            'status' => 'active',
        ]);

        $foodItem = FoodItem::create([
            'name' => 'Render Maize',
            'slug' => 'render-maize-'.uniqid(),
            'unit' => 'kg',
            'stock_quantity' => 40,
        ]);

        $package = FoodPackage::create([
            'tier_id' => $tier->id,
            'name' => 'Render Package',
            'total_value' => 5000,
        ]);

        $branch = Branch::create(['name' => 'Render Branch', 'code' => 'RB'.uniqid(), 'status' => 'active']);

        $department = Department::create([
            'branch_id' => $branch->id,
            'name' => 'Render Dept',
            'code' => 'RD'.uniqid(),
            'status' => 'active',
        ]);

        $staff = Staff::create([
            'department_id' => $department->id,
            'name' => 'Render Staff',
            'email' => 'render-staff-'.uniqid().'@example.com',
            'staff_number' => 'ST'.uniqid(),
            'position' => 'Officer',
            'status' => 'active',
        ]);

        $this->ids = [
            'admin_user' => $this->admin->id,
            'adminUser' => $this->admin->id,
            'user' => $buyer->id,
            'vendor' => $vendor->id,
            'product' => $product->id,
            'order' => $order->id,
            'membershipTier' => $tier->id,
            'membership_tier' => $tier->id,
            'food_item' => $foodItem->id,
            'package' => $package->id,
            'branch' => $branch->id,
            'department' => $department->id,
            'flashSale' => FlashSale::create([
                'name' => 'Render Sale',
                'slug' => 'render-sale-'.uniqid(),
                'starts_at' => now(),
                'ends_at' => now()->addDay(),
                'is_active' => true,
            ])->id,
            'payout' => VendorPayout::create([
                'vendor_id' => $vendor->id,
                'reference' => 'PO-'.uniqid(),
                'amount' => 1000,
                'commission_amount' => 100,
                'status' => 'requested',
            ])->id,
            'location' => PickupLocation::create([
                'name' => 'Render Depot',
                'address' => '1 Audit Way',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'is_active' => true,
            ])->id,
            'ticket' => SupportTicket::create([
                'ticket_number' => 'TK'.uniqid(),
                'user_id' => $buyer->id,
                'subject' => 'Render enquiry',
                'message' => 'Seeded for the render audit.',
                'status' => 'open',
                'priority' => 'medium',
            ])->id,
            'staff' => $staff->id,
            'post' => Post::create([
                'title' => 'Render Audit Post',
                'category' => 'Shopping Tips',
                'excerpt' => 'Seeded so the post manager renders with real content.',
                'body' => 'A body paragraph for the render audit.',
                'status' => 'published',
                'published_at' => now()->subDay(),
            ])->id,
        ];
    }

    private function customer(): User
    {
        return User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);
    }

    public function test_every_index_level_admin_page_renders_without_defects(): void
    {
        $this->seedForDetailPages();

        $pages = [
            'admin.dashboard',
            'admin.settings.index',
            'admin.users.index',
            'admin.admin-users.index',
            'admin.membership-tiers.index',
            'admin.manual-payments.index',
            'admin.payments.index',
            'admin.distributions.index',
            'admin.inventory.index',
            'admin.inventory.low-stock',
            'admin.inventory.transactions',
            'admin.food-items.index',
            'admin.packages.index',
            'admin.food-support.index',
            'admin.food-assistance.index',
            'admin.food-assistance-tiers.index',
            'admin.food-loans.index',
            'admin.food-loans.eligibility-rules',
            'admin.vouchers.index',
            'admin.issuance.index',
            'admin.incidents.index',
            'admin.support.index',
            'admin.locations.index',
            'admin.branches.index',
            'admin.departments.index',
            'admin.staff.index',
            'admin.bank-accounts.index',
            'admin.page-contents.index',
    'admin.posts.index',
    'admin.posts.create',
            'admin.marketplace.dashboard',
            'admin.marketplace.orders.index',
            'admin.marketplace.products.index',
            'admin.marketplace.vendors.index',
            'admin.marketplace.payouts.index',
            'admin.marketplace.audit-logs.index',
            'admin.marketplace.reviews.index',
            'admin.marketplace.bulk-orders.index',
            'admin.marketplace.wallet-transactions.index',
            'admin.marketplace.delivery-zones.index',
            'admin.marketplace.categories.index',
            'admin.marketplace.brands.index',
            'admin.marketplace.coupons.index',
            'admin.marketplace.flash-sales.index',
            'admin.reports.overview',
            'admin.reports.sales',
            'admin.reports.customers',
            'admin.reports.products',
            'admin.reports.vendors',
            'admin.reports.payouts',
        ];

        foreach ($pages as $route) {
            $response = $this->actingAs($this->admin, 'admin')->get(route($route));

            $response->assertOk();
            $this->assertClean($route, $response->getContent());
        }
    }

    public function test_every_parameterised_admin_detail_page_renders_without_defects(): void
    {
        $this->seedForDetailPages();

        $routes = [
            'admin.admin-users.show',
            'admin.users.show',
            'admin.users.referrals',
            'admin.membership-tiers.show',
            'admin.membership-tiers.members',
            'admin.membership-tiers.unions',
            'admin.marketplace.orders.show',
            'admin.marketplace.products.show',
            'admin.marketplace.vendors.show',
            'admin.marketplace.payouts.show',
            'admin.marketplace.flash-sales.show',
            'admin.departments.show',
            'admin.staff.show',
            'admin.food-items.show',
            'admin.packages.show',
            'admin.support.show',
            'admin.posts.edit',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin, 'admin')->get(route($route, $this->ids));

            $response->assertOk();
            $this->assertClean($route, $response->getContent());
        }
    }

    /**
     * The per-branch department picker is consumed by fetch() rather than a
     * full page load, so it is asserted as JSON instead of HTML.
     */
    public function test_the_branch_department_endpoint_returns_json(): void
    {
        $this->seedForDetailPages();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.departments.by-branch', ['branch' => $this->ids['branch']]))
            ->assertOk()
            ->assertJsonStructure(['*' => ['id', 'name']]);
    }

    /**
     * The report range buttons must all be reachable and must actually change
     * the query window, not just the highlighted button.
     */
    public function test_every_report_range_returns_its_own_figures(): void
    {
        $this->seedForDetailPages();

        $admin = $this->admin;

        foreach ([7, 30, 90, 365] as $days) {
            $this->actingAs($admin, 'admin')
                ->get(route('admin.reports.sales', ['days' => $days]))
                ->assertOk()
                ->assertSee('Gross Sales ('.$days.'d)', false);
        }

        $this->actingAs($admin, 'admin')
            ->get(route('admin.reports.sales', ['days' => 45]))
            ->assertOk()
            ->assertSee('Gross Sales (30d)', false);
    }
}

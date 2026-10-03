<?php

namespace Tests\Feature\Admin;

use App\Models\AdminAuditLog;
use App\Models\AdminUser;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Product approval workflow: vendor-created products start pending and stay
 * hidden from the storefront until an admin approves (or rejects) them.
 */
class AdminProductApprovalTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    private User $vendorUser;

    private Vendor $vendor;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::create([
            'name' => 'Operations Admin',
            'email' => 'approval-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->vendorUser = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $this->vendor = Vendor::create([
            'user_id' => $this->vendorUser->id,
            'name' => 'Approval Store',
            'slug' => 'approval-store-'.uniqid(),
            'business_name' => 'Approval Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $this->category = Category::create([
            'name' => 'Groceries',
            'slug' => 'groceries',
            'is_active' => true,
        ]);
    }

    private function createVendorProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'vendor_id' => $this->vendor->id,
            'category_id' => $this->category->id,
            'name' => 'Vendor Rice 2026',
            'slug' => 'vendor-rice-'.uniqid(),
            'sku' => Product::generateSku(),
            'price' => 4500,
            'stock_quantity' => 100,
            'is_active' => true,
            'approval_status' => 'pending',
        ], $overrides));
    }

    private function createProductViaVendorStore(array $params = []): Product
    {
        $this->actingAs($this->vendorUser)
            ->post(route('vendor.products.store'), array_merge([
                'name' => 'New Vendor Product',
                'category_id' => $this->category->id,
                'price' => 3000,
                'stock_quantity' => 20,
            ], $params))
            ->assertRedirect(route('vendor.products.index'))
            ->assertSessionHas('success');

        return Product::where('vendor_id', $this->vendor->id)->where('name', 'New Vendor Product')->firstOrFail();
    }

    private function actingAsAdmin()
    {
        return $this->actingAs($this->admin, 'admin');
    }

    public function test_vendor_created_product_starts_pending_approval(): void
    {
        $product = $this->createProductViaVendorStore();

        $this->assertTrue($product->isPendingApproval());
        $this->assertSame('pending', $product->approval_status);
        $this->assertTrue((bool) $product->is_active);
    }

    public function test_pending_product_is_hidden_from_the_storefront_and_cart(): void
    {
        $product = $this->createVendorProduct();

        $this->assertFalse(Product::active()->whereKey($product->id)->exists());

        $this->actingAs($this->vendorUser)
            ->get(route('marketplace.product', $product->slug))
            ->assertNotFound();
    }

    public function test_admin_approval_makes_the_product_live(): void
    {
        $product = $this->createVendorProduct();

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.products.approve', $product))
            ->assertRedirect()
            ->assertSessionHas('success');

        $product->refresh();
        $this->assertTrue($product->isApproved());
        $this->assertNotNull($product->approved_at);
        $this->assertTrue(Product::active()->whereKey($product->id)->exists());

        $this->actingAs($this->vendorUser)
            ->get(route('marketplace.product', $product->slug))
            ->assertOk();
    }

    public function test_admin_rejection_records_a_reason_and_keeps_it_hidden(): void
    {
        $product = $this->createVendorProduct();

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.products.reject', $product), [
                'rejection_reason' => 'Incomplete labelling',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $product->refresh();
        $this->assertTrue($product->isRejected());
        $this->assertSame('Incomplete labelling', $product->rejection_reason);
        $this->assertFalse(Product::active()->whereKey($product->id)->exists());
    }

    public function test_approve_and_reject_actions_are_audit_logged(): void
    {
        $product = $this->createVendorProduct();

        $this->actingAsAdmin()->post(route('admin.marketplace.products.approve', $product));
        $this->actingAsAdmin()->post(route('admin.marketplace.products.reject', $product), ['rejection_reason' => 'Testing']);

        $this->assertDatabaseHas('admin_audit_logs', [
            'admin_user_id' => $this->admin->id,
            'subject_id' => $product->id,
            'action' => 'product.approve',
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'admin_user_id' => $this->admin->id,
            'subject_id' => $product->id,
            'action' => 'product.reject',
        ]);
    }

    public function test_audit_log_index_lists_entries(): void
    {
        $product = $this->createVendorProduct();
        $this->actingAsAdmin()->post(route('admin.marketplace.products.approve', $product));

        $this->actingAsAdmin()
            ->get(route('admin.marketplace.audit-logs.index'))
            ->assertOk()
            ->assertSee('product.approve');
    }

    public function test_vendor_editing_a_rejected_product_resubmits_for_review(): void
    {
        $product = $this->createVendorProduct(['approval_status' => 'rejected', 'rejection_reason' => 'Fix photos']);

        $this->actingAs($this->vendorUser)
            ->put(route('vendor.products.update', $product), [
                'name' => 'Vendor Rice 2026',
                'category_id' => $this->category->id,
                'price' => 4600,
                'stock_quantity' => 90,
            ])
            ->assertRedirect(route('vendor.products.index'));

        $product->refresh();
        $this->assertTrue($product->isPendingApproval());
        $this->assertNull($product->rejection_reason);
    }

    public function test_admin_products_list_can_filter_to_pending_approval(): void
    {
        $pending = $this->createVendorProduct();
        $approved = $this->createVendorProduct(['approval_status' => 'approved']);

        $this->actingAsAdmin()
            ->get(route('admin.marketplace.products.index', ['approval' => 'pending']))
            ->assertOk()
            ->assertSee($pending->sku);

        $this->actingAsAdmin()
            ->get(route('admin.marketplace.products.index', ['approval' => 'approved']))
            ->assertOk()
            ->assertSee($approved->sku);
    }
}
<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression coverage for the admin layout crash: a non-super-admin reaching
 * any admin page used to 500 because the sidebar section flags were only
 * defined inside role-gated @if blocks. Every role must be able to render the
 * shared layout, and role-scoped routes must stay gated for everyone else.
 */
class AdminRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $role): AdminUser
    {
        return AdminUser::create([
            'name' => ucfirst(str_replace('_', ' ', $role)),
            'email' => $role.'@example.com',
            'password' => bcrypt('password'),
            'role' => $role,
            'is_active' => true,
        ]);
    }

    public function test_every_admin_role_can_render_the_shared_dashboard(): void
    {
        foreach (['super_admin', 'inventory_manager', 'customer_support'] as $role) {
            $this->actingAs($this->makeAdmin($role), 'admin')
                ->get(route('admin.dashboard'))
                ->assertOk();
        }
    }

    public function test_non_super_admin_roles_can_render_marketplace_management_pages(): void
    {
        // The marketplace admin section renders the shared layout for every
        // role that reaches it; the 403 gate itself is validated below.
        $admin = $this->makeAdmin('super_admin');

        foreach (['admin.marketplace.dashboard', 'admin.marketplace.products.index', 'admin.marketplace.vendors.index'] as $route) {
            $this->actingAs($admin, 'admin')->get(route($route))->assertOk();
        }
    }

    public function test_marketplace_payouts_are_super_admin_only(): void
    {
        foreach (['inventory_manager', 'customer_support'] as $role) {
            $this->actingAs($this->makeAdmin($role), 'admin')
                ->get(route('admin.marketplace.payouts.index'))
                ->assertForbidden();
        }
    }

    public function test_operations_pages_require_super_admin_or_inventory_manager(): void
    {
        $this->actingAs($this->makeAdmin('inventory_manager'), 'admin')
            ->get(route('admin.inventory.index'))
            ->assertOk();

        $this->actingAs($this->makeAdmin('customer_support'), 'admin')
            ->get(route('admin.inventory.index'))
            ->assertForbidden();
    }

    public function test_support_pages_require_super_admin_or_customer_support(): void
    {
        $this->actingAs($this->makeAdmin('customer_support'), 'admin')
            ->get(route('admin.support.index'))
            ->assertOk();

        $this->actingAs($this->makeAdmin('inventory_manager'), 'admin')
            ->get(route('admin.support.index'))
            ->assertForbidden();
    }
}
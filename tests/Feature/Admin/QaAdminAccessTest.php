<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies the QA admin account produced by storage/app/qa-make-admin.php can
 * actually sign in through the admin guard and reach the admin dashboards.
 *
 * The account is created here with the same attributes the script uses, so the
 * test documents and pins the contract of that script.
 */
class QaAdminAccessTest extends TestCase
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

    public function test_qa_admin_can_log_in_through_the_admin_guard(): void
    {
        $this->qaAdmin();

        $response = $this->post(route('admin.login.post'), [
            'email' => 'qa-admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(
            AdminUser::where('email', 'qa-admin@example.com')->first(),
            'admin'
        );
    }

    public function test_qa_admin_can_reach_the_admin_dashboard(): void
    {
        $this->actingAs($this->qaAdmin(), 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_qa_admin_can_reach_the_marketplace_admin_sections(): void
    {
        $admin = $this->qaAdmin();

        foreach (
            [
                'admin.marketplace.dashboard',
                'admin.marketplace.products.index',
                'admin.marketplace.vendors.index',
                'admin.marketplace.orders.index',
                'admin.marketplace.categories.index',
                'admin.marketplace.reports',
            ] as $route
        ) {
            $this->actingAs($admin, 'admin')
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_inactive_qa_admin_is_logged_out_and_cannot_reach_dashboards(): void
    {
        $admin = $this->qaAdmin();
        $admin->update(['is_active' => false]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }
}

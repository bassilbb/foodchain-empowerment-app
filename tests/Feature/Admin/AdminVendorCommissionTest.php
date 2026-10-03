<?php

namespace Tests\Feature\Admin;

use App\Models\AdminAuditLog;
use App\Models\AdminUser;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admins may configure the commission rate applied to a vendor's earnings.
 */
class AdminVendorCommissionTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::create([
            'name' => 'Commissions Admin',
            'email' => 'commission-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->vendor = Vendor::create([
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'name' => 'Commission Store',
            'slug' => 'commission-store-'.uniqid(),
            'business_name' => 'Commission Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);
    }

    private function actingAsAdmin()
    {
        return $this->actingAs($this->admin, 'admin');
    }

    public function test_admin_can_update_a_vendors_commission_rate(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.marketplace.vendors.commission', $this->vendor), [
                'commission_rate' => 7.5,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(7.5, (float) $this->vendor->fresh()->commission_rate);
    }

    public function test_commission_rate_must_be_within_0_and_100(): void
    {
        foreach ([101, -5, 'abc'] as $invalid) {
            $this->actingAsAdmin()
                ->post(route('admin.marketplace.vendors.commission', $this->vendor), [
                    'commission_rate' => $invalid,
                ])
                ->assertSessionHasErrors('commission_rate');
        }

        $this->assertSame(10.0, (float) $this->vendor->fresh()->commission_rate);
    }

    public function test_zero_commission_is_allowed(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.marketplace.vendors.commission', $this->vendor), [
                'commission_rate' => 0,
            ])
            ->assertRedirect();

        $this->assertSame(0.0, (float) $this->vendor->fresh()->commission_rate);
    }

    public function test_commission_change_is_audit_logged(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.marketplace.vendors.commission', $this->vendor), [
                'commission_rate' => 5,
            ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'admin_user_id' => $this->admin->id,
            'subject_id' => $this->vendor->id,
            'action' => 'vendor.commission',
        ]);

        $log = AdminAuditLog::where('action', 'vendor.commission')->first();
        $this->assertSame(5, (int) $log->changes['commission_rate']);
        $this->assertStringContainsString('Commission rate changed from 10.00% to 5.00%.', $log->description);
    }
}
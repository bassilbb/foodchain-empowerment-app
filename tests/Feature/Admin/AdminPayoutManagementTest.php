<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPayoutManagementTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::create([
            'name' => 'Super Admin',
            'email' => 'payout-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['is_active' => true]);

        $this->vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => 'Payout Store',
            'slug' => 'payout-store-'.uniqid(),
            'business_name' => 'Payout Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);
    }

    private function payout(string $status = 'requested', array $overrides = []): VendorPayout
    {
        return VendorPayout::create(array_merge([
            'vendor_id' => $this->vendor->id,
            'reference' => VendorPayout::generateReference(),
            'amount' => 20000,
            'gross_sales' => 22000,
            'commission_amount' => 2000,
            'commission_rate' => 10,
            'status' => $status,
            'requested_at' => now(),
        ], $overrides));
    }

    private function actingAsAdmin()
    {
        return $this->actingAs($this->admin, 'admin');
    }

    public function test_admin_can_view_the_payout_list(): void
    {
        $payout = $this->payout();

        $this->actingAsAdmin()
            ->get(route('admin.marketplace.payouts.index'))
            ->assertOk()
            ->assertSee($payout->reference);
    }

    public function test_payout_list_lists_payouts_from_every_vendor(): void
    {
        $otherUser = User::factory()->create(['is_active' => true]);
        $otherVendor = Vendor::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Store',
            'slug' => 'other-store-'.uniqid(),
            'business_name' => 'Other Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);
        $theirs = VendorPayout::create([
            'vendor_id' => $otherVendor->id,
            'reference' => VendorPayout::generateReference(),
            'amount' => 5000,
            'status' => 'requested',
            'requested_at' => now(),
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.marketplace.payouts.index'))
            ->assertOk()
            ->assertSee($theirs->reference)
            ->assertSee('Other Business');
    }

    public function test_admin_can_open_a_payout_detail_page(): void
    {
        $payout = $this->payout();

        $this->actingAsAdmin()
            ->get(route('admin.marketplace.payouts.show', $payout))
            ->assertOk()
            ->assertSee($payout->reference);
    }

    public function test_requested_payout_can_be_approved(): void
    {
        $payout = $this->payout('requested');

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.approve', $payout))
            ->assertRedirect();

        $payout->refresh();
        $this->assertSame('approved', $payout->status);
        $this->assertSame(AdminUser::class, $payout->processed_by_type);
        $this->assertSame($this->admin->id, $payout->processed_by_id);
        $this->assertNotNull($payout->processed_at);
    }

    public function test_only_requested_payouts_can_be_approved(): void
    {
        $payout = $this->payout('paid', ['paid_at' => now()]);

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.approve', $payout))
            ->assertStatus(422);

        $this->assertSame('paid', $payout->fresh()->status);
    }

    public function test_approved_payout_can_be_marked_as_processing(): void
    {
        $payout = $this->payout('approved');

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.process', $payout))
            ->assertRedirect();

        $this->assertSame('processing', $payout->fresh()->status);
    }

    public function test_processing_only_allowed_from_approved(): void
    {
        $payout = $this->payout('requested');

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.process', $payout))
            ->assertStatus(422);

        $this->assertSame('requested', $payout->fresh()->status);
    }

    public function test_approved_payout_can_be_marked_paid(): void
    {
        $payout = $this->payout('approved');

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.mark-paid', $payout))
            ->assertRedirect();

        $payout->refresh();
        $this->assertSame('paid', $payout->status);
        $this->assertNotNull($payout->paid_at);
    }

    public function test_processing_payout_can_be_marked_paid(): void
    {
        $payout = $this->payout('processing');

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.mark-paid', $payout))
            ->assertRedirect();

        $this->assertSame('paid', $payout->fresh()->status);
    }

    public function test_requested_payout_cannot_be_marked_paid_directly(): void
    {
        $payout = $this->payout('requested');

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.mark-paid', $payout))
            ->assertStatus(422);

        $this->assertSame('requested', $payout->fresh()->status);
    }

    public function test_rejecting_requires_a_reason(): void
    {
        $payout = $this->payout('requested');

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.reject', $payout))
            ->assertSessionHasErrors('rejection_reason');

        $this->assertSame('requested', $payout->fresh()->status);
    }

    public function test_requested_payout_can_be_rejected_with_a_reason(): void
    {
        $payout = $this->payout('requested');

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.reject', $payout), [
                'rejection_reason' => 'Bank details could not be verified.',
            ])
            ->assertRedirect();

        $payout->refresh();
        $this->assertSame('rejected', $payout->status);
        $this->assertSame('Bank details could not be verified.', $payout->rejection_reason);
        $this->assertSame($this->admin->id, $payout->processed_by_id);
    }

    public function test_a_paid_payout_cannot_be_rejected(): void
    {
        $payout = $this->payout('paid', ['paid_at' => now()]);

        $this->actingAsAdmin()
            ->post(route('admin.marketplace.payouts.reject', $payout), [
                'rejection_reason' => 'Too late now.',
            ])
            ->assertStatus(422);

        $this->assertSame('paid', $payout->fresh()->status);
    }

    public function test_payout_list_can_be_filtered_by_status(): void
    {
        $this->payout('requested');
        $paid = $this->payout('paid', ['paid_at' => now()]);

        $this->actingAsAdmin()
            ->get(route('admin.marketplace.payouts.index', ['status' => 'paid']))
            ->assertOk()
            ->assertSee($paid->reference);
    }
}
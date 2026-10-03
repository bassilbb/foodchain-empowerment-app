<?php

namespace Tests\Feature\Vendor;

use App\Models\Marketplace\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Vendor Centre approval gating: only vendors whose record has been approved
 * by an administrator may use the centre. Pending vendors are redirected to
 * their account dashboard; suspended (or otherwise inactive) vendors are
 * blocked entirely.
 */
class VendorApprovalGateTest extends TestCase
{
    use RefreshDatabase;

    private function makeVendorUser(string $status): User
    {
        $user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        Vendor::create([
            'user_id' => $user->id,
            'name' => 'Store '.uniqid(),
            'slug' => 'store-'.uniqid(),
            'business_name' => 'Business '.uniqid(),
            'status' => $status,
            'commission_rate' => 10,
        ]);

        return $user;
    }

    public function test_pending_vendor_is_redirected_to_their_account_dashboard(): void
    {
        $user = $this->makeVendorUser('pending');

        $this->actingAs($user)
            ->get(route('vendor.dashboard'))
            ->assertRedirect(route('account.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_suspended_vendor_is_blocked_from_the_vendor_centre(): void
    {
        $user = $this->makeVendorUser('suspended');

        $this->actingAs($user)
            ->get(route('vendor.dashboard'))
            ->assertForbidden();
    }

    public function test_pending_vendor_cannot_reach_any_vendor_route(): void
    {
        $user = $this->makeVendorUser('pending');

        foreach (['vendor.products.index', 'vendor.orders.index', 'vendor.payouts.index'] as $route) {
            $this->actingAs($user)->get(route($route))->assertRedirect(route('account.dashboard'));
        }
    }

    public function test_approved_vendor_can_reach_the_vendor_centre(): void
    {
        $user = $this->makeVendorUser('approved');

        $this->actingAs($user)
            ->get(route('vendor.dashboard'))
            ->assertOk();
    }
}
<?php

namespace Tests\Feature\Account;

use App\Models\Marketplace\Vendor;
use App\Models\NigerianState;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Become-a-vendor application flow for members who have no vendor record.
 */
class BecomeVendorTest extends TestCase
{
    use RefreshDatabase;

    private function member(): User
    {
        return User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);
    }

    private function submitApplication(User $user, array $overrides = [])
    {
        return $this->actingAs($user)->post(route('account.become-vendor.store'), array_merge([
            'name' => 'Jane Doe',
            'business_name' => 'Jane Fresh Foods',
            'email' => 'jane@example.com',
            'phone' => '08012345678',
            'description' => 'Farm-fresh produce delivery.',
            'address' => '12 Health Street, Abuja',
            'city' => 'Garki',
        ], $overrides));
    }

    public function test_member_without_vendor_can_view_the_application_form(): void
    {
        $this->actingAs($this->member())
            ->get(route('account.become-vendor.create'))
            ->assertOk();
    }

    public function test_member_can_submit_a_vendor_application(): void
    {
        $user = $this->member();

        $this->submitApplication($user)
            ->assertRedirect(route('account.dashboard'))
            ->assertSessionHas('success');

        $vendor = Vendor::where('user_id', $user->id)->first();

        $this->assertNotNull($vendor);
        $this->assertSame('pending', $vendor->status);
        $this->assertSame('Jane Fresh Foods', $vendor->business_name);
        $this->assertSame('Jane Doe', $vendor->name);
        $this->assertSame('jane@example.com', $vendor->email);
        $this->assertSame('12 Health Street, Abuja', $vendor->address);
        $this->assertSame(10.0, (float) $vendor->commission_rate);
        $this->assertSame(0, (int) $vendor->wallet_balance);
    }

    public function test_application_records_the_selected_state(): void
    {
        $state = NigerianState::create([
            'name' => 'FCT Abuja',
            'is_active' => true,
        ]);

        $this->submitApplication($this->member(), ['state_id' => $state->id]);

        $vendor = Vendor::first();
        $this->assertSame('FCT Abuja', $vendor->state);
    }

    public function test_application_requires_core_details(): void
    {
        $this->submitApplication($this->member(), [
            'name' => '',
            'business_name' => '',
            'email' => 'not-an-email',
            'phone' => '',
        ])->assertSessionHasErrors(['name', 'business_name', 'email', 'phone']);

        $this->assertSame(0, Vendor::count());
    }

    public function test_member_cannot_apply_twice(): void
    {
        $user = $this->member();
        $this->submitApplication($user);

        $this->submitApplication($user)
            ->assertSessionHas('error');

        $this->assertSame(1, Vendor::where('user_id', $user->id)->count());
    }

    public function test_pending_applicant_is_redirected_away_from_the_form(): void
    {
        $user = $this->member();
        $this->submitApplication($user);

        $this->actingAs($user)
            ->get(route('account.become-vendor.create'))
            ->assertRedirect(route('account.dashboard'))
            ->assertSessionHas('info');
    }

    public function test_approved_vendor_is_redirected_to_their_dashboard(): void
    {
        $user = $this->member();

        Vendor::create([
            'user_id' => $user->id,
            'name' => 'Store',
            'slug' => 'store-'.uniqid(),
            'business_name' => 'Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $this->actingAs($user)
            ->get(route('account.become-vendor.create'))
            ->assertRedirect(route('vendor.dashboard'))
            ->assertSessionHas('info');
    }
}
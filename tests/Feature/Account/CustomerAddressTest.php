<?php

namespace Tests\Feature\Account;

use App\Models\NigerianState;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAddressTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // The address form validates against the same `nigerian_states` table
        // that drives state-based pricing, so the table must be populated in
        // tests exactly as it is in production.
        $this->seed(\Database\Seeders\NigerianStateSeeder::class);

        $this->user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);
    }

    private function stateName(): string
    {
        $name = NigerianState::active()->ordered()->value('name');

        $this->assertNotNull($name, 'NigerianStateSeeder must provide at least one active state.');

        return $name;
    }

    private function addressData(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Ada Obi',
            'phone' => '08012345678',
            'address' => '12 Market Road',
            'state' => $this->stateName(),
            'lga' => 'Ikeja',
            'city' => 'Ikeja',
            'landmark' => 'Beside the bank',
            'delivery_instructions' => 'Call on arrival',
        ], $overrides);
    }

    private function makeAddress(User $user, array $overrides = []): UserAddress
    {
        return UserAddress::create(array_merge([
            'user_id' => $user->id,
            'full_name' => 'Existing Person',
            'phone' => '08099999999',
            'address' => '1 Existing Street',
            'state' => $this->stateName(),
        ], $overrides));
    }

    // ---------------------------------------------------------------- access

    public function test_guest_cannot_access_address_book(): void
    {
        $this->get(route('account.addresses.index'))->assertRedirect(route('login'));
    }

    public function test_customer_can_view_the_address_book(): void
    {
        $this->actingAs($this->user)
            ->get(route('account.addresses.index'))
            ->assertOk()
            ->assertSee('Delivery Addresses');
    }

    public function test_empty_state_is_shown_when_there_are_no_addresses(): void
    {
        $this->actingAs($this->user)
            ->get(route('account.addresses.index'))
            ->assertOk()
            ->assertSee('No delivery addresses yet');
    }

    // --------------------------------------------------------------- create

    public function test_customer_can_create_an_address(): void
    {
        $this->actingAs($this->user)
            ->post(route('account.addresses.store'), $this->addressData())
            ->assertRedirect(route('account.addresses.index'));

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $this->user->id,
            'full_name' => 'Ada Obi',
        ]);
    }

    public function test_first_address_is_automatically_the_default(): void
    {
        $this->actingAs($this->user)
            ->post(route('account.addresses.store'), $this->addressData());

        $address = UserAddress::where('user_id', $this->user->id)->first();
        $this->assertTrue((bool) $address->is_default);
    }

    public function test_address_creation_is_rejected_with_an_invalid_state(): void
    {
        $this->actingAs($this->user)
            ->post(route('account.addresses.store'), $this->addressData(['state' => 'Not A Real State']))
            ->assertSessionHasErrors('state');

        $this->assertDatabaseCount('user_addresses', 0);
    }

    public function test_required_address_fields_are_validated(): void
    {
        $this->actingAs($this->user)
            ->post(route('account.addresses.store'), [])
            ->assertSessionHasErrors(['full_name', 'phone', 'address', 'state']);
    }

    // --------------------------------------------------------------- update

    public function test_customer_can_update_their_own_address(): void
    {
        $address = $this->makeAddress($this->user);

        $this->actingAs($this->user)
            ->put(route('account.addresses.update', $address), $this->addressData(['full_name' => 'Renamed']))
            ->assertRedirect(route('account.addresses.index'));

        $this->assertSame('Renamed', $address->fresh()->full_name);
    }

    public function test_only_one_address_can_be_default_at_a_time(): void
    {
        $first = $this->makeAddress($this->user);
        $first->makeDefault();

        $second = $this->makeAddress($this->user, ['full_name' => 'Second']);

        $this->actingAs($this->user)
            ->post(route('account.addresses.default', $second))
            ->assertRedirect();

        $this->assertSame(1, UserAddress::where('user_id', $this->user->id)->where('is_default', true)->count());
        $this->assertFalse((bool) $first->fresh()->is_default);
        $this->assertTrue((bool) $second->fresh()->is_default);
    }

    // --------------------------------------------------------------- delete

    public function test_customer_can_delete_their_own_address(): void
    {
        $address = $this->makeAddress($this->user);

        $this->actingAs($this->user)
            ->delete(route('account.addresses.destroy', $address))
            ->assertRedirect(route('account.addresses.index'));

        $this->assertDatabaseMissing('user_addresses', ['id' => $address->id]);
    }

    public function test_deleting_the_default_promotes_another_address(): void
    {
        $default = $this->makeAddress($this->user);
        $default->makeDefault();

        $spare = $this->makeAddress($this->user, ['full_name' => 'Spare']);

        $this->actingAs($this->user)
            ->delete(route('account.addresses.destroy', $default));

        $this->assertTrue(
            (bool) UserAddress::where('user_id', $this->user->id)->where('is_default', true)->exists(),
            'A default address must always remain so checkout has a preselected option.'
        );
    }

    // ----------------------------------------------------------- IDOR guard

    public function test_customer_cannot_view_another_customers_address_for_edit(): void
    {
        $other = User::factory()->create(['is_active' => true]);
        $theirs = $this->makeAddress($other);

        $this->actingAs($this->user)
            ->get(route('account.addresses.edit', $theirs))
            ->assertNotFound();
    }

    public function test_customer_cannot_update_another_customers_address(): void
    {
        $other = User::factory()->create(['is_active' => true]);
        $theirs = $this->makeAddress($other);

        $this->actingAs($this->user)
            ->put(route('account.addresses.update', $theirs), $this->addressData(['full_name' => 'Hacked']))
            ->assertNotFound();

        $this->assertSame('Existing Person', $theirs->fresh()->full_name);
    }

    public function test_customer_cannot_delete_another_customers_address(): void
    {
        $other = User::factory()->create(['is_active' => true]);
        $theirs = $this->makeAddress($other);

        $this->actingAs($this->user)
            ->delete(route('account.addresses.destroy', $theirs))
            ->assertNotFound();

        $this->assertDatabaseHas('user_addresses', ['id' => $theirs->id]);
    }

    public function test_customer_cannot_set_another_customers_address_as_default(): void
    {
        $other = User::factory()->create(['is_active' => true]);
        $theirs = $this->makeAddress($other);

        $this->actingAs($this->user)
            ->post(route('account.addresses.default', $theirs))
            ->assertNotFound();
    }

    // ---------------------------------------------- checkout integration

    public function test_delivery_snapshot_matches_the_checkout_column_contract(): void
    {
        $address = $this->makeAddress($this->user, [
            'address' => '5 Broad Street',
            'landmark' => 'Opposite the park',
            'city' => 'Ikeja',
            'lga' => 'Ikeja',
            'delivery_instructions' => 'Leave with security',
        ]);

        $snapshot = $address->toDeliverySnapshot();

        // These exact keys are what CheckoutController reads to build the order.
        $this->assertSame(
            ['delivery_address', 'delivery_city', 'delivery_state', 'delivery_lga', 'delivery_notes'],
            array_keys($snapshot)
        );

        $this->assertStringContainsString('5 Broad Street', $snapshot['delivery_address']);
        $this->assertSame('Ikeja', $snapshot['delivery_city']);
        $this->assertSame('Leave with security', $snapshot['delivery_notes']);
    }

    public function test_address_book_does_not_alter_storefront_pricing_state(): void
    {
        // Saving an address must not silently switch the state used for prices:
        // storefront pricing is session-driven via MarketplacePricingService.
        $sessionState = 'Lagos';
        $addressState = NigerianState::active()->ordered()
            ->where('name', '!=', $sessionState)
            ->value('name');

        if (! $addressState) {
            $this->markTestSkipped('Only one active state available.');
        }

        $this->withSession(['marketplace_state' => $sessionState])
            ->actingAs($this->user)
            ->post(route('account.addresses.store'), $this->addressData(['state' => $addressState]))
            ->assertRedirect();

        $this->assertSame($sessionState, session('marketplace_state'));
    }
}

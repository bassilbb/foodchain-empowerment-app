<?php

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\NigerianStateSeeder::class);

        $this->user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
            'password' => Hash::make('old-password'),
        ]);
    }

    public function test_guest_cannot_view_the_profile(): void
    {
        $this->get(route('account.profile.show'))->assertRedirect(route('login'));
    }

    public function test_customer_can_view_their_profile(): void
    {
        $this->actingAs($this->user)
            ->get(route('account.profile.show'))
            ->assertOk()
            ->assertSee($this->user->email)
            ->assertSee('Personal Information')
            ->assertSee('Change Password');
    }

    public function test_customer_can_update_their_profile(): void
    {
        $this->actingAs($this->user)
            ->put(route('account.profile.update'), [
                'first_name' => 'Chioma',
                'last_name' => 'Okafor',
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'city' => 'Enugu',
            ])
            ->assertRedirect();

        $fresh = $this->user->fresh();
        $this->assertSame('Chioma', $fresh->first_name);
        $this->assertSame('Enugu', $fresh->city);
    }

    public function test_customer_cannot_take_another_customers_email(): void
    {
        $other = User::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->put(route('account.profile.update'), [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $other->email,
                'phone' => $this->user->phone,
            ])
            ->assertSessionHasErrors('email');

        $this->assertNotSame($other->email, $this->user->fresh()->email);
    }

    public function test_customer_cannot_take_another_customers_phone(): void
    {
        $other = User::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->put(route('account.profile.update'), [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'phone' => $other->phone,
            ])
            ->assertSessionHasErrors('phone');
    }

    public function test_invalid_state_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->put(route('account.profile.update'), [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'state' => 'Atlantis',
            ])
            ->assertSessionHasErrors('state');
    }

    public function test_customer_can_change_password_with_the_correct_current_password(): void
    {
        $this->actingAs($this->user)
            ->put(route('account.profile.password'), [
                'current_password' => 'old-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('brand-new-password', $this->user->fresh()->password));
    }

    public function test_password_change_is_rejected_when_current_password_is_wrong(): void
    {
        $this->actingAs($this->user)
            ->put(route('account.profile.password'), [
                'current_password' => 'wrong-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $this->user->fresh()->password));
    }

    public function test_password_change_requires_confirmation_to_match(): void
    {
        $this->actingAs($this->user)
            ->put(route('account.profile.password'), [
                'current_password' => 'old-password',
                'password' => 'brand-new-password',
                'password_confirmation' => 'something-else',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('old-password', $this->user->fresh()->password));
    }

    public function test_password_must_meet_the_minimum_length(): void
    {
        $this->actingAs($this->user)
            ->put(route('account.profile.password'), [
                'current_password' => 'old-password',
                'password' => 'short',
                'password_confirmation' => 'short',
            ])
            ->assertSessionHasErrors('password');
    }
}

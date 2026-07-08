<?php

namespace Tests\Feature\Auth;

use App\Models\MembershipTier;
use App\Models\SavingsPlan;
use App\Models\SavingsPlanOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function createTier(string $name, string $slug, float $annualFee, float $packPrice, int $sortOrder): MembershipTier
    {
        return MembershipTier::create([
            'name' => $name,
            'slug' => $slug,
            'annual_fee' => $annualFee,
            'instant_food_pack_price' => $packPrice,
            'is_active' => true,
            'sort_order' => $sortOrder,
        ]);
    }

    private function registerPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'phone' => '+2348000000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => '123 Test Street',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'payment_type' => 'membership_only',
            'selected_tier_id' => null,
            'savings_plan_option_id' => null,
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->createTier('Bronze', 'bronze', 5000, 20000, 1);

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_with_membership_only(): void
    {
        $tier = $this->createTier('Bronze', 'bronze', 5000, 20000, 1);

        $response = $this->post('/register', $this->registerPayload([
            'email' => 'test@example.com',
            'phone' => '+2348000000000',
            'payment_type' => 'membership_only',
            'selected_tier_id' => $tier->id,
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'current_tier_id' => $tier->id,
        ]);

        $response->assertRedirectContains('payment/pending');
        $response->assertSessionHas('pending_registration_user_id');
    }

    public function test_user_can_register_with_silver_tier_and_savings_plan(): void
    {
        $this->createTier('Bronze', 'bronze', 5000, 20000, 1);
        $silverTier = $this->createTier('Silver', 'silver', 5000, 50000, 2);

        $plan = SavingsPlan::factory()->create(['is_active' => true, 'name' => 'Bronze']);
        $option = SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 25,
            'weekly_contribution' => 4000,
            'is_active' => true,
        ]);

        $response = $this->post('/register', $this->registerPayload([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000001',
            'payment_type' => 'with_savings_plan',
            'selected_tier_id' => $silverTier->id,
            'savings_plan_option_id' => $option->id,
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'first_name' => 'Jane',
            'current_tier_id' => $silverTier->id,
        ]);

        $response->assertRedirectContains('payment/pending');
        $response->assertSessionHas('pending_savings_plan_option_id', (string) $option->id);
    }

    public function test_user_can_register_with_savings_plan_membership_only(): void
    {
        $tier = $this->createTier('Bronze', 'bronze', 5000, 20000, 1);

        $plan = SavingsPlan::factory()->create(['is_active' => true, 'name' => 'Gold']);
        $option = SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 50,
            'weekly_contribution' => 5000,
            'is_active' => true,
        ]);

        $response = $this->post('/register', $this->registerPayload([
            'email' => 'gold@example.com',
            'phone' => '+2348000000002',
            'payment_type' => 'membership_only',
            'selected_tier_id' => $tier->id,
            'savings_plan_option_id' => $option->id,
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'gold@example.com',
            'current_tier_id' => $tier->id,
        ]);

        $response->assertRedirectContains('payment/pending');
    }

    public function test_registration_fails_with_invalid_payment_type(): void
    {
        $tier = $this->createTier('Bronze', 'bronze', 5000, 20000, 1);

        $response = $this->post('/register', $this->registerPayload([
            'email' => 'bad@example.com',
            'phone' => '+2348000000099',
            'payment_type' => 'with_starter_pack',
            'selected_tier_id' => $tier->id,
        ]));

        $response->assertSessionHasErrors('payment_type');
        $this->assertDatabaseMissing('users', ['email' => 'bad@example.com']);
    }

    public function test_registration_with_invalid_savings_plan_option_fails(): void
    {
        $tier = $this->createTier('Bronze', 'bronze', 5000, 20000, 1);

        $response = $this->post('/register', $this->registerPayload([
            'email' => 'badplan@example.com',
            'phone' => '+2348000000098',
            'payment_type' => 'with_savings_plan',
            'selected_tier_id' => $tier->id,
            'savings_plan_option_id' => 99999,
        ]));

        $response->assertSessionHasErrors('savings_plan_option_id');
        $this->assertDatabaseMissing('users', ['email' => 'badplan@example.com']);
    }
}

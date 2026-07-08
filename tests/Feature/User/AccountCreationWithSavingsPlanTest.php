<?php

namespace Tests\Feature\User;

use App\Models\SavingsPlan;
use App\Models\SavingsPlanOption;
use App\Models\User;
use App\Models\UserAccount;
use Database\Seeders\MembershipTierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountCreationWithSavingsPlanTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->seed(MembershipTierSeeder::class);

        $this->user = User::factory()->create([
            'is_active' => true,
            'referral_code' => 'TESTCODE',
        ]);
    }

    public function test_user_can_view_create_account_form(): void
    {
        SavingsPlan::factory()->create(['is_active' => true, 'name' => 'Bronze']);

        $response = $this->actingAs($this->user)
            ->get(route('user.accounts.create'));

        $response->assertStatus(200);
        $response->assertViewHas('savingsPlans');
    }

    public function test_user_can_create_account_with_savings_plan_option(): void
    {
        $plan = SavingsPlan::factory()->create(['is_active' => true, 'name' => 'Bronze']);
        $option = SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 25,
            'weekly_contribution' => 4000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('user.accounts.store'), [
                'account_name' => 'My Savings',
                'savings_plan_option_id' => $option->id,
            ]);

        $response->assertRedirect(route('user.accounts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_accounts', [
            'primary_user_id' => $this->user->id,
            'account_name' => 'My Savings',
            'savings_plan_option_id' => $option->id,
            'weekly_contribution_amount' => 4000,
            'status' => 'active',
        ]);
    }

    public function test_user_can_create_account_with_referral_code(): void
    {
        $referrer = User::factory()->create(['referral_code' => 'REFERRER']);
        $plan = SavingsPlan::factory()->create(['is_active' => true]);
        $option = SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'weekly_contribution' => 5000,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('user.accounts.store'), [
                'account_name' => 'Referred Account',
                'savings_plan_option_id' => $option->id,
                'referral_code' => 'REFERRER',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_accounts', [
            'account_name' => 'Referred Account',
            'referred_by' => $referrer->id,
        ]);
    }

    public function test_user_cannot_create_account_without_option(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('user.accounts.store'), [
                'account_name' => 'My Account',
                'savings_plan_option_id' => '',
            ]);

        $response->assertSessionHasErrors('savings_plan_option_id');
    }

    public function test_user_cannot_create_account_with_invalid_option_id(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('user.accounts.store'), [
                'account_name' => 'My Account',
                'savings_plan_option_id' => 99999,
            ]);

        $response->assertSessionHasErrors('savings_plan_option_id');
    }

    public function test_user_cannot_create_account_without_name(): void
    {
        $plan = SavingsPlan::factory()->create();
        $option = SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id]);

        $response = $this->actingAs($this->user)
            ->post(route('user.accounts.store'), [
                'account_name' => '',
                'savings_plan_option_id' => $option->id,
            ]);

        $response->assertSessionHasErrors('account_name');
    }

    public function test_account_has_correct_plan_name_via_accessor(): void
    {
        $plan = SavingsPlan::factory()->create(['name' => 'Gold']);
        $option = SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 50,
        ]);

        $account = UserAccount::factory()->create([
            'primary_user_id' => $this->user->id,
            'savings_plan_option_id' => $option->id,
        ]);

        $account->load('savingsPlanOption.plan');
        $this->assertEquals('Gold', $account->plan_name);
        $this->assertEquals(50, $account->plan_duration_weeks);
    }

    public function test_old_contribution_plan_account_still_works(): void
    {
        $account = UserAccount::factory()->create([
            'primary_user_id' => $this->user->id,
            'selected_plan_id' => null,
            'savings_plan_option_id' => null,
        ]);

        $this->assertEquals('N/A', $account->plan_name);
        $this->assertEquals(0, $account->plan_duration_weeks);
    }
}

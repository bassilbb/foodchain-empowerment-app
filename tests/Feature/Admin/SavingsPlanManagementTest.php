<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\SavingsPlan;
use App\Models\SavingsPlanOption;
use App\Models\UserAccount;
use Database\Seeders\MembershipTierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsPlanManagementTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->seed(MembershipTierSeeder::class);

        $this->admin = AdminUser::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_plans_index(): void
    {
        SavingsPlan::factory()->count(2)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.savings-plans.index'));

        $response->assertStatus(200);
        $response->assertViewHas('plans');
    }

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.savings-plans.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_plan_with_options(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.savings-plans.store'), [
                'name' => 'Bronze',
                'target_amount' => 100000,
                'description' => 'Test plan',
                'is_active' => true,
                'options' => [
                    ['duration_weeks' => 12, 'weekly_contribution' => 8500],
                    ['duration_weeks' => 25, 'weekly_contribution' => 4000],
                ],
            ]);

        $response->assertRedirect(route('admin.savings-plans.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('savings_plans', ['name' => 'Bronze']);
        $this->assertDatabaseHas('savings_plan_options', [
            'duration_weeks' => 12,
            'weekly_contribution' => 8500,
        ]);
        $this->assertDatabaseHas('savings_plan_options', [
            'duration_weeks' => 25,
            'weekly_contribution' => 4000,
        ]);
    }

    public function test_admin_cannot_create_plan_without_options(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.savings-plans.store'), [
                'name' => 'Bronze',
                'target_amount' => 100000,
                'options' => [],
            ]);

        $response->assertSessionHasErrors('options');
        $this->assertDatabaseMissing('savings_plans', ['name' => 'Bronze']);
    }

    public function test_admin_cannot_create_plan_with_invalid_data(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.savings-plans.store'), [
                'name' => '',
                'target_amount' => 0,
                'options' => [
                    ['duration_weeks' => 0, 'weekly_contribution' => 0],
                ],
            ]);

        $response->assertSessionHasErrors(['name', 'target_amount', 'options.0.duration_weeks', 'options.0.weekly_contribution']);
    }

    public function test_admin_can_view_edit_form(): void
    {
        $plan = SavingsPlan::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.savings-plans.edit', $plan));

        $response->assertStatus(200);
        $response->assertViewHas('savingsPlan');
    }

    public function test_admin_can_update_plan_and_options(): void
    {
        $plan = SavingsPlan::factory()->create(['name' => 'Bronze']);
        $option = SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 12,
            'weekly_contribution' => 8500,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.savings-plans.update', $plan), [
                'name' => 'Silver',
                'target_amount' => 150000,
                'is_active' => true,
                'options' => [
                    ['id' => $option->id, 'duration_weeks' => 25, 'weekly_contribution' => 6000],
                    ['duration_weeks' => 50, 'weekly_contribution' => 3000],
                ],
            ]);

        $response->assertRedirect(route('admin.savings-plans.index'));

        $this->assertDatabaseHas('savings_plans', [
            'id' => $plan->id,
            'name' => 'Silver',
            'target_amount' => 150000,
        ]);
        $this->assertDatabaseHas('savings_plan_options', [
            'id' => $option->id,
            'duration_weeks' => 25,
            'weekly_contribution' => 6000,
        ]);
        $this->assertDatabaseHas('savings_plan_options', [
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 50,
            'weekly_contribution' => 3000,
        ]);
    }

    public function test_admin_can_delete_plan_without_accounts(): void
    {
        $plan = SavingsPlan::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.savings-plans.destroy', $plan));

        $response->assertRedirect(route('admin.savings-plans.index'));
        $this->assertDatabaseMissing('savings_plans', ['id' => $plan->id]);
    }

    public function test_admin_cannot_delete_plan_with_active_accounts(): void
    {
        $plan = SavingsPlan::factory()->create();
        $option = SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id]);

        UserAccount::factory()->create([
            'savings_plan_option_id' => $option->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.savings-plans.destroy', $plan));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('savings_plans', ['id' => $plan->id]);
    }

    public function test_admin_can_toggle_option_status(): void
    {
        $option = SavingsPlanOption::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.savings-plans.toggle-option', $option));

        $response->assertRedirect();
        $this->assertFalse($option->fresh()->is_active);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.savings-plans.toggle-option', $option));

        $response->assertRedirect();
        $this->assertTrue($option->fresh()->is_active);
    }
}

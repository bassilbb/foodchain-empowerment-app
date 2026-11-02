<?php

namespace Tests\Unit;

use App\Models\SavingsPlan;
use App\Models\SavingsPlanOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_savings_plan(): void
    {
        $plan = SavingsPlan::factory()->create([
            'name' => 'Bronze',
            'target_amount' => 100000,
        ]);

        $this->assertModelExists($plan);
        $this->assertEquals('Bronze', $plan->name);
        $this->assertEquals(100000, (float) $plan->target_amount);
    }

    public function test_plan_has_many_options(): void
    {
        $plan = SavingsPlan::factory()->create();
        $option1 = SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 12]);
        $option2 = SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 25]);

        $this->assertCount(2, $plan->options);
        $this->assertTrue($plan->options->contains($option1));
        $this->assertTrue($plan->options->contains($option2));
    }

    public function test_plan_has_active_options(): void
    {
        $plan = SavingsPlan::factory()->create();
        SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 12, 'is_active' => true]);
        SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 25, 'is_active' => false]);

        $this->assertCount(2, $plan->options);
        $this->assertCount(1, $plan->activeOptions);
    }

    public function test_active_scope(): void
    {
        SavingsPlan::factory()->create(['is_active' => true, 'name' => 'Bronze']);
        SavingsPlan::factory()->create(['is_active' => false, 'name' => 'Silver']);

        $this->assertCount(1, SavingsPlan::active()->get());
        $this->assertEquals('Bronze', SavingsPlan::active()->first()->name);
    }

    public function test_plan_is_deleted_with_options(): void
    {
        $plan = SavingsPlan::factory()->create();
        SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 12]);
        SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 25]);
        SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 50]);

        $plan->delete();

        $this->assertDatabaseMissing('savings_plans', ['id' => $plan->id]);
        $this->assertEquals(0, SavingsPlanOption::where('savings_plan_id', $plan->id)->count());
    }
}

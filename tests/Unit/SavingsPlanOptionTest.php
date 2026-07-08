<?php

namespace Tests\Unit;

use App\Models\SavingsPlan;
use App\Models\SavingsPlanOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsPlanOptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_option(): void
    {
        $plan = SavingsPlan::factory()->create();
        $option = SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 25,
            'weekly_contribution' => 4000,
        ]);

        $this->assertModelExists($option);
        $this->assertEquals(25, $option->duration_weeks);
        $this->assertEquals(4000, (float) $option->weekly_contribution);
    }

    public function test_option_belongs_to_plan(): void
    {
        $plan = SavingsPlan::factory()->create(['name' => 'Test Plan']);
        $option = SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id]);

        $this->assertInstanceOf(SavingsPlan::class, $option->plan);
        $this->assertEquals('Test Plan', $option->plan->name);
    }

    public function test_active_scope(): void
    {
        $plan = SavingsPlan::factory()->create();
        SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 12, 'is_active' => true]);
        SavingsPlanOption::factory()->create(['savings_plan_id' => $plan->id, 'duration_weeks' => 25, 'is_active' => false]);

        $this->assertCount(1, SavingsPlanOption::active()->get());
    }

    public function test_prevents_duplicate_durations_per_plan(): void
    {
        $plan = SavingsPlan::factory()->create();
        SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 25,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan->id,
            'duration_weeks' => 25,
        ]);
    }

    public function test_allows_same_duration_across_different_plans(): void
    {
        $plan1 = SavingsPlan::factory()->create();
        $plan2 = SavingsPlan::factory()->create();

        SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan1->id,
            'duration_weeks' => 25,
        ]);
        SavingsPlanOption::factory()->create([
            'savings_plan_id' => $plan2->id,
            'duration_weeks' => 25,
        ]);

        $this->assertCount(2, SavingsPlanOption::all());
    }
}

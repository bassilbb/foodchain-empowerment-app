<?php

namespace Tests\Feature\User;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Incident;
use App\Models\MembershipTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Branch $branch;
    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $tier = MembershipTier::factory()->create(['slug' => 'bronze', 'annual_fee' => 5000, 'instant_food_pack_price' => 20000]);
        $this->user = User::factory()->create(['current_tier_id' => $tier->id]);
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TEST-01',
            'address' => '123 Test St',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'phone' => '+2348000000000',
            'email' => 'test@branch.com',
            'is_active' => true,
        ]);
        $this->department = Department::create([
            'branch_id' => $this->branch->id,
            'name' => 'Test Dept',
            'code' => 'TEST-01-DEPT',
        ]);
    }

    public function test_create_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.incidents.create'));

        $response->assertStatus(200);
    }

    public function test_user_can_create_incident(): void
    {
        $response = $this->actingAs($this->user)->post(route('user.incidents.store'), [
            'branch_id' => $this->branch->id,
            'department_id' => $this->department->id,
            'title' => 'Test incident',
            'description' => 'Test description',
            'category' => 'technical',
            'priority' => 'medium',
        ]);

        $this->assertDatabaseHas('incidents', [
            'reported_by' => $this->user->id,
            'branch_id' => $this->branch->id,
            'department_id' => $this->department->id,
            'title' => 'Test incident',
            'description' => 'Test description',
            'category' => 'technical',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $incident = Incident::where('reported_by', $this->user->id)->first();
        $response->assertRedirect(route('user.incidents.show', $incident));
    }

    public function test_user_can_view_own_incident(): void
    {
        $incident = Incident::create([
            'branch_id' => $this->branch->id,
            'department_id' => $this->department->id,
            'title' => 'Test',
            'description' => 'Test',
            'category' => 'safety',
            'priority' => 'high',
            'reported_by' => $this->user->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->get(route('user.incidents.show', $incident));

        $response->assertStatus(200);
        $response->assertSee('Test');
    }

    public function test_user_cannot_view_others_incident(): void
    {
        $otherUser = User::factory()->create();
        $incident = Incident::create([
            'branch_id' => $this->branch->id,
            'department_id' => $this->department->id,
            'title' => 'Other incident',
            'description' => 'Secret',
            'category' => 'logistics',
            'priority' => 'low',
            'reported_by' => $otherUser->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->get(route('user.incidents.show', $incident));

        $response->assertStatus(403);
    }

    public function test_user_can_comment_on_own_incident(): void
    {
        $incident = Incident::create([
            'branch_id' => $this->branch->id,
            'department_id' => $this->department->id,
            'title' => 'Test',
            'description' => 'Test',
            'category' => 'safety',
            'priority' => 'high',
            'reported_by' => $this->user->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->post(route('user.incidents.comment', $incident), [
            'comment' => 'My comment',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('incident_comments', [
            'incident_id' => $incident->id,
            'user_id' => $this->user->id,
            'comment' => 'My comment',
            'is_internal' => false,
        ]);
    }

    public function test_user_cannot_comment_on_others_incident(): void
    {
        $otherUser = User::factory()->create();
        $incident = Incident::create([
            'branch_id' => $this->branch->id,
            'department_id' => $this->department->id,
            'title' => 'Test',
            'description' => 'Test',
            'category' => 'safety',
            'priority' => 'high',
            'reported_by' => $otherUser->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->post(route('user.incidents.comment', $incident), [
            'comment' => 'Sneaky comment',
        ]);

        $response->assertStatus(403);
    }
}

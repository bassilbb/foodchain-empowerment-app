<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Branch;
use App\Models\Department;
use App\Models\FoodDistribution;
use App\Models\FoodItem;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\MembershipTier;
use App\Models\PickupLocation;
use App\Models\Staff;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompletedPagesTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->admin = AdminUser::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_food_items_show_page_renders_with_inventory(): void
    {
        $foodItem = FoodItem::create([
            'name' => 'Rice',
            'slug' => 'rice',
            'unit' => 'kg',
            'unit_price' => 5000,
            'is_active' => true,
        ]);

        $location = PickupLocation::create([
            'name' => 'Lagos Branch',
            'address' => '23 Adeola Odeku St',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'is_active' => true,
        ]);

        Inventory::create([
            'food_item_id' => $foodItem->id,
            'location_id' => $location->id,
            'quantity' => 50,
            'reserved_quantity' => 10,
            'cost_per_unit' => 4000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.food-items.show', $foodItem));

        $response->assertStatus(200);
        $response->assertViewHas('foodItem');
        $response->assertSee('Rice');
    }

    public function test_locations_show_page_renders_with_inventory_and_distributions(): void
    {
        $foodItem = FoodItem::create([
            'name' => 'Beans',
            'slug' => 'beans',
            'unit' => 'kg',
            'unit_price' => 3000,
            'is_active' => true,
        ]);

        $location = PickupLocation::create([
            'name' => 'Abuja Branch',
            'address' => '1 Airport Road',
            'city' => 'Abuja',
            'state' => 'FCT',
            'is_active' => true,
        ]);

        Inventory::create([
            'food_item_id' => $foodItem->id,
            'location_id' => $location->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
            'cost_per_unit' => 2500,
        ]);

        $user = User::factory()->create();
        $tier = MembershipTier::factory()->create();

        FoodDistribution::create([
            'user_id' => $user->id,
            'tier_id' => $tier->id,
            'package_value' => 20000,
            'status' => 'approved',
            'pickup_location_id' => $location->id,
            'approved_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.locations.show', $location));

        $response->assertStatus(200);
        $response->assertViewHas('location');
        $response->assertSee('Abuja Branch');
        $response->assertSee('Beans');
    }

    public function test_admin_users_show_page_renders_with_related_records(): void
    {
        $target = AdminUser::factory()->create([
            'role' => 'inventory_manager',
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $tier = MembershipTier::factory()->create();
        $foodItem = FoodItem::create([
            'name' => 'Vegetable Oil',
            'slug' => 'vegetable-oil',
            'unit' => 'liters',
            'unit_price' => 2000,
            'is_active' => true,
        ]);

        FoodDistribution::create([
            'user_id' => $user->id,
            'tier_id' => $tier->id,
            'package_value' => 15000,
            'status' => 'approved',
            'approved_by' => $target->id,
            'approved_at' => now(),
        ]);

        InventoryTransaction::create([
            'food_item_id' => $foodItem->id,
            'type' => 'stock_in',
            'quantity' => 20,
            'quantity_before' => 0,
            'quantity_after' => 20,
            'performed_by' => $target->id,
        ]);

        SupportTicket::create([
            'user_id' => $user->id,
            'subject' => 'Missing package',
            'message' => 'I did not receive my package.',
            'assigned_to' => $target->id,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.admin-users.show', $target));

        $response->assertStatus(200);
        $response->assertViewHas('adminUser');
        $response->assertSee('Missing package');
    }

    public function test_admin_user_show_renders_for_self(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.admin-users.show', $this->admin));

        $response->assertStatus(200);
    }

    public function test_staff_module_full_crud(): void
    {
        $branch = Branch::create([
            'name' => 'Head Office',
            'code' => 'HQ',
        ]);

        $department = Department::create([
            'branch_id' => $branch->id,
            'name' => 'Operations',
            'code' => 'OPS',
        ]);

        $staff = Staff::create([
            'department_id' => $department->id,
            'admin_user_id' => $this->admin->id,
            'staff_number' => 'STF-0001',
            'position' => 'Operations Officer',
            'phone' => '08012345678',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.staff.index'));
        $response->assertStatus(200);
        $response->assertSee('STF-0001');

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.staff.show', $staff));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.staff.create'));
        $response->assertStatus(200);
        $response->assertSee('Operations');

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.staff.edit', $staff));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.staff.update', $staff), [
                'department_id' => $department->id,
                'admin_user_id' => $this->admin->id,
                'staff_number' => 'STF-0001',
                'position' => 'Senior Operations Officer',
                'phone' => '08012345678',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'position' => 'Senior Operations Officer',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.staff.destroy', $staff));

        $response->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseMissing('staff', ['id' => $staff->id]);
    }
}
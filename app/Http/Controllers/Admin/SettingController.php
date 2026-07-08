<?php

// app/Http/Controllers/Admin/SettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use App\Models\AdminRolePermission;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        $permissions = AdminPermission::orderBy('module')->orderBy('name')->get();

        if ($permissions->isEmpty()) {
            $permissions = $this->seedDefaultPermissions();
        }

        $rolePermissions = AdminRolePermission::all()->groupBy('role');

        $logoSetting = Setting::where('key', 'site_logo')->first();

        return view('admin.settings.index', compact('settings', 'permissions', 'rolePermissions', 'logoSetting'));
    }

    private function seedDefaultPermissions()
    {
        $defaultPermissions = [
            ['name' => 'View Users', 'slug' => 'view_users', 'module' => 'users'],
            ['name' => 'Create Users', 'slug' => 'create_users', 'module' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'edit_users', 'module' => 'users'],
            ['name' => 'View Payments', 'slug' => 'view_payments', 'module' => 'payments'],
            ['name' => 'Verify Payments', 'slug' => 'verify_payments', 'module' => 'payments'],
            ['name' => 'View Inventory', 'slug' => 'view_inventory', 'module' => 'inventory'],
            ['name' => 'Manage Inventory', 'slug' => 'manage_inventory', 'module' => 'inventory'],
            ['name' => 'View Distributions', 'slug' => 'view_distributions', 'module' => 'distributions'],
            ['name' => 'Manage Distributions', 'slug' => 'manage_distributions', 'module' => 'distributions'],
            ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'module' => 'settings'],
            ['name' => 'View Support Tickets', 'slug' => 'view_support_tickets', 'module' => 'support'],
            ['name' => 'Respond to Tickets', 'slug' => 'respond_to_tickets', 'module' => 'support'],
            ['name' => 'View Reports', 'slug' => 'view_reports', 'module' => 'reports'],
        ];

        $permissionIds = [];
        foreach ($defaultPermissions as $perm) {
            $permission = AdminPermission::create($perm);
            $permissionIds[$perm['slug']] = $permission->id;
        }

        // Assign permissions to each role
        // Super Admin - gets all
        $superAdminPerms = array_values($permissionIds);
        foreach ($superAdminPerms as $permId) {
            AdminRolePermission::create(['role' => 'super_admin', 'permission_id' => $permId]);
        }

        // Inventory Manager
        $inventoryPerms = ['view_inventory', 'manage_inventory', 'view_distributions', 'manage_distributions', 'view_users'];
        foreach ($inventoryPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                AdminRolePermission::create(['role' => 'inventory_manager', 'permission_id' => $permissionIds[$slug]]);
            }
        }

        // Customer Support
        $supportPerms = ['view_users', 'view_payments', 'view_distributions', 'view_support_tickets', 'respond_to_tickets', 'view_reports'];
        foreach ($supportPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                AdminRolePermission::create(['role' => 'customer_support', 'permission_id' => $permissionIds[$slug]]);
            }
        }

        return AdminPermission::orderBy('module')->orderBy('name')->get();
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => 'general']
            );
        }

        // Clear config cache
        Artisan::call('config:clear');

        return back()->with('success', 'Settings updated successfully.');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Delete old logo if exists
        $oldLogo = Setting::where('key', 'site_logo')->first();
        if ($oldLogo && $oldLogo->value) {
            Storage::disk('public')->delete($oldLogo->value);
        }

        // Store new logo
        $path = $request->file('logo')->store('logos', 'public');

        // Save to settings
        Setting::updateOrCreate(
            ['key' => 'site_logo'],
            ['value' => $path, 'group' => 'general']
        );

        return back()->with('success', 'Logo uploaded successfully.');
    }

    public function deleteLogo()
    {
        $logo = Setting::where('key', 'site_logo')->first();
        if ($logo && $logo->value) {
            Storage::disk('public')->delete($logo->value);
            $logo->delete();
        }

        return back()->with('success', 'Logo deleted successfully.');
    }

    public function updateRolePermissions(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:super_admin,inventory_manager,customer_support,member',
            'permissions' => 'required|array',
        ]);

        // Remove existing permissions for this role
        AdminRolePermission::where('role', $validated['role'])->delete();

        // Add new permissions
        foreach ($validated['permissions'] as $permissionId) {
            AdminRolePermission::create([
                'role' => $validated['role'],
                'permission_id' => $permissionId,
            ]);
        }

        return back()->with('success', 'Role permissions updated successfully.');
    }
}

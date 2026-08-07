<?php

// app/Http/Controllers/Admin/SettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use App\Models\AdminRolePermission;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
        $faviconSetting = Setting::where('key', 'site_favicon')->first();

        return view('admin.settings.index', compact('settings', 'permissions', 'rolePermissions', 'logoSetting', 'faviconSetting'));
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
            $existing = Setting::where('key', $key)->first();
            $group = $existing ? $existing->group : (str_starts_with($key, 'module_') ? 'modules' : 'general');
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => $group]
            );
        }

        // Clear config cache
        Artisan::call('config:clear');

        return back()->with('success', 'Settings updated successfully.');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:10240',
        ]);

        try {
            $oldLogo = Setting::where('key', 'site_logo')->first();
            if ($oldLogo && $oldLogo->value) {
                foreach (['uploads/', 'storage/app/public/'] as $dir) {
                    $p = $dir === 'uploads/' ? public_path($dir.$oldLogo->value) : storage_path($dir.$oldLogo->value);
                    if (file_exists($p)) {
                        unlink($p);
                    }
                }
            }

            $file = $request->file('logo');
            $filename = 'logo_'.time().'.'.$file->getClientOriginalExtension();
            $uploadDir = public_path('uploads/logos');
            if (! file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $filename);
            $path = 'logos/'.$filename;

            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $path, 'group' => 'general']
            );

            return back()->with('success', 'Logo uploaded successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Logo upload failed: '.$e->getMessage());
        }
    }

    public function deleteLogo()
    {
        $logo = Setting::where('key', 'site_logo')->first();
        if ($logo && $logo->value) {
            foreach (['uploads/', 'storage/app/public/'] as $dir) {
                $p = $dir === 'uploads/' ? public_path($dir.$logo->value) : storage_path($dir.$logo->value);
                if (file_exists($p)) {
                    unlink($p);
                }
            }
            $logo->delete();
        }

        return back()->with('success', 'Logo deleted successfully.');
    }

    public function updateFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:png,jpg,jpeg,svg,ico,webp|max:5120',
        ]);

        try {
            $oldFavicon = Setting::where('key', 'site_favicon')->first();
            if ($oldFavicon && $oldFavicon->value) {
                foreach (['uploads/', 'storage/app/public/'] as $dir) {
                    $p = $dir === 'uploads/' ? public_path($dir.$oldFavicon->value) : storage_path($dir.$oldFavicon->value);
                    if (file_exists($p)) {
                        unlink($p);
                    }
                }
            }

            $file = $request->file('favicon');
            $filename = 'favicon_'.time().'.'.$file->getClientOriginalExtension();
            $uploadDir = public_path('uploads/favicons');
            if (! file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $filename);
            $path = 'favicons/'.$filename;

            Setting::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => $path, 'group' => 'general']
            );

            return back()->with('success', 'Favicon uploaded successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Favicon upload failed: '.$e->getMessage());
        }
    }

    public function deleteFavicon()
    {
        $favicon = Setting::where('key', 'site_favicon')->first();
        if ($favicon && $favicon->value) {
            foreach (['uploads/', 'storage/app/public/'] as $dir) {
                $p = $dir === 'uploads/' ? public_path($dir.$favicon->value) : storage_path($dir.$favicon->value);
                if (file_exists($p)) {
                    unlink($p);
                }
            }
            $favicon->delete();
        }

        return back()->with('success', 'Favicon deleted successfully.');
    }

    public function runDeductions()
    {
        try {
            Artisan::call('accounts:process-weekly-deductions');
            $output = Artisan::output();

            return back()->with('success', 'Deductions completed. Output: '.trim($output));
        } catch (\Exception $e) {
            return back()->with('error', 'Deduction command failed: '.$e->getMessage());
        }
    }

    public function retryDeductions()
    {
        try {
            Artisan::call('accounts:retry-defaulted-deductions');
            $output = Artisan::output();

            return back()->with('success', 'Retry completed. Output: '.trim($output));
        } catch (\Exception $e) {
            return back()->with('error', 'Retry command failed: '.$e->getMessage());
        }
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

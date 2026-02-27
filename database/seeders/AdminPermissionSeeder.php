<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view_users', 'module' => 'users'],
            ['name' => 'Create Users', 'slug' => 'create_users', 'module' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'edit_users', 'module' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'delete_users', 'module' => 'users'],
            ['name' => 'View Referral Tree', 'slug' => 'view_referral_tree', 'module' => 'users'],
            
            // Payment Management
            ['name' => 'View Payments', 'slug' => 'view_payments', 'module' => 'payments'],
            ['name' => 'Verify Payments', 'slug' => 'verify_payments', 'module' => 'payments'],
            
            // Inventory Management
            ['name' => 'View Inventory', 'slug' => 'view_inventory', 'module' => 'inventory'],
            ['name' => 'Manage Inventory', 'slug' => 'manage_inventory', 'module' => 'inventory'],
            ['name' => 'Add Stock', 'slug' => 'add_stock', 'module' => 'inventory'],
            ['name' => 'Remove Stock', 'slug' => 'remove_stock', 'module' => 'inventory'],
            
            // Distribution Management
            ['name' => 'View Distributions', 'slug' => 'view_distributions', 'module' => 'distributions'],
            ['name' => 'Approve Distributions', 'slug' => 'approve_distributions', 'module' => 'distributions'],
            ['name' => 'Create Discretionary Distribution', 'slug' => 'create_discretionary_distribution', 'module' => 'distributions'],
            ['name' => 'Manage Distribution Schedule', 'slug' => 'manage_distribution_schedule', 'module' => 'distributions'],
            
            // Settings & Configuration
            ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'module' => 'settings'],
            ['name' => 'Manage Tiers', 'slug' => 'manage_tiers', 'module' => 'settings'],
            ['name' => 'Manage Locations', 'slug' => 'manage_locations', 'module' => 'settings'],
            
            // Support
            ['name' => 'View Support Tickets', 'slug' => 'view_support_tickets', 'module' => 'support'],
            ['name' => 'Respond to Tickets', 'slug' => 'respond_to_tickets', 'module' => 'support'],
            
            // Reports
            ['name' => 'View Reports', 'slug' => 'view_reports', 'module' => 'reports'],
            ['name' => 'Export Data', 'slug' => 'export_data', 'module' => 'reports'],
        ];

        foreach ($permissions as $permission) {
            $permId = DB::table('admin_permissions')->insertGetId([
                'name' => $permission['name'],
                'slug' => $permission['slug'],
                'module' => $permission['module'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Super admin gets all permissions
            DB::table('admin_role_permissions')->insert([
                'role' => 'super_admin',
                'permission_id' => $permId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Inventory Manager permissions
        $inventoryPerms = ['view_inventory', 'manage_inventory', 'add_stock', 'remove_stock', 
                          'view_distributions', 'manage_distribution_schedule', 'view_users'];
        $this->assignRolePermissions('inventory_manager', $inventoryPerms);

        // Customer Support permissions
        $supportPerms = ['view_users', 'view_payments', 'view_distributions', 
                        'view_support_tickets', 'respond_to_tickets', 'view_reports'];
        $this->assignRolePermissions('customer_support', $supportPerms);
    }

    private function assignRolePermissions(string $role, array $slugs): void
    {
        $permissions = DB::table('admin_permissions')->whereIn('slug', $slugs)->get();
        
        foreach ($permissions as $permission) {
            DB::table('admin_role_permissions')->insert([
                'role' => $role,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
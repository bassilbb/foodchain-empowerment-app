<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $permissions = [
        ['name' => 'View Bulk Price Uploads', 'slug' => 'view_price_imports', 'module' => 'marketplace'],
        ['name' => 'Manage Bulk Price Uploads', 'slug' => 'manage_price_imports', 'module' => 'marketplace'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('admin_permissions') || ! Schema::hasTable('admin_role_permissions')) {
            return;
        }

        foreach ($this->permissions as $permission) {
            $id = DB::table('admin_permissions')->where('slug', $permission['slug'])->value('id');

            if (! $id) {
                $id = DB::table('admin_permissions')->insertGetId([
                    'name' => $permission['name'],
                    'slug' => $permission['slug'],
                    'module' => $permission['module'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach (['super_admin', 'inventory_manager'] as $role) {
                $exists = DB::table('admin_role_permissions')
                    ->where('role', $role)
                    ->where('permission_id', $id)
                    ->exists();

                if (! $exists) {
                    DB::table('admin_role_permissions')->insert([
                        'role' => $role,
                        'permission_id' => $id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('admin_permissions')) {
            return;
        }

        $ids = DB::table('admin_permissions')
            ->whereIn('slug', array_column($this->permissions, 'slug'))
            ->pluck('id');

        if (Schema::hasTable('admin_role_permissions')) {
            DB::table('admin_role_permissions')->whereIn('permission_id', $ids)->delete();
        }

        DB::table('admin_permissions')->whereIn('id', $ids)->delete();
    }
};

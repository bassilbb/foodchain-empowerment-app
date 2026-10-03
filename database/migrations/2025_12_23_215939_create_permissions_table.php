<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Simple permission system for admin roles
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // manage_users, manage_inventory, etc.
            $table->string('slug')->unique();
            $table->string('module'); // users, inventory, payments, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // super_admin, inventory_manager, customer_support
            $table->foreignId('permission_id')->constrained('admin_permissions')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['role', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_role_permissions');
        Schema::dropIfExists('admin_permissions');
    }
};


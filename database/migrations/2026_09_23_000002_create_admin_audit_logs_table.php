<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();

            // The acting admin (AdminUser) when authenticated on the admin guard.
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_users')->nullOnDelete();

            // The subject of the action — a marketplace Vendor, Product,
            // VendorPayout, Order, etc. Polymorphic (morphs are skipped so a
            // deleted subject never cascades into audit history).
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->string('action');
            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('description', 500)->nullable();

            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};

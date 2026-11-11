<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_product_image_research', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained('marketplace_products')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('marketplace_vendors')->nullOnDelete();
            $table->string('sku')->nullable()->index();
            $table->string('product_name');

            // pending_research | candidate_found | pending_verification |
            // verified | downloaded | not_found | rejected
            $table->string('status')->default('pending_research')->index();

            $table->string('candidate_image_url', 2048)->nullable();
            $table->string('candidate_source_url', 2048)->nullable();
            $table->string('candidate_source_site')->nullable();
            $table->string('candidate_title')->nullable();
            $table->string('candidate_query')->nullable();

            $table->string('downloaded_path')->nullable();
            $table->string('download_status')->default('not_started');
            $table->text('download_error')->nullable();
            $table->timestamp('downloaded_at')->nullable();

            $table->string('verification_status')->default('unverified');
            $table->foreignId('verified_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->text('rejection_reason')->nullable();
            $table->text('reviewer_notes')->nullable();

            $table->unsignedInteger('research_attempts')->default(0);
            $table->timestamp('last_researched_at')->nullable();

            $table->timestamps();

            $table->index(['vendor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_product_image_research');
    }
};

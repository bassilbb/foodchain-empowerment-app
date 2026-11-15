<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per uploaded supplier price list (CSV or XML).
        Schema::create('marketplace_price_import_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('marketplace_vendors')->cascadeOnDelete();
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_users')->nullOnDelete();

            $table->string('original_filename');
            $table->string('stored_path');
            $table->string('file_hash', 64);
            $table->string('format', 10); // csv | xml

            // uploaded -> previewed -> queued/processing -> completed | failed | rolled_back
            $table->string('status', 30)->default('uploaded');

            $table->json('field_mapping')->nullable();
            $table->json('options')->nullable();

            $table->unsignedInteger('total_records')->default(0);
            $table->unsignedInteger('matched_count')->default(0);
            $table->unsignedInteger('increased_count')->default(0);
            $table->unsignedInteger('reduced_count')->default(0);
            $table->unsignedInteger('unchanged_count')->default(0);
            $table->unsignedInteger('new_count')->default(0);
            $table->unsignedInteger('missing_count')->default(0);
            $table->unsignedInteger('invalid_count')->default(0);
            $table->unsignedInteger('ambiguous_count')->default(0);
            $table->unsignedInteger('manual_count')->default(0);

            $table->unsignedInteger('applied_count')->default(0);
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedInteger('deactivated_count')->default(0);
            $table->unsignedInteger('rolled_back_count')->default(0);
            $table->unsignedInteger('processed_count')->default(0);

            $table->text('last_error')->nullable();

            $table->timestamp('previewed_at')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('rolled_back_at')->nullable();

            $table->foreignId('rollback_admin_user_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->string('rollback_reason', 500)->nullable();

            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('status');
            $table->index('created_at');
        });

        // One row per record read from the uploaded file.
        Schema::create('marketplace_price_import_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')->constrained('marketplace_price_import_batches')->cascadeOnDelete();
            $table->unsignedInteger('row_number');

            $table->foreignId('product_id')->nullable()->constrained('marketplace_products')->nullOnDelete();

            $table->string('sku')->nullable();
            $table->string('supplier_product_code')->nullable();
            $table->string('barcode')->nullable();
            $table->string('product_name')->nullable();
            $table->string('category_name')->nullable();
            $table->string('unit', 60)->nullable();

            $table->decimal('price', 14, 2)->nullable();
            $table->decimal('current_price', 14, 2)->nullable();
            $table->decimal('new_price', 14, 2)->nullable();
            $table->decimal('difference', 14, 2)->nullable();
            $table->decimal('percent_change', 10, 2)->nullable();

            // matched | new | invalid | ambiguous
            $table->string('status', 20)->default('pending');
            // sku | supplier_code | barcode | name | none
            $table->string('match_method', 20)->nullable();
            // increase | decrease | unchanged | new | invalid | ambiguous
            $table->string('change_type', 20)->nullable();

            $table->json('errors')->nullable();
            $table->json('warnings')->nullable();
            $table->json('raw')->nullable();

            $table->boolean('requires_manual_completion')->default(false);
            $table->boolean('applied')->default(false);
            $table->timestamp('applied_at')->nullable();

            $table->timestamps();

            $table->unique(['batch_id', 'row_number'], 'ppii_batch_row_unique');
            $table->index(['batch_id', 'status'], 'ppii_batch_status_index');
            $table->index('product_id', 'ppii_product_index');
            $table->index('sku', 'ppii_sku_index');
            $table->index('supplier_product_code', 'ppii_supplier_code_index');
            $table->index('barcode', 'ppii_barcode_index');
        });

        // Products that belonged to the supplier's previous catalogue but were
        // absent from the latest upload. Never deleted automatically.
        Schema::create('marketplace_price_import_missing_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')->constrained('marketplace_price_import_batches')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('marketplace_products')->nullOnDelete();

            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->decimal('current_price', 14, 2)->nullable();

            // pending | kept | deactivated
            $table->string('action', 20)->default('pending');

            $table->timestamps();

            $table->unique(['batch_id', 'product_id'], 'ppimp_batch_product_unique');
            $table->index(['batch_id', 'action'], 'ppimp_batch_action_index');
        });

        // Immutable-ish audit trail for every price change.
        Schema::create('marketplace_product_price_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->nullable()->constrained('marketplace_products')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('marketplace_vendors')->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('marketplace_price_import_batches')->nullOnDelete();
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_users')->nullOnDelete();

            // Snapshot so the audit survives a rename / deletion.
            $table->string('product_name');
            $table->string('sku')->nullable();

            // manual | bulk_import | rollback
            $table->string('source', 20)->default('bulk_import');

            $table->decimal('old_price', 14, 2)->nullable();
            $table->decimal('new_price', 14, 2)->nullable();
            $table->decimal('difference', 14, 2)->nullable();
            $table->decimal('percent_change', 10, 2)->nullable();

            // applied | rolled_back | created
            $table->string('status', 20)->default('applied');
            $table->string('note', 500)->nullable();
            $table->timestamp('rolled_back_at')->nullable();

            $table->timestamps();

            $table->index(['product_id', 'created_at'], 'ppph_product_created_index');
            $table->index(['batch_id', 'status'], 'ppph_batch_status_index');
            $table->index(['source', 'created_at'], 'ppph_source_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_product_price_histories');
        Schema::dropIfExists('marketplace_price_import_missing_products');
        Schema::dropIfExists('marketplace_price_import_items');
        Schema::dropIfExists('marketplace_price_import_batches');
    }
};

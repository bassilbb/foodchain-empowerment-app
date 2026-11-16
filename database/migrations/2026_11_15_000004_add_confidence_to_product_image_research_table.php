<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_product_image_research', function (Blueprint $table) {
            // How strongly the supplier name matched the source catalogue
            // (0.0000 - 1.0000). Kept so an administrator can sort the review
            // queue by confidence and so automated runs are auditable.
            $table->decimal('candidate_confidence', 5, 4)->nullable()->after('candidate_title');
            $table->string('candidate_match_method', 40)->nullable()->after('candidate_confidence');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_product_image_research', function (Blueprint $table) {
            $table->dropColumn(['candidate_confidence', 'candidate_match_method']);
        });
    }
};

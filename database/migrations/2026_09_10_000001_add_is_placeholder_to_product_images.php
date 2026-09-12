<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_product_images', function (Blueprint $table) {
            $table->boolean('is_placeholder')->default(false)->after('is_primary');
        });

        // Existing generated dummy images are inline SVG data URIs.
        DB::table('marketplace_product_images')
            ->where('image_path', 'like', 'data:image/svg%')
            ->update(['is_placeholder' => true]);
    }

    public function down(): void
    {
        Schema::table('marketplace_product_images', function (Blueprint $table) {
            $table->dropColumn('is_placeholder');
        });
    }
};

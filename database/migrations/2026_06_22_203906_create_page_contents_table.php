<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page')->index();
            $table->string('section')->index();
            $table->string('label')->nullable();
            $table->string('type')->default('text');
            $table->text('value')->nullable();
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['page', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};

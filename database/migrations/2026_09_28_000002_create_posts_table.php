<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Blog posts.
 *
 * The public blog used to be four hard-coded <article> blocks in
 * resources/views/blog.blade.php, so an admin had no way to edit a post, swap
 * its image, or publish anything new. This table makes them real records.
 *
 * The four existing posts are imported by DatabaseSeeder->call(BlogPostSeeder),
 * not here, so a fresh test database starts with an empty blog and the
 * importer never fires implicitly on migrate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('status')->default('published');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

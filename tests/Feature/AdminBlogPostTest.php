<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\PageContent;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBlogPostTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = AdminUser::factory()->create(['is_active' => true]);
    }

    public function test_the_public_blog_listing_shows_published_posts(): void
    {
        $post = Post::create([
            'title' => 'Bulk Buying Basics',
            'slug' => 'bulk-buying-basics',
            'category' => 'Community Bulk',
            'excerpt' => 'Group orders unlock wholesale prices.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('Bulk Buying Basics')
            ->assertSee('Group orders unlock wholesale prices.')
            ->assertSee(route('blog_details', $post->slug), false);
    }

    public function test_drafts_and_future_dated_posts_stay_off_the_public_blog(): void
    {
        Post::create(['title' => 'Hidden Draft', 'slug' => 'hidden-draft', 'status' => 'draft']);
        Post::create([
            'title' => 'Scheduled Later',
            'slug' => 'scheduled-later',
            'status' => 'published',
            'published_at' => now()->addWeek(),
        ]);

        $this->get(route('blog'))->assertOk()->assertDontSee('Hidden Draft')->assertDontSee('Scheduled Later');
        $this->get(route('blog_details', 'hidden-draft'))->assertNotFound();
        $this->get(route('blog_details', 'scheduled-later'))->assertNotFound();
    }

    public function test_a_post_detail_page_renders_the_stored_copy_and_image(): void
    {
        Storage::fake('public');
        $post = Post::create([
            'title' => 'Vendor Spotlight Story',
            'slug' => 'vendor-spotlight-story',
            'excerpt' => 'A short summary.',
            'body' => "First paragraph of the body.\n\nSecond paragraph of the body.",
            'image' => 'blog/vendor.png',
            'status' => 'published',
            'published_at' => now(),
        ]);
        Storage::disk('public')->put($post->image, 'binary');

        $this->get(route('blog_details', $post->slug))
            ->assertOk()
            ->assertSee('Vendor Spotlight Story')
            ->assertSee('First paragraph of the body.')
            ->assertSee('Second paragraph of the body.')
            ->assertSee(Storage::disk('public')->url('blog/vendor.png'), false);
    }

    public function test_the_legacy_blog_details_url_still_resolves(): void
    {
        $post = Post::create([
            'title' => 'Newest Published',
            'slug' => 'newest-published',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('blog_details'))
            ->assertOk()
            ->assertSee('Newest Published');
    }

    public function test_the_admin_post_list_loads(): void
    {
        Post::create(['title' => 'Visible In Admin', 'slug' => 'visible-in-admin', 'status' => 'draft']);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('Visible In Admin')
            ->assertSee('New Post');
    }

    public function test_an_admin_can_create_a_post_with_an_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.posts.store'), [
            'title' => 'How Delivery Works',
            'category' => 'Delivery Options',
            'excerpt' => 'Pickup, home or community delivery.',
            'body' => 'Long form content.',
            'status' => 'published',
            'is_featured' => '1',
            'image' => UploadedFile::fake()->image('delivery.jpg', 900, 600),
        ]);

        $post = Post::firstOrFail();

        $response->assertRedirect(route('admin.posts.edit', $post));
        $this->assertSame('how-delivery-works', $post->slug);
        $this->assertSame('published', $post->status);
        $this->assertTrue($post->is_featured);
        $this->assertSame($this->admin->id, $post->author_id);
        $this->assertNotNull($post->published_at);

        Storage::disk('public')->assertExists($post->image);

        $this->get(route('blog'))->assertOk()->assertSee('How Delivery Works');
    }

    public function test_an_admin_can_edit_a_previous_post_and_replace_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('blog/original.jpg', 'original-bytes');

        $post = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'status' => 'published',
            'image' => 'blog/original.jpg',
            'published_at' => now(),
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.posts.update', $post), [
                'title' => 'Corrected Title',
                'excerpt' => 'Updated summary.',
                'body' => 'Updated body.',
                'status' => 'published',
                'image_alt' => 'A replaced photo',
                'image' => UploadedFile::fake()->image('replacement.png', 800, 500),
            ])
            ->assertRedirect(route('admin.posts.edit', $post));

        $post->refresh();

        $this->assertSame('Corrected Title', $post->title);
        $this->assertSame('Updated summary.', $post->excerpt);
        $this->assertSame('A replaced photo', $post->image_alt);
        $this->assertNotSame('blog/original.jpg', $post->image);
        Storage::disk('public')->assertMissing('blog/original.jpg');

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('Corrected Title')
            ->assertDontSee('Original Title');
    }

    public function test_an_admin_can_remove_a_post_image_without_touching_the_copy(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('blog/doomed.jpg', 'bytes');

        $post = Post::create([
            'title' => 'Image Removal Case',
            'slug' => 'image-removal-case',
            'status' => 'published',
            'image' => 'blog/doomed.jpg',
            'published_at' => now(),
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.posts.update', $post), [
                'title' => 'Image Removal Case',
                'status' => 'published',
                'remove_image' => '1',
            ])
            ->assertRedirect();

        $post->refresh();
        $this->assertNull($post->image);
        Storage::disk('public')->assertMissing('blog/doomed.jpg');

        $this->get(route('blog'))->assertOk()->assertSee('Image Removal Case');
    }

    public function test_editing_the_title_does_not_change_the_post_url(): void
    {
        $post = Post::create(['title' => 'Stable Slug', 'slug' => 'stable-slug', 'status' => 'published', 'published_at' => now()]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.posts.update', $post), [
                'title' => 'A Completely Different Title',
                'status' => 'published',
            ])
            ->assertRedirect();

        $post->refresh();
        $this->assertSame('A Completely Different Title', $post->title);
        $this->assertSame('stable-slug', $post->slug, 'the URL must survive a title-only edit');

        $this->get(route('blog_details', 'stable-slug'))->assertOk()->assertSee('A Completely Different Title');
    }

    public function test_an_admin_can_set_the_slug_explicitly(): void
    {
        $post = Post::create(['title' => 'Rename Me', 'slug' => 'rename-me', 'status' => 'draft']);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.posts.update', $post), [
                'title' => 'Rename Me',
                'slug' => 'a-brand-new-slug',
                'status' => 'draft',
            ])
            ->assertRedirect();

        $this->assertSame('a-brand-new-slug', $post->fresh()->slug);
    }

    public function test_stored_image_urls_are_host_relative_and_resolve(): void
    {
        Storage::fake('public');
        $post = Post::create([
            'title' => 'Host Relative Image',
            'slug' => 'host-relative-image',
            'status' => 'published',
            'image' => 'blog/host-relative.png',
            'published_at' => now(),
        ]);
        Storage::disk('public')->put($post->image, 'bytes');

        // Storage::url() bakes in APP_URL (http://localhost by default), which
        // 404s when the app is served from 127.0.0.1 or any other host.
        $url = $post->fresh()->image_url;
        $this->assertStringStartsWith('/storage/blog/', $url, 'image URL must be host-relative');

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('src="' . $url . '"', false);

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('src="' . $url . '"', false);
    }

    public function test_a_post_without_an_image_still_renders(): void
    {
        Post::create([
            'title' => 'No Image Post',
            'slug' => 'no-image-post',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('blog'))->assertOk()->assertSee('No Image Post')->assertSee('No image');
    }

    public function test_duplicate_titles_get_unique_slugs(): void
    {
        Post::create(['title' => 'Repeat Title', 'slug' => 'repeat-title', 'status' => 'draft']);

        $second = Post::create(['title' => 'Repeat Title', 'status' => 'draft']);

        $this->assertSame('repeat-title-2', $second->fresh()->slug);
    }

    public function test_creating_a_duplicate_slug_explicitly_is_rejected(): void
    {
        Post::create(['title' => 'Taken Slug', 'slug' => 'taken-slug', 'status' => 'draft']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.posts.store'), [
                'title' => 'Another Post',
                'slug' => 'taken-slug',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('slug');

        $this->assertSame(1, Post::count());
    }

    public function test_an_admin_can_delete_a_post_and_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('blog/gone.jpg', 'bytes');

        $post = Post::create([
            'title' => 'Delete Me',
            'slug' => 'delete-me',
            'status' => 'published',
            'image' => 'blog/gone.jpg',
            'published_at' => now(),
        ]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.posts.destroy', $post))
            ->assertRedirect(route('admin.posts.index'));

        $this->assertSoftDeleted($post);
        Storage::disk('public')->assertMissing('blog/gone.jpg');

        $this->get(route('blog'))->assertOk()->assertDontSee('Delete Me');
    }

    public function test_the_admin_index_can_search_and_filter(): void
    {
        Post::create(['title' => 'Alpha Published', 'slug' => 'alpha-published', 'status' => 'published', 'published_at' => now()]);
        Post::create(['title' => 'Beta Draft', 'slug' => 'beta-draft', 'status' => 'draft']);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.posts.index', ['q' => 'Alpha']))
            ->assertOk()
            ->assertSee('Alpha Published')
            ->assertDontSee('Beta Draft');

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.posts.index', ['status' => 'draft']))
            ->assertOk()
            ->assertSee('Beta Draft')
            ->assertDontSee('Alpha Published');
    }

    public function test_disabling_the_blog_page_hides_the_listing(): void
    {
        Post::create(['title' => 'Should Be Hidden', 'slug' => 'should-be-hidden', 'status' => 'published', 'published_at' => now()]);

        PageContent::create(['page' => 'blog', 'section' => 'page_title', 'value' => 'Blog', 'is_active' => false]);

        $this->get(route('blog'))->assertForbidden()->assertDontSee('Should Be Hidden', false);
    }

    public function test_admins_only_see_the_post_manager_behind_auth(): void
    {
        $this->get(route('admin.posts.index'))->assertRedirect();
        $this->get(route('admin.posts.create'))->assertRedirect();
    }
}

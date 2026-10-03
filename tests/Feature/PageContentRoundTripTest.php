<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\PageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageContentRoundTripTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = AdminUser::factory()->create(['is_active' => true]);
    }

    public function test_the_table_starts_empty_so_the_index_offers_a_seed_button(): void
    {
        $this->assertSame(0, PageContent::count());

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.page-contents.index'))
            ->assertOk()
            ->assertSee('Create Default Content')
            ->assertSee('No page content found');
    }

    public function test_seeding_creates_every_section_the_public_views_read(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.page-contents.seed'))->assertRedirect(route('admin.page-contents.index'));

        $this->assertSame(22, PageContent::count());

        foreach (['landing', 'gallery', 'team', 'sponsor', 'supplier', 'rider', 'blog'] as $page) {
            $this->assertGreaterThan(0, PageContent::where('page', $page)->count(), "page {$page} has no rows");
        }
    }

    public function test_an_admin_edit_reaches_the_public_website(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.page-contents.seed'));

        $title = PageContent::where('page', 'landing')->where('section', 'hero_title')->firstOrFail();
        $subtitle = PageContent::where('page', 'landing')->where('section', 'hero_subtitle')->firstOrFail();

        $this->actingAs($this->admin, 'admin')->get(route('admin.page-contents.edit', 'landing'))->assertOk();

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.page-contents.update', 'landing'), [
                'sections' => [
                    $title->id => 'Feed a Community Today',
                    $subtitle->id => 'Fresh produce from verified local vendors.',
                ],
            ])
            ->assertRedirect(route('admin.page-contents.edit', 'landing'));

        $this->assertDatabaseHas('page_contents', [
            'id' => $title->id,
            'value' => 'Feed a Community Today',
        ]);

        $this->get('/')->assertOk()->assertSee('Feed a Community Today');
        $this->get('/')->assertSee('Fresh produce from verified local vendors.');
    }

    public function test_disabling_a_page_hides_it_from_the_public(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.page-contents.seed'));

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.page-contents.toggle', 'team'))
            ->assertRedirect();

        $this->assertFalse(PageContent::isPageActive('team'));

        $this->get('/team')->assertOk()->assertSee('Page Currently Unavailable');

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.page-contents.toggle', 'team'))
            ->assertRedirect();

        $this->assertTrue(PageContent::isPageActive('team'));
        $this->get('/team')->assertOk()->assertDontSee('Page Currently Unavailable');
    }

    public function test_a_partial_submission_cannot_blank_out_the_other_sections(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.page-contents.seed'));

        $title = PageContent::where('page', 'gallery')->where('section', 'page_title')->firstOrFail();
        $subtitle = PageContent::where('page', 'gallery')->where('section', 'page_subtitle')->firstOrFail();

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.page-contents.update', 'gallery'), [
                'sections' => [$subtitle->id => 'Only the subtitle was sent'],
            ]);

        $this->assertSame('Our Gallery', $title->fresh()->value, 'the untouched section was wiped');
        $this->assertSame('Only the subtitle was sent', $subtitle->fresh()->value);
    }

    public function test_clearing_a_field_falls_back_to_the_view_default(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.page-contents.seed'));

        $title = PageContent::where('page', 'landing')->where('section', 'hero_title')->firstOrFail();

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.page-contents.update', 'landing'), ['sections' => [$title->id => '']]);

        $this->assertNull($title->fresh()->value);
        $this->get('/')->assertOk();
    }
}

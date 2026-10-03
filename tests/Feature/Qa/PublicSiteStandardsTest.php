<?php

namespace Tests\Feature\Qa;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public-site standards regression suite.
 *
 * Locks in the SEO, accessibility and security work done on the marketing
 * pages: one h1, canonical/description/OG/Twitter metadata, a working skip
 * link, no leaked PHP errors, and the security headers on every surface.
 */
class PublicSiteStandardsTest extends TestCase
{
    use RefreshDatabase;

    private const LEAKS = [
        'Whoops',
        'SQLSTATE',
        'Undefined variable',
        'Call to a member function',
        'Call to undefined method',
        'Too few arguments',
        'htmlspecialchars(): Argument',
        'ParseError',
    ];

    /**
     * @return array<int, array{0: string, 1: string}> route name => expected single h1 text prefix
     */
    public static function marketingPages(): array
    {
        return [
            ['landing', 'Join the FoodChain Marketplace'],
            ['gallery', 'Available Stock'],
            ['team', 'Our Team'],
            ['sponsor', 'Our Esteemed Sponsors'],
            ['supplier', 'Our Esteemed Suppliers'],
            ['rider', 'Become a Dispatch Rider'],
            ['csr', 'Our CSR Commitment'],
            ['franchise', 'Join the FCCEI Franchise Network'],
            ['pages.privacy', 'Privacy Policy'],
            ['pages.terms', 'Terms of Service'],
            ['pages.cookies', 'Cookie Policy'],
            ['pages.faq', 'Frequently Asked Questions'],
            ['blog', 'Our Blog'],
            ['login', 'Welcome Back'],
            ['register', 'Create Your Account'],
        ];
    }

    private function cleanHtml(string $html): string
    {
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);

        return (string) $html;
    }

    public function test_every_marketing_page_renders_cleanly(): void
    {
        foreach (self::marketingPages() as [$route, $expect]) {
            $response = $this->get(route($route));

            $response->assertOk();
            foreach (self::LEAKS as $needle) {
                $this->assertStringNotContainsString($needle, $response->getContent(), "{$route} leaked {$needle}.");
            }
            $this->assertStringContainsString('</html>', $response->getContent(), "{$route} is not a full page.");
        }
    }

    public function test_every_marketing_page_has_exactly_one_h1(): void
    {
        foreach (self::marketingPages() as [$route, $heading]) {
            $html = $this->cleanHtml($this->get(route($route))->getContent());

            $this->assertSame(1, substr_count($html, '<h1'), "{$route} does not have exactly one h1.");
            $this->assertStringContainsString($heading, strip_tags($html), "{$route} h1 is not \"{$heading}\".");
        }
    }

    public function test_every_page_ships_canonical_and_social_metadata(): void
    {
        foreach (self::marketingPages() as [$route, $expect]) {
            $response = $this->get(route($route));

            foreach ([
                '<link rel="canonical"',
                '<meta name="description"',
                '<meta property="og:title"',
                '<meta property="og:description"',
                '<meta name="twitter:card"',
            ] as $tag) {
                $this->assertStringContainsString($tag, $response->getContent(), "{$route} is missing {$tag}.");
            }

            $this->assertSame(1, substr_count($response->getContent(), '<meta name="description"'), "{$route} has duplicate descriptions.");
        }
    }

    public function test_marketing_pages_have_a_working_skip_link(): void
    {
        foreach ([
            ['landing', '#main-content'],
            ['gallery', '#main-content'],
            ['team', '#main-content'],
            ['sponsor', '#main-content'],
            ['supplier', '#main-content'],
            ['rider', '#main-content'],
            ['csr', '#about'],
            ['franchise', '#home'],
            ['pages.privacy', '#main-content'],
        ] as [$route, $target]) {
            $response = $this->get(route($route));

            $response->assertSee('Skip to main content', false)
                ->assertSee('href="'.$target.'"', false)
                ->assertSee('id="'.substr($target, 1).'"', false);
        }
    }

    public function test_security_headers_are_sent_on_public_pages(): void
    {
        foreach (self::marketingPages() as [$route, $expect]) {
            $response = $this->get(route($route));

            $response->assertHeader('X-Frame-Options', 'DENY')
                ->assertHeader('X-Content-Type-Options', 'nosniff')
                ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
                ->assertHeader('Permissions-Policy')
                ->assertHeader('Content-Security-Policy');
        }
    }

    public function test_private_surfaces_are_noindexed_and_public_ones_are_not(): void
    {
        // Public surfaces must stay indexable.
        foreach (['landing', 'gallery', 'team', 'blog', 'csr', 'pages.privacy'] as $route) {
            $this->get(route($route))->assertHeaderMissing('X-Robots-Tag');
        }

        // Auth + account surfaces must be hidden from crawlers.
        $this->get(route('login'))->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get(route('register'))->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user)->get(route('dashboard'))->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->actingAs($user)->get(url('/marketplace/cart'))->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_sitemap_and_robots_are_served(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeaderContains('Content-Type', 'application/xml');

        $robots = $this->get(route('robots'))->assertOk()->getContent();

        $this->assertStringContainsString('Sitemap:', $robots);
        foreach (['/admin', '/dashboard', '/login', '/marketplace', '/vendor'] as $path) {
            $this->assertStringContainsString('Disallow: '.$path, $robots);
        }
    }

    public function test_blog_details_page_meets_standards(): void
    {
        $post = Post::create([
            'title' => 'Standards Audit Post',
            'category' => 'Community Updates',
            'excerpt' => 'Seeded so the article view renders with real content.',
            'body' => 'A body paragraph for the standards audit.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('blog_details', $post->slug));

        $response->assertOk();

        $html = $this->cleanHtml($response->getContent());

        $this->assertSame(1, substr_count($html, '<h1'), 'blog_details does not have exactly one h1.');
        $this->assertStringContainsString('<link rel="canonical"', $response->getContent());
        $this->assertStringContainsString('application/ld+json', $response->getContent());
        $this->assertStringContainsString('Skip to main content', $response->getContent(), false);
        $this->assertStringContainsString('id="main-content"', $response->getContent());
    }
}
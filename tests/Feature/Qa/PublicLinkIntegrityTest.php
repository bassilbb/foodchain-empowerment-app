<?php

namespace Tests\Feature\Qa;

use App\Models\Marketplace\Brand;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\NigerianState;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Crawls the public site and proves every internal link and form target resolves.
 *
 * The other public-site suites check each page in isolation (one h1, canonical,
 * headers). Nothing checked the *edges*: a nav entry, footer link or "read more"
 * target that 404s, or a form posting to a route that no longer exists. Those
 * are invisible in a page-level assertion but are exactly what a visitor hits.
 */
class PublicLinkIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /** Static extensions that are served from public/ or storage, not a route. */
    private const ASSET_EXTENSIONS = [
        'css', 'js', 'mjs', 'map', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp',
        'avif', 'bmp', 'ico', 'woff', 'woff2', 'ttf', 'otf', 'eot', 'pdf',
        'mp4', 'webm', 'mp3', 'zip', 'csv',
    ];

    /** Paths that need a payment reference to be meaningful. */
    private const SKIP_PATHS = [
        '/payment/callback',
    ];

    private Post $post;

    private Vendor $vendor;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        NigerianState::create([
            'name' => 'Lagos',
            'code' => 'LA',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->post = Post::create([
            'title' => 'Link Integrity Post',
            'category' => 'Community Updates',
            'excerpt' => 'Seeded so the article page exposes real outbound links.',
            'body' => 'Body copy for the link crawl.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $vendorUser = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        $this->vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'name' => 'Crawl Farms',
            'slug' => 'crawl-farms',
            'business_name' => 'Crawl Farms Ltd',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        $category = Category::create([
            'name' => 'Grains',
            'slug' => 'grains',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Crawl Brand',
            'slug' => 'crawl-brand',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'vendor_id' => $this->vendor->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Crawl Rice 5kg',
            'slug' => 'crawl-rice-5kg',
            'sku' => Product::generateSku(),
            'price' => 12000,
            'stock_quantity' => 20,
            'low_stock_threshold' => 3,
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
    }

    /**
     * Every public surface a visitor can land on, including the dynamic ones
     * that need seeded records.
     *
     * @return array<string, string> label => url
     */
    private function entryPages(): array
    {
        return [
            'landing' => route('landing'),
            'gallery' => route('gallery'),
            'team' => route('team'),
            'sponsor' => route('sponsor'),
            'supplier' => route('supplier'),
            'rider' => route('rider'),
            'csr' => route('csr'),
            'franchise' => route('franchise'),
            'blog index' => route('blog'),
            'blog post' => route('blog_details', $this->post->slug),
            'privacy' => route('pages.privacy'),
            'terms' => route('pages.terms'),
            'cookies' => route('pages.cookies'),
            'compliance' => route('pages.compliance'),
            'help' => route('pages.help'),
            'faq' => route('pages.faq'),
            'community' => route('pages.community'),
            'affiliate' => route('pages.affiliate'),
            'login' => route('login'),
            'register' => route('register'),
            'forgot password' => route('password.request'),
            'shop home' => route('marketplace.home'),
            'shop all' => route('marketplace.shop'),
            'shop category' => route('marketplace.category', ['category' => 'grains']),
            'shop brand' => route('marketplace.brand', ['brand' => 'crawl-brand']),
            'shop vendors' => route('marketplace.vendors'),
            'shop vendor' => route('marketplace.vendor', ['vendor' => 'crawl-farms']),
            'shop product' => route('marketplace.product', ['product' => 'crawl-rice-5kg']),
            'shop search' => route('marketplace.search', ['q' => 'Rice']),
            'flash sale' => route('marketplace.flash-sale'),
        ];
    }

    /**
     * Pull internal links and form targets out of rendered HTML.
     *
     * Script and style bodies are removed first: JS template literals such as
     * `/shop/product/${p.slug}` look like links to a regex but are never
     * resolved by a browser until a slug is substituted at runtime.
     */
    private function extractInternalTargets(string $html): array
    {
        $html = (string) preg_replace('#<(script|style|template)\b[^>]*>.*?</\1>#is', '', $html);

        preg_match_all('/(?:href|action|src)\s*=\s*(["\'])(.*?)\1/i', $html, $matches);

        $targets = [];

        foreach ($matches[2] as $raw) {
            $url = html_entity_decode(trim($raw), ENT_QUOTES | ENT_HTML5);

            if ($url === '' || str_starts_with($url, '#')) {
                continue;
            }

            if (preg_match('#^(mailto|tel|javascript|data|blob):#i', $url)) {
                continue;
            }

            // Absolute URLs only count when they point at this application.
            if (preg_match('#^https?://#i', $url)) {
                $path = parse_url($url, PHP_URL_PATH);
                $host = parse_url($url, PHP_URL_HOST);
                if ($host !== 'localhost') {
                    continue;
                }
                $url = $path ?: '/';
            }

            if (! str_starts_with($url, '/')) {
                continue;
            }

            // Query strings and fragments never change whether the route exists.
            $url = strtok($url, '?#') ?: '/';

            if (str_ends_with($url, '/') && $url !== '/') {
                $url = rtrim($url, '/');
            }

            $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));

            if ($extension !== '' && in_array($extension, self::ASSET_EXTENSIONS, true)) {
                continue;
            }

            if (in_array($url, self::SKIP_PATHS, true)) {
                continue;
            }

            $targets[$url] = true;
        }

        return array_keys($targets);
    }

    public function test_every_internal_link_and_form_target_on_the_public_site_resolves(): void
    {
        $checked = [];
        $broken = [];

        foreach ($this->entryPages() as $label => $url) {
            $response = $this->get($url);
            $status = $response->getStatusCode();

            // A 3xx is acceptable for an entry page (e.g. /shop/flash-sale
            // redirects to the shop when no sale is live); 404/5xx is not.
            if ($status === 404 || $status >= 500) {
                $broken[] = sprintf('%s (%s) itself returned %d', $label, $url, $status);

                continue;
            }

            foreach ($this->extractInternalTargets($response->getContent()) as $target) {
                if (isset($checked[$target])) {
                    continue;
                }

                $checked[$target] = true;

                $status = $this->get($target)->getStatusCode();

                // 3xx is fine: private pages legitimately redirect to login.
                if ($status === 404 || $status >= 500) {
                    $broken[] = sprintf('%s (%s) -> %s returned %d', $label, $url, $target, $status);
                }
            }
        }

        // Guard against the crawler silently rotting: if the extractor breaks,
        // the run still "passes" while checking almost nothing.
        $this->assertGreaterThan(
            25,
            count($checked),
            'The crawl found suspiciously few links; the extractor is probably broken.'
        );

        // The extractor normalises every target to a bare path, so compare
        // paths rather than full URLs.
        $mustReach = array_map(
            fn (string $url): string => rtrim((string) parse_url($url, PHP_URL_PATH), '/') ?: '/',
            [
                route('marketplace.shop'),
                route('marketplace.home'),
                route('marketplace.cart.index'),
                route('login'),
                route('register'),
            ]
        );

        foreach ($mustReach as $path) {
            $this->assertContains(
                $path,
                array_keys($checked),
                "The crawl never reached {$path}; navigation coverage has regressed."
            );
        }

        $this->assertSame([], $broken, "Broken internal links:\n".implode("\n", $broken));
    }

    public function test_the_storefront_search_and_autocomplete_endpoints_answer(): void
    {
        $this->get(route('marketplace.search', ['q' => 'Rice']))->assertOk()->assertSee('Crawl Rice 5kg');

        $this->getJson(route('marketplace.autocomplete', ['q' => 'Crawl']))
            ->assertOk()
            ->assertJsonPath('products.0.name', 'Crawl Rice 5kg');

        // A blank search must send the visitor somewhere useful, not nowhere.
        $this->get(route('marketplace.search'))->assertRedirect(route('marketplace.shop'));
    }

    public function test_an_empty_search_term_does_not_break_autocomplete(): void
    {
        $this->getJson(route('marketplace.autocomplete', ['q' => 'a']))
            ->assertOk()
            ->assertExactJson(['products' => [], 'categories' => [], 'brands' => []]);
    }

    public function test_public_images_all_carry_alt_text(): void
    {
        $missing = [];

        foreach (array_keys($this->entryPages()) as $label) {
            $html = $this->get($this->entryPages()[$label])->getContent();

            // Only rendered markup; commented-out templates must not count.
            $html = (string) preg_replace('/<!--.*?-->/s', '', $html);

            preg_match_all('/<img\b[^>]*>/i', $html, $images);

            foreach ($images[0] as $image) {
                // A decorative image is allowed an empty alt; a missing one is not.
                if (! preg_match('/\balt\s*=\s*(["\']).*?\1/i', $image)) {
                    $missing[] = sprintf('%s: %s', $label, $image);
                }
            }
        }

        $this->assertSame([], $missing, "Images without an alt attribute:\n".implode("\n", $missing));
    }

    public function test_the_footer_reaches_the_legal_and_help_pages(): void
    {
        $html = $this->get(route('landing'))->getContent();

        foreach ([
            route('pages.privacy'),
            route('pages.terms'),
            route('pages.cookies'),
            route('pages.help'),
            route('pages.faq'),
        ] as $expected) {
            $this->assertStringContainsString(
                'href="'.$expected.'"',
                $html,
                'The landing footer does not link to '.$expected
            );
        }
    }

    public function test_public_forms_post_to_real_endpoints_with_csrf_tokens(): void
    {
        $forms = [
            route('landing') => [route('landing.sponsor'), route('landing.contact'), route('landing.newsletter')],
            route('sponsor') => [route('landing.sponsor')],
            route('supplier') => [route('landing.supplier')],
            route('rider') => [route('landing.rider')],
            route('csr') => [route('landing.contact')],
        ];

        foreach ($forms as $page => $endpoints) {
            $html = (string) preg_replace('/<!--.*?-->/s', '', $this->get($page)->getContent());

            foreach ($endpoints as $endpoint) {
                $this->assertMatchesRegularExpression(
                    '/<form\b[^>]*action="'.preg_quote($endpoint, '/').'"/i',
                    $html,
                    "The form posting to {$endpoint} is missing from {$page}."
                );
            }

            // Every landing form must carry a CSRF token.
            $this->assertStringContainsString('name="_token"', $html, "{$page} has a form without a CSRF token.");
        }
    }

    /**
     * A form that posts nowhere is a silent dead end: the visitor fills it in,
     * the page reloads, and the enquiry evaporates. Every public form must
     * therefore have a real method + action, not rely on the browser default.
     */
    public function test_no_public_page_renders_a_form_that_posts_nowhere(): void
    {
        $offenders = [];

        foreach (array_keys($this->entryPages()) as $label) {
            $url = $this->entryPages()[$label];
            $html = (string) preg_replace('/<!--.*?-->/s', '', $this->get($url)->getContent());

            preg_match_all('/<form\b[^>]*>/i', $html, $forms);

            foreach ($forms[0] as $form) {
                if (! preg_match('/\baction\s*=\s*(["\'])(.*?)\1/i', $form, $action)) {
                    $offenders[] = "{$label} ({$url}): a form with no action — {$form}";

                    continue;
                }

                $target = html_entity_decode($action[2], ENT_QUOTES | ENT_HTML5);

                if (str_starts_with($target, '#') || $target === '') {
                    $offenders[] = "{$label} ({$url}): a form posting to {$target} — {$form}";
                }
            }
        }

        $this->assertSame([], $offenders, "Forms that submit nowhere:\n".implode("\n", $offenders));
    }

    /**
     * A redirect that flashes "thank you" is useless if the page it lands on
     * never renders the message. Submit each public form, follow the redirect,
     * and require the visitor to actually see the result on the page.
     *
     * @dataProvider publicFormSubmissions
     */
    public function test_a_submitted_public_form_shows_the_result_on_the_page(string $pageRoute, string $endpointRoute, array $payload, string $expected): void
    {
        Mail::fake();

        $page = route($pageRoute);

        // from() sets the Referer so back() lands the visitor on the page they
        // submitted from, which is the whole point of redirect-after-post.
        $this->from($page)
            ->post(route($endpointRoute), $payload)
            ->assertRedirect($page)
            ->assertSessionHas('success', $expected);

        // The message has to survive the redirect into rendered HTML, not just
        // sit in the session.
        $this->get($page)->assertOk()->assertSee($expected);
    }

    /**
     * Data providers run before the application is booted, so they may only
     * hand back route *names*; the route() helper is resolved inside the test.
     *
     * @return array<string, array{0: string, 1: string, 2: array, 3: string}>
     */
    public static function publicFormSubmissions(): array
    {
        return [
            'sponsor' => [
                'sponsor', 'landing.sponsor',
                ['name' => 'Ada Sponsor', 'organization' => 'Ada Ltd', 'email' => 'ada@example.org'],
                'Thank you for your sponsorship interest! We will contact you shortly.',
            ],
            'supplier' => [
                'supplier', 'landing.supplier',
                ['name' => 'Bo Supplier', 'organization' => 'Bo Foods', 'email' => 'bo@example.org', 'message' => 'We supply tomatoes.'],
                'Thank you for registering as a supplier! We will review your inquiry.',
            ],
            'rider' => [
                'rider', 'landing.rider',
                ['name' => 'Cy Rider', 'phone' => '08012345678', 'email' => 'cy@example.org', 'vehicle' => 'Motorbike', 'address' => '12 Test Street'],
                'Registration submitted! We will contact you shortly.',
            ],
            'newsletter' => [
                'landing', 'landing.newsletter',
                ['email' => 'sub@example.org'],
                'You are subscribed! Watch your inbox for marketplace updates.',
            ],
            'csr contact' => [
                'csr', 'landing.contact',
                ['name' => 'Dee Visitor', 'email' => 'dee@example.org', 'subject' => 'CSR page enquiry', 'message' => 'I would like to partner.'],
                'Thank you for your message! We have received it and will get back to you soon.',
            ],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: array}>
     */
    public static function publicFormPages(): array
    {
        return [
            'sponsor' => ['sponsor', 'landing.sponsor', ['name' => '', 'organization' => '', 'email' => 'not-an-email']],
            'supplier' => ['supplier', 'landing.supplier', ['name' => '', 'organization' => '', 'email' => 'not-an-email', 'message' => '']],
            'rider' => ['rider', 'landing.rider', ['name' => '', 'phone' => '', 'email' => 'not-an-email', 'vehicle' => '', 'address' => '']],
            'newsletter' => ['landing', 'landing.newsletter', ['email' => 'not-an-email']],
            'csr contact' => ['csr', 'landing.contact', ['name' => '', 'email' => 'not-an-email', 'message' => '']],
        ];
    }

    /**
     * Validation failures must be reported on the page too, not swallowed.
     *
     * @dataProvider publicFormPages
     */
    public function test_a_rejected_public_submission_shows_the_error_on_the_page(string $pageRoute, string $endpointRoute, array $payload): void
    {
        $page = route($pageRoute);

        $this->from($page)
            ->post(route($endpointRoute), $payload)
            ->assertRedirect($page)
            ->assertSessionHasErrors();

        // The errors must be rendered where the visitor actually lands.
        $this->followingRedirects()
            ->from($page)
            ->post(route($endpointRoute), $payload)
            ->assertSee('Please fix the following:', false);
    }
}

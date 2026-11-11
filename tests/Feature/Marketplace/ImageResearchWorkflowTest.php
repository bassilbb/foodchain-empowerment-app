<?php

namespace Tests\Feature\Marketplace;

use App\Models\AdminUser;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductImage;
use App\Models\Marketplace\Vendor;
use App\Models\ProductImageResearch;
use App\Models\User;
use App\Services\Images\Contracts\ImageSearchProvider;
use App\Services\Images\ImageResearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The image workflow must never invent, scrape or auto-approve an image, and a
 * failed download must never damage the images a product already has.
 */
class ImageResearchWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Vendor $vendor;

    private string $testDirectory = 'images/test-image-research';

    private AdminUser $admin;

    private ?int $categoryId = null;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'image_research.vendor_id' => 0,
            'image_research.directory' => $this->testDirectory,
            'image_research.min_width' => 100,
            'image_research.min_height' => 100,
        ]);

        $owner = User::factory()->create(['is_active' => true]);

        $this->vendor = Vendor::create([
            'user_id' => $owner->id,
            'name' => 'Image Research Store',
            'slug' => 'image-research-store-'.uniqid(),
            'business_name' => 'Image Research Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        config(['image_research.vendor_id' => $this->vendor->id]);

        $this->admin = AdminUser::create([
            'name' => 'Image Admin',
            'email' => 'image-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        $directory = public_path($this->testDirectory);

        if (is_dir($directory)) {
            foreach (glob($directory.'/*') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($directory);
        }

        parent::tearDown();
    }

    private function makeProduct(string $sku, array $overrides = []): Product
    {
        if ($this->categoryId === null) {
            $this->categoryId = Category::create([
                'name' => 'Image Category '.uniqid(),
                'slug' => 'image-category-'.uniqid(),
                'is_active' => true,
            ])->id;
        }

        return Product::create(array_merge([
            'vendor_id' => $this->vendor->id,
            'category_id' => $this->categoryId,
            'name' => 'Product '.$sku,
            'slug' => 'product-'.strtolower($sku).'-'.uniqid(),
            'sku' => $sku,
            'price' => 5000,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'is_active' => true,
            'approval_status' => 'approved',
        ], $overrides));
    }

    private function pngBinary(): string
    {
        $image = imagecreatetruecolor(300, 300);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 120, 60));

        ob_start();
        imagepng($image);
        $binary = ob_get_clean();
        imagedestroy($image);

        return $binary;
    }

    private function fakeProvider(array $candidates): void
    {
        $this->app->instance(ImageSearchProvider::class, new class($candidates) implements ImageSearchProvider
        {
            public function __construct(private array $candidates)
            {
            }

            public function search(string $query, int $limit = 5): array
            {
                return $this->candidates;
            }

            public function isConfigured(): bool
            {
                return true;
            }

            public function name(): string
            {
                return 'fake';
            }
        });
    }

    public function test_audit_only_creates_rows_for_products_without_real_images(): void
    {
        $covered = $this->makeProduct('OPEY-T001');
        ProductImage::create([
            'product_id' => $covered->id,
            'image_path' => 'images/products/covered.webp',
            'is_primary' => true,
            'is_placeholder' => false,
            'sort_order' => 0,
        ]);

        $placeholderOnly = $this->makeProduct('OPEY-T002');
        ProductImage::create([
            'product_id' => $placeholderOnly->id,
            'image_path' => 'images/products/placeholder.svg',
            'is_primary' => true,
            'is_placeholder' => true,
            'sort_order' => 0,
        ]);

        $missing = $this->makeProduct('OPEY-T003');

        $result = app(ImageResearchService::class)->audit($this->vendor->id);

        $this->assertSame(2, $result['created']);
        $this->assertSame(3, $result['total']);
        $this->assertSame(1, $result['with_images']);
        $this->assertSame(2, $result['outstanding']);

        $this->assertSame(0, ProductImageResearch::where('product_id', $covered->id)->count());
        $this->assertSame(1, ProductImageResearch::where('product_id', $placeholderOnly->id)->count());
        $this->assertSame(1, ProductImageResearch::where('product_id', $missing->id)->count());

        // Idempotent: running it again does not duplicate rows.
        $second = app(ImageResearchService::class)->audit($this->vendor->id);
        $this->assertSame(0, $second['created']);
        $this->assertSame(2, ProductImageResearch::count());
    }

    public function test_research_stores_a_candidate_but_never_auto_approves_or_attaches(): void
    {
        $product = $this->makeProduct('OPEY-T010');
        app(ImageResearchService::class)->audit($this->vendor->id);

        $this->fakeProvider([[
            'image_url' => 'https://example.test/packaging.png',
            'source_url' => 'https://example.test/product',
            'source_site' => 'example.test',
            'title' => 'Product packaging',
        ]]);

        $record = ProductImageResearch::where('product_id', $product->id)->first();
        app(ImageResearchService::class)->research($record);

        $record->refresh();
        $this->assertSame(ProductImageResearch::STATUS_PENDING_VERIFICATION, $record->status);
        $this->assertSame('unverified', $record->verification_status);
        $this->assertSame('https://example.test/packaging.png', $record->candidate_image_url);
        $this->assertSame(0, $product->images()->count());
    }

    public function test_research_marks_not_found_when_the_provider_returns_nothing(): void
    {
        $product = $this->makeProduct('OPEY-T011');
        app(ImageResearchService::class)->audit($this->vendor->id);
        $this->fakeProvider([]);

        $record = ProductImageResearch::where('product_id', $product->id)->first();
        app(ImageResearchService::class)->research($record);

        $this->assertSame(ProductImageResearch::STATUS_NOT_FOUND, $record->fresh()->status);
        $this->assertSame(0, $product->images()->count());
    }

    public function test_a_candidate_cannot_be_downloaded_until_it_is_verified(): void
    {
        $product = $this->makeProduct('OPEY-T012');
        app(ImageResearchService::class)->audit($this->vendor->id);

        $record = ProductImageResearch::where('product_id', $product->id)->first();
        $record->update([
            'status' => ProductImageResearch::STATUS_PENDING_VERIFICATION,
            'candidate_image_url' => 'https://example.test/packaging.png',
        ]);

        $result = app(ImageResearchService::class)->download($record);

        $this->assertFalse($result['success']);
        $this->assertSame(0, $product->images()->count());
    }

    public function test_failed_download_leaves_existing_product_images_untouched(): void
    {
        $product = $this->makeProduct('OPEY-T013');

        app(ImageResearchService::class)->audit($this->vendor->id);
        $record = ProductImageResearch::where('product_id', $product->id)->first();

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'images/products/existing.webp',
            'is_primary' => true,
            'is_placeholder' => false,
            'sort_order' => 0,
        ]);

        $record->update([
            'status' => ProductImageResearch::STATUS_VERIFIED,
            'verification_status' => 'verified',
            'candidate_image_url' => 'https://example.test/missing.png',
        ]);

        Http::fake([
            '*' => Http::response('not an image', 200, ['Content-Type' => 'text/plain']),
        ]);

        $result = app(ImageResearchService::class)->download($record, true);

        $this->assertFalse($result['success']);
        $this->assertSame(ProductImageResearch::DOWNLOAD_FAILED, $record->fresh()->download_status);
        $this->assertNotNull($record->fresh()->download_error);

        $images = $product->images()->get();
        $this->assertCount(1, $images);
        $this->assertSame('images/products/existing.webp', $images->first()->image_path);
    }

    public function test_verified_download_attaches_the_image_to_the_correct_product_only(): void
    {
        $product = $this->makeProduct('OPEY-T014');
        $other = $this->makeProduct('OPEY-T015');

        app(ImageResearchService::class)->audit($this->vendor->id);
        $record = ProductImageResearch::where('product_id', $product->id)->first();
        $record->update([
            'status' => ProductImageResearch::STATUS_VERIFIED,
            'verification_status' => 'verified',
            'candidate_image_url' => 'https://example.test/packaging.png',
        ]);

        Http::fake([
            '*' => Http::response($this->pngBinary(), 200, ['Content-Type' => 'image/png']),
        ]);

        $result = app(ImageResearchService::class)->download($record);

        $this->assertTrue($result['success'], $result['message'] ?? '');
        $this->assertFileExists(public_path($result['path']));

        $image = $product->images()->first();
        $this->assertNotNull($image);
        $this->assertSame($result['path'], $image->image_path);
        $this->assertFalse($image->is_placeholder);
        $this->assertTrue($image->is_primary);
        $this->assertStringStartsWith('images/test-image-research/opeyemi-opey-t014-', $image->image_path);

        $this->assertSame(0, $other->images()->count());
        $this->assertSame(ProductImageResearch::STATUS_DOWNLOADED, $record->fresh()->status);
    }

    public function test_reject_requires_a_reason_and_keeps_the_product_untouched(): void
    {
        $product = $this->makeProduct('OPEY-T016');
        app(ImageResearchService::class)->audit($this->vendor->id);
        $record = ProductImageResearch::where('product_id', $product->id)->first();
        $record->update(['candidate_image_url' => 'https://example.test/packaging.png']);

        $service = app(ImageResearchService::class);

        try {
            $service->reject($record, $this->admin->id, '   ');
            $this->fail('Rejecting without a reason should throw.');
        } catch (\DomainException $e) {
            $this->assertSame('A rejection reason is required.', $e->getMessage());
        }

        $service->reject($record, $this->admin->id, 'Wrong pack size.');

        $record->refresh();
        $this->assertSame(ProductImageResearch::STATUS_REJECTED, $record->status);
        $this->assertSame('Wrong pack size.', $record->rejection_reason);
        $this->assertSame(0, $product->images()->count());
    }

    public function test_admin_can_review_verify_and_reject_from_the_dashboard(): void
    {
        $product = $this->makeProduct('OPEY-T017');
        app(ImageResearchService::class)->audit($this->vendor->id);
        $record = ProductImageResearch::where('product_id', $product->id)->first();
        $record->update([
            'status' => ProductImageResearch::STATUS_PENDING_VERIFICATION,
            'candidate_image_url' => 'https://example.test/packaging.png',
            'candidate_source_site' => 'example.test',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.image-research.index'))
            ->assertOk()
            ->assertSee('OPEY-T017');

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.image-research.show', $record))
            ->assertOk()
            ->assertSee('https://example.test/packaging.png');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.image-research.verify', $record), ['notes' => 'Confirmed'])
            ->assertRedirect();

        $this->assertSame(ProductImageResearch::STATUS_VERIFIED, $record->fresh()->status);
        $this->assertSame($this->admin->id, $record->fresh()->verified_by);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.image-research.reject', $record), ['reason' => 'Wrong variant'])
            ->assertRedirect();

        $this->assertSame(ProductImageResearch::STATUS_REJECTED, $record->fresh()->status);
    }

    public function test_customer_cannot_access_image_research(): void
    {
        $customer = User::factory()->create(['is_active' => true]);

        $this->actingAs($customer)
            ->get(route('admin.image-research.index'))
            ->assertRedirect(route('admin.login'));
    }
}

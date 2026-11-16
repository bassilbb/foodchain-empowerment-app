<?php

namespace Tests\Feature\Marketplace;

use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\ProductImageResearch;
use App\Models\User;
use App\Services\Images\ImageResearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * End-to-end behaviour of the catalogue image sourcing commands: what gets
 * written to disk, what gets auto-attached, and what is left for a human.
 */
class CatalogueImageImportTest extends TestCase
{
    use RefreshDatabase;

    private Vendor $vendor;

    private string $cataloguePath;

    private string $imageDirectory = 'images/test-catalogue-import';

    private ?int $categoryId = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cataloguePath = storage_path('app/test-image-catalogue-'.uniqid().'.json');

        $owner = User::factory()->create(['is_active' => true]);

        $this->vendor = Vendor::create([
            'user_id' => $owner->id,
            'name' => 'Catalogue Store',
            'slug' => 'catalogue-store-'.uniqid(),
            'business_name' => 'Catalogue Business',
            'status' => 'approved',
            'commission_rate' => 10,
        ]);

        config([
            'image_research.vendor_id' => $this->vendor->id,
            'image_research.directory' => $this->imageDirectory,
            'image_research.catalogue.path' => $this->cataloguePath,
            'image_research.catalogue.base_url' => 'https://catalogue.test',
            'image_research.catalogue.min_score' => 0.75,
            'image_research.catalogue.min_margin' => 0.05,
            'image_research.catalogue.review_score' => 0.60,
            'image_research.catalogue.min_width' => 100,
            'image_research.catalogue.min_height' => 100,
        ]);
    }

    protected function tearDown(): void
    {
        @unlink($this->cataloguePath);

        $directory = public_path($this->imageDirectory);

        if (is_dir($directory)) {
            foreach (glob($directory.'/*') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($directory);
        }

        parent::tearDown();
    }

    private function makeProduct(string $sku, string $name): Product
    {
        if ($this->categoryId === null) {
            $this->categoryId = Category::create([
                'name' => 'Catalogue Category '.uniqid(),
                'slug' => 'catalogue-category-'.uniqid(),
                'is_active' => true,
            ])->id;
        }

        return Product::create([
            'vendor_id' => $this->vendor->id,
            'category_id' => $this->categoryId,
            'name' => $name,
            'slug' => 'product-'.strtolower($sku).'-'.uniqid(),
            'sku' => $sku,
            'price' => 5000,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
    }

    private function pngBinary(int $size = 300): string
    {
        $image = imagecreatetruecolor($size, $size);
        imagefill($image, 0, 0, imagecolorallocate($image, 30, 90, 160));

        ob_start();
        imagepng($image);
        $binary = ob_get_clean();
        imagedestroy($image);

        return $binary;
    }

    /**
     * @param  array<int, array{name: string, slug: string}>  $products
     */
    private function fakeStore(array $products): void
    {
        $payload = array_map(fn (array $product, int $index): array => [
            'id' => $index + 1,
            'name' => $product['name'],
            'slug' => $product['slug'],
            'permalink' => 'https://catalogue.test/product/'.$product['slug'],
            'sku' => null,
            'images' => [[
                'src' => 'https://catalogue.test/uploads/'.$product['slug'].'.png',
                'thumbnail' => 'https://catalogue.test/uploads/'.$product['slug'].'.png',
            ]],
        ], $products, array_keys($products));

        Http::fake([
            'https://catalogue.test/wp-json/wc/store/products*' => Http::response($payload, 200, [
                'X-WP-Total' => (string) count($payload),
                'X-WP-TotalPages' => '1',
            ]),
            'https://catalogue.test/uploads/*' => Http::response($this->pngBinary(), 200, ['Content-Type' => 'image/png']),
        ]);
    }

    public function test_sync_writes_a_catalogue_and_decodes_entities(): void
    {
        $this->fakeStore([
            ['name' => 'Dangote Sugar 1kg', 'slug' => 'dangote-sugar-1kg'],
            ['name' => 'Checkers Custard &amp; Pap 400g', 'slug' => 'checkers-custard-pap-400g'],
        ]);

        $this->artisan('images:sync-catalogue')->assertSuccessful();

        $this->assertFileExists($this->cataloguePath);

        $catalogue = json_decode(File::get($this->cataloguePath), true);

        $this->assertCount(2, $catalogue);
        $this->assertSame('Dangote Sugar 1kg', $catalogue[0]['name']);
        $this->assertSame('Checkers Custard & Pap 400g', $catalogue[1]['name']);
        $this->assertSame('catalogue.test', $catalogue[0]['source_site']);
    }

    public function test_dry_run_writes_nothing_and_downloads_nothing(): void
    {
        $product = $this->makeProduct('OPEY-C001', 'DANGOTE SUGAR 1KG*10');
        app(ImageResearchService::class)->audit($this->vendor->id);

        $this->fakeStore([
            ['name' => 'Dangote Sugar 1kg', 'slug' => 'dangote-sugar-1kg'],
        ]);

        $this->artisan('images:sync-catalogue')->assertSuccessful();
        $this->artisan('images:import-from-site', ['--dry-run' => true])->assertSuccessful();

        $record = ProductImageResearch::where('product_id', $product->id)->first();

        $this->assertSame(ProductImageResearch::STATUS_PENDING_RESEARCH, $record->status);
        $this->assertNull($record->candidate_image_url);
        $this->assertSame(0, $product->images()->count());
    }

    public function test_a_strong_match_is_verified_downloaded_and_attached(): void
    {
        $product = $this->makeProduct('OPEY-C010', 'DANGOTE SUGAR 1KG*10');
        app(ImageResearchService::class)->audit($this->vendor->id);

        $this->fakeStore([
            ['name' => 'Dangote Sugar 1kg', 'slug' => 'dangote-sugar-1kg'],
            ['name' => 'Dangote Sugar 500g', 'slug' => 'dangote-sugar-500g'],
        ]);

        $this->artisan('images:sync-catalogue')->assertSuccessful();
        $this->artisan('images:import-from-site')->assertSuccessful();

        $record = ProductImageResearch::where('product_id', $product->id)->first();
        $record->refresh();

        $this->assertSame(ProductImageResearch::STATUS_DOWNLOADED, $record->status);
        $this->assertSame(ProductImageResearch::DOWNLOAD_DOWNLOADED, $record->download_status);
        $this->assertSame('catalogue-name-match', $record->candidate_match_method);
        $this->assertGreaterThanOrEqual(0.75, $record->candidate_confidence);

        $image = $product->images()->first();
        $this->assertNotNull($image);
        $this->assertFalse($image->is_placeholder);
        $this->assertTrue($image->is_primary);
        $this->assertFileExists(public_path($image->image_path));
    }

    public function test_a_weak_match_is_queued_for_review_and_not_downloaded(): void
    {
        // Two genuine candidates for the same pack size: the margin rule must
        // keep this out of the automatic path.
        $product = $this->makeProduct('OPEY-C011', 'PEAK MILK 350G*12 REFILL');
        app(ImageResearchService::class)->audit($this->vendor->id);

        $this->fakeStore([
            ['name' => 'Peak milk 123 (Refill) (350g)', 'slug' => 'peak-123-350g'],
            ['name' => 'Peak milk 456 (Refil) (350g)', 'slug' => 'peak-456-350g'],
        ]);

        $this->artisan('images:sync-catalogue')->assertSuccessful();
        $this->artisan('images:import-from-site')->assertSuccessful();

        $record = ProductImageResearch::where('product_id', $product->id)->first();
        $record->refresh();

        $this->assertSame(ProductImageResearch::STATUS_PENDING_VERIFICATION, $record->status);
        $this->assertNotNull($record->candidate_image_url);
        $this->assertSame(0, $product->images()->count());
    }

    public function test_a_conflicting_pack_size_is_not_stored_as_a_candidate(): void
    {
        $product = $this->makeProduct('OPEY-C012', 'DANGOTE SUGAR 250g*40');
        app(ImageResearchService::class)->audit($this->vendor->id);

        $this->fakeStore([
            ['name' => 'Dangote Sugar 1kg', 'slug' => 'dangote-sugar-1kg'],
        ]);

        $this->artisan('images:sync-catalogue')->assertSuccessful();
        $this->artisan('images:import-from-site')->assertSuccessful();

        $record = ProductImageResearch::where('product_id', $product->id)->first();
        $record->refresh();

        $this->assertSame(ProductImageResearch::STATUS_PENDING_RESEARCH, $record->status);
        $this->assertNull($record->candidate_image_url);
        $this->assertSame(0, $product->images()->count());
    }

    public function test_a_failed_download_is_retried_on_the_next_run(): void
    {
        $product = $this->makeProduct('OPEY-C013', 'DANGOTE SUGAR 1KG*10');
        app(ImageResearchService::class)->audit($this->vendor->id);

        // First attempt 404s, the retry succeeds.
        $attempts = 0;
        $binary = $this->pngBinary();

        Http::fake([
            'https://catalogue.test/wp-json/wc/store/products*' => Http::response([[
                'id' => 1,
                'name' => 'Dangote Sugar 1kg',
                'slug' => 'dangote-sugar-1kg',
                'permalink' => 'https://catalogue.test/product/dangote-sugar-1kg',
                'images' => [['src' => 'https://catalogue.test/uploads/sugar.png']],
            ]], 200, ['X-WP-Total' => '1', 'X-WP-TotalPages' => '1']),
            'https://catalogue.test/uploads/*' => function () use (&$attempts, $binary) {
                $attempts++;

                return $attempts === 1
                    ? Http::response('nope', 404)
                    : Http::response($binary, 200, ['Content-Type' => 'image/png']);
            },
        ]);

        $this->artisan('images:sync-catalogue')->assertSuccessful();
        $this->artisan('images:import-from-site')->assertFailed();

        $record = ProductImageResearch::where('product_id', $product->id)->first();
        $this->assertSame(ProductImageResearch::DOWNLOAD_FAILED, $record->fresh()->download_status);
        $this->assertSame(0, $product->images()->count());

        // Second run: the image is available again and the row is retried.
        $this->artisan('images:import-from-site')->assertSuccessful();

        $record->refresh();
        $this->assertSame(ProductImageResearch::STATUS_DOWNLOADED, $record->status);
        $this->assertSame(1, $product->images()->count());
    }

    public function test_it_fails_clearly_when_no_catalogue_has_been_synced(): void
    {
        $this->makeProduct('OPEY-C014', 'DANGOTE SUGAR 1KG*10');
        app(ImageResearchService::class)->audit($this->vendor->id);

        $this->artisan('images:import-from-site')
            ->expectsOutputToContain('No synced catalogue found')
            ->assertFailed();
    }

    public function test_an_already_imaged_product_is_never_touched(): void
    {
        $product = $this->makeProduct('OPEY-C015', 'DANGOTE SUGAR 1KG*10');
        $product->images()->create([
            'image_path' => 'images/products/existing.webp',
            'is_primary' => true,
            'is_placeholder' => false,
            'sort_order' => 0,
        ]);

        app(ImageResearchService::class)->audit($this->vendor->id);

        $this->fakeStore([
            ['name' => 'Dangote Sugar 1kg', 'slug' => 'dangote-sugar-1kg'],
        ]);

        $this->artisan('images:sync-catalogue')->assertSuccessful();
        $this->artisan('images:import-from-site')->assertSuccessful();

        $images = $product->images()->get();
        $this->assertCount(1, $images);
        $this->assertSame('images/products/existing.webp', $images->first()->image_path);
    }
}

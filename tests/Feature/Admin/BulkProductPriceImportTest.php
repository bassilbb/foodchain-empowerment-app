<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\PriceImportBatch;
use App\Models\Marketplace\PriceImportItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductPriceHistory;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * End-to-end coverage of the two-stage bulk product price update system:
 * upload -> preview -> confirm -> apply -> audit -> rollback.
 */
class BulkProductPriceImportTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    private Vendor $vendor;

    private Vendor $otherVendor;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = AdminUser::create([
            'name' => 'Bulk Admin',
            'email' => 'bulk-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->vendor = $this->makeVendor('Opeyemi Stores', 'opeyemi');
        $this->otherVendor = $this->makeVendor('Other Supplier', 'other');

        $this->category = Category::create([
            'name' => 'Groceries',
            'slug' => 'groceries',
            'is_active' => true,
        ]);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function makeVendor(string $name, string $slug): Vendor
    {
        $user = User::factory()->create([
            'is_active' => true,
            'membership_expires_at' => now()->addYear(),
        ]);

        return Vendor::create([
            'user_id' => $user->id,
            'name' => $name,
            'slug' => $slug.'-'.uniqid(),
            'business_name' => $name,
            'status' => 'approved',
            'commission_rate' => 10,
        ]);
    }

    private function makeProduct(array $overrides = [], ?Vendor $vendor = null): Product
    {
        $vendor ??= $this->vendor;

        return Product::create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $this->category->id,
            'name' => 'Product '.uniqid(),
            'slug' => 'product-'.uniqid(),
            'sku' => 'SKU-'.strtoupper(uniqid()),
            'price' => 100,
            'stock_quantity' => 25,
            'is_active' => true,
            'approval_status' => 'approved',
            'approved_at' => now(),
        ], $overrides));
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function csv(array $rows): string
    {
        $header = ['sku', 'supplier_product_code', 'barcode', 'product_name', 'category', 'unit', 'price'];
        $lines = [implode(',', $header)];

        foreach ($rows as $row) {
            $lines[] = implode(',', [
                $row['sku'] ?? '',
                $row['supplier_product_code'] ?? '',
                $row['barcode'] ?? '',
                $row['product_name'] ?? '',
                $row['category'] ?? '',
                $row['unit'] ?? '',
                $row['price'] ?? '',
            ]);
        }

        return implode("\n", $lines)."\n";
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function xml(array $rows): string
    {
        $out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<products>\n";

        foreach ($rows as $row) {
            $out .= "  <product>\n";
            foreach ([
                'sku', 'supplier_product_code', 'barcode', 'product_name', 'category', 'unit', 'price',
            ] as $field) {
                $value = htmlspecialchars((string) ($row[$field] ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $out .= "    <{$field}>{$value}</{$field}>\n";
            }
            $out .= "  </product>\n";
        }

        return $out."</products>\n";
    }

    private function uploadCsv(string $content, array $overrides = [], ?Vendor $vendor = null)
    {
        $vendor ??= $this->vendor;

        return $this->actingAs($this->admin, 'admin')->post(
            route('admin.marketplace.price-imports.store'),
            array_merge([
                'vendor_id' => $vendor->id,
                'file' => UploadedFile::fake()->createWithContent('supplier-prices.csv', $content),
            ], $overrides),
        );
    }

    private function uploadXml(string $content, array $overrides = [], ?Vendor $vendor = null)
    {
        $vendor ??= $this->vendor;

        return $this->actingAs($this->admin, 'admin')->post(
            route('admin.marketplace.price-imports.store'),
            array_merge([
                'vendor_id' => $vendor->id,
                'file' => UploadedFile::fake()->createWithContent('supplier-prices.xml', $content),
            ], $overrides),
        );
    }

    private function latestBatch(): PriceImportBatch
    {
        return PriceImportBatch::latest('id')->firstOrFail();
    }

    private function applyBatch(PriceImportBatch $batch, array $overrides = [])
    {
        return $this->actingAs($this->admin, 'admin')->post(
            route('admin.marketplace.price-imports.apply', $batch),
            array_merge(['confirm_review' => 1], $overrides),
        );
    }

    // ------------------------------------------------------------------
    // Preview / matching
    // ------------------------------------------------------------------

    public function test_preview_classifies_increases_decreases_and_unchanged_without_changing_prices(): void
    {
        $increase = $this->makeProduct(['name' => 'Rice 5kg', 'sku' => 'OPEY-0001', 'price' => 100]);
        $decrease = $this->makeProduct(['name' => 'Beans 5kg', 'sku' => 'OPEY-0002', 'price' => 200]);
        $unchanged = $this->makeProduct(['name' => 'Garri 5kg', 'sku' => 'OPEY-0003', 'price' => 300]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0001', 'product_name' => 'Rice 5kg', 'category' => 'Groceries', 'price' => 125.50],
            ['sku' => 'OPEY-0002', 'product_name' => 'Beans 5kg', 'category' => 'Groceries', 'price' => 175],
            ['sku' => 'OPEY-0003', 'product_name' => 'Garri 5kg', 'category' => 'Groceries', 'price' => 300],
        ]))->assertRedirect()->assertSessionHas('success');

        $batch = $this->latestBatch();

        $this->assertSame(PriceImportBatch::STATUS_PREVIEWED, $batch->status);
        $this->assertSame(3, $batch->total_records);
        $this->assertSame(1, $batch->increased_count);
        $this->assertSame(1, $batch->reduced_count);
        $this->assertSame(1, $batch->unchanged_count);

        // Preview must not touch any product.
        $this->assertSame('100.00', $increase->fresh()->price);
        $this->assertSame('200.00', $decrease->fresh()->price);
        $this->assertSame('300.00', $unchanged->fresh()->price);

        $this->assertSame(0, ProductPriceHistory::count());
    }

    public function test_apply_updates_only_the_selling_price_and_records_every_change(): void
    {
        $product = $this->makeProduct([
            'name' => 'Golden Penny Spaghetti',
            'sku' => 'OPEY-0001',
            'price' => 100,
            'discount_price' => 95,
            'member_price' => 90,
            'stock_quantity' => 42,
            'description' => 'Original description',
        ]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0001', 'product_name' => 'Golden Penny Spaghetti', 'category' => 'Groceries', 'price' => 150],
        ]));

        $batch = $this->latestBatch();

        $this->applyBatch($batch)->assertRedirect()->assertSessionHas('success');

        $batch->refresh();
        $product->refresh();

        $this->assertSame(PriceImportBatch::STATUS_COMPLETED, $batch->status);
        $this->assertSame(1, $batch->applied_count);
        $this->assertSame('150.00', $product->price);

        // Untouched fields.
        $this->assertSame('95.00', $product->discount_price);
        $this->assertSame('90.00', $product->member_price);
        $this->assertSame(42, (int) $product->stock_quantity);
        $this->assertSame('Original description', $product->description);
        $this->assertSame('OPEY-0001', $product->sku);

        $history = ProductPriceHistory::where('batch_id', $batch->id)->firstOrFail();
        $this->assertSame(ProductPriceHistory::SOURCE_BULK_IMPORT, $history->source);
        $this->assertSame('100.00', $history->old_price);
        $this->assertSame('150.00', $history->new_price);
        $this->assertSame('50.00', $history->difference);
        $this->assertSame($this->admin->id, (int) $history->admin_user_id);
        $this->assertSame($this->vendor->id, (int) $history->vendor_id);
        $this->assertSame('Golden Penny Spaghetti', $history->product_name);
    }

    public function test_sku_match_wins_over_a_conflicting_name(): void
    {
        $product = $this->makeProduct(['name' => 'Real Product Name', 'sku' => 'OPEY-0001', 'price' => 100]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0001', 'product_name' => 'Completely Different Label', 'category' => 'Groceries', 'price' => 120],
        ]));

        $item = $this->latestBatch()->items()->firstOrFail();

        $this->assertSame(PriceImportItem::STATUS_MATCHED, $item->status);
        $this->assertSame('sku', $item->match_method);
        $this->assertSame($product->id, (int) $item->product_id);
    }

    public function test_supplier_product_code_and_barcode_are_used_when_sku_is_absent(): void
    {
        $byCode = $this->makeProduct([
            'name' => 'Code Product',
            'sku' => 'OPEY-0010',
            'supplier_product_code' => 'SUP-900',
            'price' => 100,
        ]);

        $byBarcode = $this->makeProduct([
            'name' => 'Barcode Product',
            'sku' => 'OPEY-0011',
            'barcode' => '6151100012345',
            'price' => 200,
        ]);

        $this->uploadCsv($this->csv([
            ['supplier_product_code' => 'SUP-900', 'product_name' => 'Code Product', 'price' => 111],
            ['barcode' => '6151100012345', 'product_name' => 'Barcode Product', 'price' => 222],
        ]));

        $items = $this->latestBatch()->items()->orderBy('row_number')->get();

        $this->assertSame('supplier_product_code', $items[0]->match_method);
        $this->assertSame($byCode->id, (int) $items[0]->product_id);
        $this->assertSame('barcode', $items[1]->match_method);
        $this->assertSame($byBarcode->id, (int) $items[1]->product_id);
    }

    public function test_name_and_category_match_is_accepted_when_unambiguous(): void
    {
        $product = $this->makeProduct(['name' => 'Local Honey', 'sku' => 'OPEY-0020', 'price' => 100]);

        $this->uploadCsv($this->csv([
            ['product_name' => 'Local Honey', 'category' => 'Groceries', 'price' => 140],
        ]));

        $item = $this->latestBatch()->items()->firstOrFail();

        $this->assertSame(PriceImportItem::STATUS_MATCHED, $item->status);
        $this->assertSame('name', $item->match_method);
        $this->assertSame($product->id, (int) $item->product_id);
    }

    public function test_ambiguous_names_are_never_auto_matched(): void
    {
        $first = $this->makeProduct(['name' => 'Same Name', 'sku' => 'OPEY-0031', 'price' => 100]);
        $second = $this->makeProduct(['name' => 'Same Name', 'sku' => 'OPEY-0032', 'price' => 200]);

        $this->uploadCsv($this->csv([
            ['product_name' => 'Same Name', 'category' => 'Groceries', 'price' => 999],
        ]));

        $batch = $this->latestBatch();
        $item = $batch->items()->firstOrFail();

        $this->assertSame(PriceImportItem::STATUS_AMBIGUOUS, $item->status);
        $this->assertSame(1, $batch->ambiguous_count);

        $this->applyBatch($batch);

        $this->assertSame('100.00', $first->fresh()->price);
        $this->assertSame('200.00', $second->fresh()->price);
        $this->assertSame(0, ProductPriceHistory::count());
    }

    // ------------------------------------------------------------------
    // Supplier isolation & validation
    // ------------------------------------------------------------------

    public function test_one_supplier_file_never_updates_another_suppliers_product(): void
    {
        $foreign = $this->makeProduct([
            'name' => 'Foreign Product',
            'sku' => 'OTHER-0001',
            'price' => 500,
        ], $this->otherVendor);

        $this->uploadCsv($this->csv([
            ['sku' => 'OTHER-0001', 'product_name' => 'Foreign Product', 'category' => 'Groceries', 'price' => 1],
        ]));

        $batch = $this->latestBatch();
        $item = $batch->items()->firstOrFail();

        $this->assertSame(PriceImportItem::STATUS_INVALID, $item->status);
        $this->assertStringContainsString('another supplier', implode(' ', $item->errors));

        $this->applyBatch($batch);

        $this->assertSame('500.00', $foreign->fresh()->price);
        $this->assertSame(0, ProductPriceHistory::count());
    }

    public function test_invalid_records_are_reported_and_never_applied(): void
    {
        $product = $this->makeProduct(['name' => 'Valid Product', 'sku' => 'OPEY-0040', 'price' => 100]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0040', 'product_name' => 'Valid Product', 'price' => 130],
            ['sku' => 'OPEY-0041', 'product_name' => '', 'price' => 50],                 // missing name
            ['sku' => 'OPEY-0042', 'product_name' => 'No Price'],                        // missing price
            ['sku' => 'OPEY-0043', 'product_name' => 'Negative', 'price' => -10],        // negative
            ['sku' => 'OPEY-0044', 'product_name' => 'Zero', 'price' => 0],              // zero
            ['sku' => 'OPEY-0045', 'product_name' => 'Garbage', 'price' => 'abc'],       // not numeric
            ['sku' => 'OPEY-0046', 'product_name' => 'Dup A', 'price' => 10],            // duplicate SKU
            ['sku' => 'OPEY-0046', 'product_name' => 'Dup B', 'price' => 20],            // duplicate SKU
        ]));

        $batch = $this->latestBatch();

        $this->assertSame(7, $batch->invalid_count);
        $this->assertSame(1, $batch->matched_count);

        $this->applyBatch($batch);

        $batch->refresh();

        $this->assertSame(PriceImportBatch::STATUS_COMPLETED, $batch->status);
        $this->assertSame(1, $batch->applied_count);
        $this->assertSame('130.00', $product->fresh()->price);
        $this->assertSame(1, ProductPriceHistory::count());
    }

    public function test_malformed_and_unsupported_files_are_rejected(): void
    {
        // Malformed CSV: the batch is recorded as failed, never previewed.
        $this->uploadCsv("this is not\na price list\n")->assertSessionHas('error');

        $this->assertSame(1, PriceImportBatch::count());
        $this->assertSame(PriceImportBatch::STATUS_FAILED, $this->latestBatch()->status);
        $this->assertSame(0, $this->latestBatch()->total_records);

        // Executable uploads are rejected before a batch is created.
        $this->actingAs($this->admin, 'admin')->post(
            route('admin.marketplace.price-imports.store'),
            [
                'vendor_id' => $this->vendor->id,
                'file' => UploadedFile::fake()->createWithContent('payload.php', "<?php echo 'x';"),
            ],
        )->assertSessionHasErrors('file');

        $this->assertSame(1, PriceImportBatch::count());

        // XXE / DTD payloads are refused.
        $this->uploadXml("<?xml version=\"1.0\"?>\n<!DOCTYPE foo [<!ENTITY xxe SYSTEM \"file:///etc/passwd\">]>\n<products><product><product_name>X</product_name><price>1</price></product></products>")
            ->assertSessionHas('error');

        $this->assertSame(2, PriceImportBatch::count());
        $this->assertSame(PriceImportBatch::STATUS_FAILED, $this->latestBatch()->status);
    }

    // ------------------------------------------------------------------
    // New products
    // ------------------------------------------------------------------

    public function test_new_products_are_previewed_and_only_created_when_enabled(): void
    {
        $this->uploadCsv($this->csv([
            ['sku' => 'NEW-0001', 'product_name' => 'Brand New Snack', 'category' => 'Groceries', 'unit' => '50g', 'price' => 75],
        ]), ['create_new_products' => 1]);

        $batch = $this->latestBatch();
        $item = $batch->items()->firstOrFail();

        $this->assertSame(PriceImportItem::STATUS_NEW, $item->status);
        $this->assertSame(1, $batch->new_count);

        // Nothing created during preview.
        $this->assertNull(Product::where('sku', 'NEW-0001')->first());

        $this->applyBatch($batch, ['create_new_products' => 1]);

        $product = Product::where('sku', 'NEW-0001')->firstOrFail();

        $this->assertSame($this->vendor->id, (int) $product->vendor_id);
        $this->assertSame('Brand New Snack', $product->name);
        $this->assertSame('75.00', $product->price);
        $this->assertSame('50g', $product->unit);
        $this->assertSame('approved', $product->approval_status);
        $this->assertSame(1, $batch->fresh()->created_count);

        $history = ProductPriceHistory::where('product_id', $product->id)->firstOrFail();
        $this->assertSame(ProductPriceHistory::STATUS_CREATED, $history->status);
        $this->assertNull($history->old_price);
    }

    public function test_new_products_are_not_created_when_the_admin_does_not_enable_it(): void
    {
        $this->uploadCsv($this->csv([
            ['sku' => 'NEW-0002', 'product_name' => 'Not Created', 'category' => 'Groceries', 'price' => 75],
        ]));

        $batch = $this->latestBatch();

        $this->applyBatch($batch);

        $this->assertNull(Product::where('sku', 'NEW-0002')->first());
        $this->assertSame(0, $batch->fresh()->created_count);
    }

    public function test_new_products_without_a_resolvable_category_are_flagged_for_manual_completion(): void
    {
        $this->uploadCsv($this->csv([
            ['sku' => 'NEW-0003', 'product_name' => 'Unknown Category Product', 'category' => 'Nonexistent', 'price' => 75],
        ]), ['create_new_products' => 1]);

        $item = $this->latestBatch()->items()->firstOrFail();

        $this->assertSame(PriceImportItem::STATUS_NEW, $item->status);
        $this->assertTrue((bool) $item->requires_manual_completion);
        $this->assertNull(Product::where('sku', 'NEW-0003')->first());
    }

    // ------------------------------------------------------------------
    // Missing products
    // ------------------------------------------------------------------

    public function test_missing_products_are_listed_but_kept_by_default(): void
    {
        $keep = $this->makeProduct(['name' => 'Keep Me', 'sku' => 'OPEY-0051', 'price' => 100]);
        $alsoKeep = $this->makeProduct(['name' => 'Keep Me Too', 'sku' => 'OPEY-0052', 'price' => 200]);
        $inFile = $this->makeProduct(['name' => 'In File', 'sku' => 'OPEY-0053', 'price' => 300]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0053', 'product_name' => 'In File', 'category' => 'Groceries', 'price' => 350],
        ]));

        $batch = $this->latestBatch();

        $this->assertSame(2, $batch->missing_count);
        $this->assertSame(
            ['Keep Me', 'Keep Me Too'],
            $batch->missingProducts()->orderBy('product_name')->pluck('product_name')->all(),
        );

        $this->applyBatch($batch);

        $this->assertTrue((bool) $keep->fresh()->is_active);
        $this->assertTrue((bool) $alsoKeep->fresh()->is_active);
        $this->assertSame('kept', $batch->missingProducts()->first()->action);
        $this->assertSame('350.00', $inFile->fresh()->price);
    }

    public function test_missing_products_are_deactivated_only_after_explicit_complete_catalogue_confirmation(): void
    {
        $missing = $this->makeProduct(['name' => 'Discontinued', 'sku' => 'OPEY-0061', 'price' => 100]);
        $this->makeProduct(['name' => 'Still Sold', 'sku' => 'OPEY-0062', 'price' => 200]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0062', 'product_name' => 'Still Sold', 'category' => 'Groceries', 'price' => 220],
        ]));

        $batch = $this->latestBatch();

        // Deactivation without confirming the complete catalogue is refused.
        $this->applyBatch($batch, ['deactivate_missing' => 1])
            ->assertSessionHasErrors('confirm_complete_catalogue');

        $this->assertTrue((bool) $missing->fresh()->is_active);
        $this->assertSame(PriceImportBatch::STATUS_PREVIEWED, $batch->fresh()->status);

        $this->applyBatch($batch, [
            'deactivate_missing' => 1,
            'confirm_complete_catalogue' => 1,
        ])->assertRedirect();

        $missing->refresh();

        $this->assertFalse((bool) $missing->is_active);
        $this->assertNull($missing->deleted_at);
        $this->assertSame(1, $batch->fresh()->deactivated_count);
        $this->assertSame('deactivated', $batch->missingProducts()->first()->action);
    }

    // ------------------------------------------------------------------
    // XML parity
    // ------------------------------------------------------------------

    public function test_xml_upload_produces_the_same_result_as_csv(): void
    {
        $product = $this->makeProduct(['name' => 'XML Product', 'sku' => 'OPEY-0071', 'price' => 100]);

        $this->uploadXml($this->xml([
            ['sku' => 'OPEY-0071', 'product_name' => 'XML Product', 'category' => 'Groceries', 'unit' => '1kg', 'price' => 175],
        ]))->assertRedirect();

        $batch = $this->latestBatch();

        $this->assertSame('xml', $batch->format);
        $this->assertSame(1, $batch->matched_count);

        $this->applyBatch($batch);

        $this->assertSame('175.00', $product->fresh()->price);
        $this->assertSame(1, ProductPriceHistory::where('batch_id', $batch->id)->count());
    }

    // ------------------------------------------------------------------
    // Large uploads
    // ------------------------------------------------------------------

    public function test_large_uploads_are_applied_in_chunks_without_partial_untracked_updates(): void
    {
        $rows = [];

        for ($i = 1; $i <= 520; $i++) {
            $sku = 'BULK-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $this->makeProduct(['name' => 'Bulk Product '.$i, 'sku' => $sku, 'price' => 100]);
            $rows[] = ['sku' => $sku, 'product_name' => 'Bulk Product '.$i, 'category' => 'Groceries', 'price' => 150];
        }

        $this->uploadCsv($this->csv($rows));

        $batch = $this->latestBatch();
        $this->assertSame(520, $batch->total_records);

        $this->applyBatch($batch);

        $batch->refresh();

        $this->assertSame(PriceImportBatch::STATUS_COMPLETED, $batch->status);
        $this->assertSame(520, $batch->applied_count);
        $this->assertSame(520, $batch->processed_count);
        $this->assertSame(520, ProductPriceHistory::where('batch_id', $batch->id)->count());
        $this->assertSame(0, Product::where('vendor_id', $this->vendor->id)->where('price', 100)->count());
    }

    // ------------------------------------------------------------------
    // Rollback
    // ------------------------------------------------------------------

    public function test_rollback_restores_prices_and_skips_newer_changes(): void
    {
        $restore = $this->makeProduct(['name' => 'Restore Me', 'sku' => 'OPEY-0081', 'price' => 100]);
        $superseded = $this->makeProduct(['name' => 'Newer Change', 'sku' => 'OPEY-0082', 'price' => 200]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0081', 'product_name' => 'Restore Me', 'category' => 'Groceries', 'price' => 120],
            ['sku' => 'OPEY-0082', 'product_name' => 'Newer Change', 'category' => 'Groceries', 'price' => 240],
        ]));

        $batch = $this->latestBatch();
        $this->applyBatch($batch);

        $this->assertSame('120.00', $restore->fresh()->price);
        $this->assertSame('240.00', $superseded->fresh()->price);

        // A newer manual change supersedes the batch for one product.
        $superseded->update(['price' => 999]);

        $this->actingAs($this->admin, 'admin')->post(
            route('admin.marketplace.price-imports.rollback', $batch),
            ['reason' => 'Wrong supplier file', 'confirm_rollback' => 1],
        )->assertRedirect()->assertSessionHas('success');

        $batch->refresh();

        $this->assertSame(PriceImportBatch::STATUS_ROLLED_BACK, $batch->status);
        $this->assertSame(1, $batch->rolled_back_count);
        $this->assertSame('100.00', $restore->fresh()->price);
        $this->assertSame('999.00', $superseded->fresh()->price);

        $this->assertSame(
            1,
            ProductPriceHistory::where('batch_id', $batch->id)->where('source', ProductPriceHistory::SOURCE_ROLLBACK)->count(),
        );
    }

    public function test_rollback_leaves_products_created_by_the_batch_in_place(): void
    {
        $this->uploadCsv($this->csv([
            ['sku' => 'NEW-0091', 'product_name' => 'Created Then Rolled Back', 'category' => 'Groceries', 'price' => 80],
        ]), ['create_new_products' => 1]);

        $batch = $this->latestBatch();
        $this->applyBatch($batch, ['create_new_products' => 1]);

        $product = Product::where('sku', 'NEW-0091')->firstOrFail();

        $this->actingAs($this->admin, 'admin')->post(
            route('admin.marketplace.price-imports.rollback', $batch),
            ['reason' => 'Supplier retracted the product', 'confirm_rollback' => 1],
        )->assertRedirect();

        $this->assertNotNull(Product::where('id', $product->id)->first());
        $this->assertSame(PriceImportBatch::STATUS_ROLLED_BACK, $batch->fresh()->status);
        $this->assertSame(0, $batch->fresh()->rolled_back_count);
    }

    public function test_a_rolled_back_batch_cannot_be_rolled_back_twice(): void
    {
        $this->makeProduct(['name' => 'Roll Twice', 'sku' => 'OPEY-0095', 'price' => 100]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0095', 'product_name' => 'Roll Twice', 'category' => 'Groceries', 'price' => 110],
        ]));

        $batch = $this->latestBatch();
        $this->applyBatch($batch);

        $this->actingAs($this->admin, 'admin')->post(
            route('admin.marketplace.price-imports.rollback', $batch),
            ['reason' => 'First rollback', 'confirm_rollback' => 1],
        )->assertRedirect();

        $this->actingAs($this->admin, 'admin')->post(
            route('admin.marketplace.price-imports.rollback', $batch),
            ['reason' => 'Second rollback', 'confirm_rollback' => 1],
        )->assertSessionHas('error');
    }

    // ------------------------------------------------------------------
    // UI / exports / access control
    // ------------------------------------------------------------------

    public function test_admin_pages_render_and_exports_download(): void
    {
        $this->makeProduct(['name' => 'UI Product', 'sku' => 'OPEY-0099', 'price' => 100]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.marketplace.price-imports.index'))
            ->assertOk()
            ->assertSee('Bulk Product Price Update');

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0099', 'product_name' => 'UI Product', 'category' => 'Groceries', 'price' => 120],
        ]));

        $batch = $this->latestBatch();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.marketplace.price-imports.show', $batch))
            ->assertOk()
            ->assertSee('Confirm and apply')
            ->assertSee('Products Not Found in Latest Upload');

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.marketplace.price-imports.export', [$batch, 'items']))
            ->assertOk();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.marketplace.price-imports.export', [$batch, 'errors']))
            ->assertOk();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.marketplace.price-imports.template', 'csv'))
            ->assertOk();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.marketplace.price-imports.template', 'xml'))
            ->assertOk();
    }

    public function test_guests_cannot_reach_the_bulk_import_pages(): void
    {
        $this->get(route('admin.marketplace.price-imports.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_product_page_shows_the_price_change_history(): void
    {
        $product = $this->makeProduct(['name' => 'History Product', 'sku' => 'OPEY-0100', 'price' => 100]);

        $this->uploadCsv($this->csv([
            ['sku' => 'OPEY-0100', 'product_name' => 'History Product', 'category' => 'Groceries', 'price' => 130],
        ]));

        $this->applyBatch($this->latestBatch());

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.marketplace.products.show', $product))
            ->assertOk()
            ->assertSee('Price Change History')
            ->assertSee('Bulk upload');
    }
}

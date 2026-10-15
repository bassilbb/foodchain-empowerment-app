<?php

use Illuminate\Support\Facades\DB;

/**
 * Map Fumi Store product images to their products on this server.
 *
 * Usage (from the project root):
 *   php map-fumi-images.php            # apply the mapping
 *   php map-fumi-images.php --dry-run  # preview without writing
 *
 * It reads fumi-store-images/fumi-image-map.json, copies each image file into
 * public/images/products/ (so it is served at /images/products/<file>) and
 * rebuilds the marketplace_product_images rows that link every product to its
 * images. Products are matched by slug, then sku, then name, so it works even
 * when the database ids differ from the source database.
 *
 * This is safe to run more than once: for every matched product the existing
 * image rows are replaced by the ones from the map.
 */

$root = __DIR__;
$dryRun = in_array('--dry-run', $argv, true);

require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mapFile = $root.'/fumi-store-images/fumi-image-map.json';
if (! is_file($mapFile)) {
    fwrite(STDERR, "Map file not found: {$mapFile}\n");
    exit(1);
}

$map = json_decode((string) file_get_contents($mapFile), true);
if (! is_array($map)) {
    fwrite(STDERR, "Invalid map JSON: {$mapFile}\n");
    exit(1);
}

$sourceDir = $root.'/fumi-store-images';
$targetDir = $root.'/public/images/products';
if (! is_dir($targetDir)) {
    @mkdir($targetDir, 0775, true);
}

echo "Fumi image mapping\n";
echo '  map entries (products): '.count($map)."\n";
echo "  source: {$sourceDir}\n";
echo "  target: {$targetDir}\n";
echo '  mode: '.($dryRun ? 'DRY RUN (no changes)' : 'APPLY')."\n\n";

$now = now()->toDateTimeString();
$productIds = [];
$rows = [];
$productsMatched = 0;
$productsMissing = 0;
$filesCopied = 0;
$filesMissing = 0;
$missingProducts = [];

foreach ($map as $entry) {
    $slug = $entry['slug'] ?? null;
    $sku = $entry['sku'] ?? null;
    $name = $entry['name'] ?? null;

    $product = null;
    if ($slug) {
        $product = DB::table('marketplace_products')->where('slug', $slug)->first();
    }
    if (! $product && $sku) {
        $product = DB::table('marketplace_products')->where('sku', $sku)->first();
    }
    if (! $product && $name) {
        $product = DB::table('marketplace_products')->where('name', $name)->first();
    }

    if (! $product) {
        $productsMissing++;
        $missingProducts[] = $name ?: ($slug ?: $sku);
        continue;
    }
    $productsMatched++;
    $productIds[] = $product->id;

    $sort = 0;
    foreach (($entry['images'] ?? []) as $img) {
        $file = $img['file'] ?? basename($img['path'] ?? '');
        if ($file === '') {
            continue;
        }
        $sort++;

        $src = $sourceDir.'/'.$file;
        $dst = $targetDir.'/'.$file;
        if (is_file($src)) {
            if (! is_file($dst)) {
                if (! $dryRun) {
                    @copy($src, $dst);
                }
                $filesCopied++;
            }
        } else {
            $filesMissing++;
        }

        $rows[] = [
            'product_id' => $product->id,
            'image_path' => 'images/products/'.$file,
            'is_primary' => (int) ($img['is_primary'] ?? 0),
            'is_placeholder' => 0,
            'sort_order' => (int) ($img['sort_order'] ?? $sort),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}

if (! $dryRun && $productIds) {
    DB::transaction(function () use ($productIds, $rows) {
        foreach (array_chunk(array_unique($productIds), 500) as $chunk) {
            DB::table('marketplace_product_images')->whereIn('product_id', $chunk)->delete();
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('marketplace_product_images')->insert($chunk);
        }
    });
}

echo "Products matched: {$productsMatched}\n";
echo "Products missing: {$productsMissing}\n";
echo 'Images mapped:    '.count($rows)."\n";
echo "Files copied:     {$filesCopied}\n";
echo "Files not found:  {$filesMissing}\n";

if ($missingProducts) {
    echo "\nUnmatched products (first 20):\n";
    foreach (array_slice($missingProducts, 0, 20) as $m) {
        echo "  - {$m}\n";
    }
}

echo "\nDone.\n";

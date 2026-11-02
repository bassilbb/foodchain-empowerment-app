<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Marketplace\Product;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Vendor;

$fumiVendor = Vendor::where('slug', 'fumi-store')->first();
$products = Product::where('vendor_id', $fumiVendor->id)->orderBy('name')->get();

echo "=== ALL CATEGORIES ===\n";
foreach (Category::orderBy('name')->get() as $c) {
    echo "  ID {$c->id}: {$c->name} (slug: {$c->slug})\n";
}

echo "\n=== ALL FUMI PRODUCTS (187) ===\n";
foreach ($products as $p) {
    echo "  [{$p->id}] {$p->name}\n";
}
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Marketplace\Product;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Vendor;

$fumiVendor = Vendor::where('slug', 'fumi-store')->first();
$products = Product::where('vendor_id', $fumiVendor->id)->orderBy('name')->get();

$oilsCat = Category::where('slug', 'oils-fats')->first();
echo "Oils & Fats category ID: {$oilsCat->id}\n\n";

// Identify oil-related products by name keywords
$oilKeywords = ['oil', 'soya', 'vegetable', 'palm', 'olive', 'groundnut'];
$moved = 0;
$movedNames = [];

foreach ($products as $p) {
    $nameLower = strtolower($p->name);
    $isOilProduct = false;
    
    foreach ($oilKeywords as $kw) {
        if (strpos($nameLower, $kw) !== false) {
            $isOilProduct = true;
            break;
        }
    }
    
    if ($isOilProduct) {
        $currentlyInOils = $p->category_id == $oilsCat->id;
        if (!$currentlyInOils) {
            $p->category_id = $oilsCat->id;
            $p->save();
            $moved++;
            $movedNames[] = "[{$p->id}] {$p->name}";
        }
    }
}

echo "=== Summary ===\n";
echo "Total Fumi products: {$products->count()}\n";
echo "Moved to Oils & Fats: {$moved}\n";

if ($moved > 0) {
    echo "\nMoved products:\n";
    foreach ($movedNames as $name) {
        echo "  {$name}\n";
    }
}

echo "\n=== Oils & Fats Category Products ===\n";
$oilsProducts = Product::where('category_id', $oilsCat->id)->get();
echo "Total products in Oils & Fats: {$oilsProducts->count()}\n";
foreach ($oilsProducts as $p) {
    $v = $p->vendor;
    $vendorName = $v ? $v->name : 'NULL';
    $status = $v ? ($v->status == 'suspended' ? 'suspended' : 'active') : 'no vendor';
    echo "  - {$p->name} | Vendor: {$vendorName} | Vendor Status: {$status}\n";
}

$activeOils = Product::active()->where('category_id', $oilsCat->id)->count();
echo "\nActive (approved vendor) products in Oils & Fats: {$activeOils}\n";
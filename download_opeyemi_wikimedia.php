<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

$limit = (int)($argv[1] ?? 60);
$offset = (int)($argv[2] ?? 0);

$v = Vendor::where('name', 'like', '%Opeyemi%')->firstOrFail();
$targetDir = public_path('images/products');
if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);
$now = now()->toDateTimeString();

$products = Product::where('vendor_id', $v->id)
    ->orderBy('id')
    ->skip($offset)
    ->take($limit)
    ->get();

$ok = 0; $skip = 0; $fail = 0;
foreach ($products as $product) {
    $existing = $product->images()->first();
    if ($existing && !$existing->is_placeholder && $existing->image_path) {
        $p = public_path($existing->image_path);
        $ext = strtolower(pathinfo($existing->image_path, PATHINFO_EXTENSION));
        if ($ext !== 'svg' && (file_exists($p) || str_starts_with($existing->image_path, 'http'))) {
            $skip++; continue;
        }
    }

    $name = $product->name;
    // build a search query: strip pack sizes/codes, keep meaningful words
    $q = preg_replace('/\b\d+(\.\d+)?\s*(g|kg|ml|lt|l|oz|lb)\b/i', ' ', $name);
    $q = preg_replace('/[\*xX]\s*\d+/', ' ', $q);
    $q = preg_replace('/[^a-zA-Z\s]/', ' ', $q);
    $q = trim(preg_replace('/\s+/', ' ', $q));
    $words = array_values(array_filter(explode(' ', $q), fn($w)=>strlen($w)>2));
    $query = implode(' ', array_slice($words, 0, 4));
    if ($query === '') $query = 'food product packaging';

    $url = "https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrnamespace=6&gsrsearch=".urlencode($query)."&gsrlimit=1&prop=imageinfo&iiprop=url|mime&format=json&origin=*";
    $ctx = stream_context_create(['http'=>['timeout'=>20,'user_agent'=>'FoodChainEmpowerment/1.0 (contact: admin@local)','header'=>"Accept: application/json\r\n"],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
    $json = @file_get_contents($url, false, $ctx);
    if (!$json) { $fail++; echo "NOJSON {$product->sku}\n"; usleep(150000); continue; }
    $d = json_decode($json, true);
    $pages = $d['query']['pages'] ?? [];
    $imgUrl = null; $mime = null;
    foreach ($pages as $pg) {
        if (!empty($pg['imageinfo'][0]['url'])) {
            $imgUrl = $pg['imageinfo'][0]['url'];
            $mime = $pg['imageinfo'][0]['mime'] ?? '';
            break;
        }
    }
    if (!$imgUrl) { $fail++; echo "NOIMG {$product->sku} | {$query}\n"; usleep(150000); continue; }
    if (str_contains((string)$mime, 'svg')) { $fail++; echo "SVG {$product->sku}\n"; usleep(150000); continue; }

    $data = @file_get_contents($imgUrl, false, $ctx);
    if (!$data || strlen($data) < 1500) { $fail++; echo "NODATA {$product->sku}\n"; usleep(150000); continue; }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $detected = $finfo->buffer($data);
    if (!str_starts_with($detected, 'image/') || str_contains($detected, 'svg')) { $fail++; continue; }

    $ext = 'jpg';
    if (str_contains($detected, 'png')) $ext = 'png';
    elseif (str_contains($detected, 'webp')) $ext = 'webp';
    elseif (str_contains($detected, 'jpeg')) $ext = 'jpg';

    $filename = 'opeyemi-' . Str::slug(Str::limit($name, 80, '')) . '.' . $ext;
    $filename = substr(preg_replace('/[^a-z0-9\.\-]/', '', strtolower($filename)), 0, 140);
    $dst = $targetDir . DIRECTORY_SEPARATOR . $filename;
    file_put_contents($dst, $data);
    $rel = 'images/products/' . $filename;

    DB::table('marketplace_product_images')->where('product_id', $product->id)->delete();
    DB::table('marketplace_product_images')->insert([
        'product_id' => $product->id,
        'image_path' => $rel,
        'is_primary' => 1,
        'is_placeholder' => 0,
        'sort_order' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    echo "OK {$product->sku}\n";
    $ok++;
    usleep(200000);
}
echo "BATCH offset={$offset} limit={$limit} ok={$ok} skip={$skip} fail={$fail}\n";

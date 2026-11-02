<?php
require 'vendor/autoload.php';
$app=require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

$reader = IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load('C:/Users/Devops-Bassey/Downloads/opeyemi_foods_product_image_research_tracker_updated.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

$v = Vendor::where('name','like','%Opeyemi%')->first();
$targetDir = public_path('images/products');
if(!is_dir($targetDir)) @mkdir($targetDir,0775,true);
$now = now()->toDateTimeString();

foreach($rows as $i => $r){
    if($i == 0) continue;
    if(!isset($r[1]) || !$r[1]) continue;
    $name = trim($r[1]);
    $sn = $r[0];
    $searchUrl = isset($r[2]) ? trim($r[2]) : '';
    if(strpos($searchUrl, 'google.com/search') === false) continue;
    
    $sku = 'OPEY-'.str_pad((string)$sn,4,'0',STR_PAD_LEFT);
    $product = Product::where('vendor_id',$v->id)->where('sku',$sku)->first();
    if(!$product) continue;
    
    // skip if already has real image
    $existing = $product->images()->first();
    if($existing && !$existing->is_placeholder && strpos($existing->image_path,'images/products/')===0 && !Str::contains($existing->image_path,'placeholder')) {
        // check if it's svg
        $p = public_path($existing->image_path);
        if(file_exists($p) && pathinfo($p,PATHINFO_EXTENSION) !== 'svg') continue;
    }
    
    $ctx = stream_context_create(['http'=>['timeout'=>30,'user_agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
    $html = @file_get_contents($searchUrl, false, $ctx);
    if($html === false) {
        // try curl
        $ch = curl_init($searchUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $html = curl_exec($ch);
        curl_close($ch);
    }
    if(!$html) { echo "NOHTML $sku\n"; continue; }
    
    // look for actual image URLs in results
    preg_match_all('#src="([^"]+\.(jpg|jpeg|png|webp)[^"]*)"#i', $html, $m);
    if(empty($m[1])) preg_match_all('#(https?://[^\s"\']+\.(jpg|jpeg|png|webp)[^\s"\']*)#i', $html, $m);
    
    if(empty($m[1])) { echo "NOIMG $sku\n"; continue; }
    
    foreach($m[1] as $imgUrl){
        if(strpos($imgUrl, 'google') !== false) continue;
        if(strpos($imgUrl, 'gstatic') !== false && strpos($imgUrl, 'images') === false) continue;
        if(strpos($imgUrl, 'logo') !== false) continue;
        
        $data = @file_get_contents($imgUrl, false, $ctx);
        if($data === false) {
            $ch = curl_init($imgUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            $data = curl_exec($ch);
            curl_close($ch);
        }
        if(!$data || strlen($data) < 1000) continue;
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($data);
        if(strpos($mime, 'image/') !== 0) continue;
        if(strpos($mime, 'svg') !== false) continue;
        
        $ext = 'jpg';
        if(strpos($mime, 'png') !== false) $ext = 'png';
        elseif(strpos($mime, 'webp') !== false) $ext = 'webp';
        elseif(strpos($mime, 'jpeg') !== false) $ext = 'jpg';
        
        $filename = 'opeyemi-'.Str::slug($name).'.'.$ext;
        $filename = substr(preg_replace('/[^a-z0-9\.\-]/','', strtolower($filename)), 0, 150);
        $dst = $targetDir.DIRECTORY_SEPARATOR.$filename;
        file_put_contents($dst, $data);
        $rel = 'images/products/'.$filename;
        DB::table('marketplace_product_images')->where('product_id', $product->id)->delete();
        DB::table('marketplace_product_images')->insert([
            'product_id'=>$product->id,'image_path'=>$rel,'is_primary'=>1,'is_placeholder'=>0,'sort_order'=>1,'created_at'=>$now,'updated_at'=>$now,
        ]);
        echo "OK $sku -> $rel\n";
        break 2;
    }
    echo "SKIP $sku\n";
}
echo "DONE\n";

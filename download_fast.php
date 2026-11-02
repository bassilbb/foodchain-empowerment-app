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
$now = now()->toDateTimeString();
$count = 0;

foreach($rows as $i => $r){
    if($i == 0 || $count >= 50) break;
    if(!isset($r[1]) || !$r[1]) continue;
    $name = trim($r[1]);
    $sn = $r[0];
    $searchUrl = isset($r[2]) ? trim($r[2]) : '';
    if(strpos($searchUrl, 'google.com/search') === false) continue;
    
    $sku = 'OPEY-'.str_pad((string)$sn,4,'0',STR_PAD_LEFT);
    $product = Product::where('vendor_id',$v->id)->where('sku',$sku)->first();
    if(!$product) continue;
    
    $existing = $product->images()->first();
    if($existing && !$existing->is_placeholder) {
        $p = public_path($existing->image_path);
        if(file_exists($p) && strtolower(pathinfo($p,PATHINFO_EXTENSION)) !== 'svg') continue;
    }
    
    // Use Bing image search API style or just fetch with different approach
    $query = urlencode($name.' product packaging nigeria');
    $url = "https://www.bing.com/images/search?q=".$query."&form=HDRSC2&first=1";
    $ctx = stream_context_create(['http'=>['timeout'=>15,'user_agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0'],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
    $html = @file_get_contents($url, false, $ctx);
    if(!$html) continue;
    
    preg_match_all('#"murl":"([^"]+)"#', $html, $m);
    if(empty($m[1])) preg_match_all('#src="([^"]+\.(jpg|jpeg|png|webp))"#i', $html, $m);
    if(empty($m[1])) continue;
    
    foreach($m[1] as $imgUrl){
        $imgUrl = str_replace('\\/', '/', $imgUrl);
        if(strpos($imgUrl, 'bing.com') !== false || strpos($imgUrl, 'r.bing') !== false) continue;
        $data = @file_get_contents($imgUrl, false, $ctx);
        if(!$data || strlen($data) < 2000) continue;
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($data);
        if(strpos($mime, 'image/') !== 0 || strpos($mime, 'svg') !== false) continue;
        
        $ext = strpos($mime, 'png') !== false ? 'png' : (strpos($mime, 'webp') !== false ? 'webp' : 'jpg');
        $filename = 'opeyemi-'.Str::slug($name).'.'.$ext;
        $filename = substr(preg_replace('/[^a-z0-9\.\-]/','', strtolower($filename)), 0, 140);
        $dst = $targetDir.DIRECTORY_SEPARATOR.$filename;
        file_put_contents($dst, $data);
        $rel = 'images/products/'.$filename;
        DB::table('marketplace_product_images')->where('product_id', $product->id)->delete();
        DB::table('marketplace_product_images')->insert([
            'product_id'=>$product->id,'image_path'=>$rel,'is_primary'=>1,'is_placeholder'=>0,'sort_order'=>1,'created_at'=>$now,'updated_at'=>$now,
        ]);
        echo "OK $sku\n";
        $count++;
        break;
    }
}
echo "DONE $count\n";

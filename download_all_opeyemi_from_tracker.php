<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Marketplace\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;
$reader = IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load('C:/Users/Devops-Bassey/Downloads/opeyemi_foods_product_image_research_tracker_updated.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

$targetDir = public_path('images/products');
if(!is_dir($targetDir)) @mkdir($targetDir,0775,true);
$now = now()->toDateTimeString();

// map from notes - extract URLs
foreach($rows as $i => $r){
    if($i == 0) continue;
    if(!isset($r[1]) || !$r[1]) continue;
    $name = trim($r[1]);
    $sn = $r[0];
    $status = isset($r[3]) ? trim($r[3]) : '';
    $notes = isset($r[7]) ? trim($r[7]) : '';
    
    // extract URLs from notes
    preg_match_all('#https?://[^\s|]+#', $notes, $m);
    $urls = $m[0] ?? [];
    if(empty($urls)) continue;
    
    // find product
    $sku = 'OPEY-'.str_pad((string)$sn,4,'0',STR_PAD_LEFT);
    $product = Product::where('sku', $sku)->first();
    if(!$product){ echo "NOT FOUND: $sku $name\n"; continue; }
    
    // try each URL
    foreach($urls as $url){
        // skip google search links
        if(strpos($url, 'google.com/search') !== false) continue;
        
        $ctx = stream_context_create(['http'=>['timeout'=>60,'user_agent'=>'Mozilla/5.0','follow_location'=>1,'max_redirects'=>5],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
        $data = @file_get_contents($url, false, $ctx);
        if($data === false){ 
            // try curl
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($data === false || $code >= 400){ echo "FAIL $url for $name\n"; continue; }
        }
        
        // check if it's an image
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($data);
        if(strpos($mime, 'image/') !== 0){ 
            // might be html page with image - try to extract og:image or img
            if(strpos($data, '<img') !== false){
                // simple extraction
                if(preg_match('#<img[^>]+src=["\']([^"\']+)["\']#i', $data, $im)){
                    $imgUrl = $im[1];
                    if(strpos($imgUrl, 'http') !== 0){
                        // relative
                        $p = parse_url($url);
                        $base = $p['scheme'].'://'.$p['host'].(isset($p['port'])?':'.$p['port']:'');
                        if(strpos($imgUrl, '/') === 0) $imgUrl = $base.$imgUrl;
                        else $imgUrl = $base.dirname($p['path']).'/'.$imgUrl;
                    }
                    $data2 = @file_get_contents($imgUrl, false, $ctx);
                    if($data2){ $data = $data2; $url = $imgUrl; }
                }
            }
            if(strpos((string)$mime, 'image/') !== 0 && strlen($data) < 10000){ 
                // not an image, skip
                continue; 
            }
        }
        
        $ext = 'jpg';
        $pathPart = parse_url($url, PHP_URL_PATH);
        if($pathPart){ $e = strtolower(pathinfo($pathPart, PATHINFO_EXTENSION)); if(in_array($e,['jpg','jpeg','png','gif','webp','avif'])) $ext = $e == 'jpeg' ? 'jpg' : $e; }
        $filename = 'opeyemi-'.Str::slug($name).'.'.$ext;
        $filename = substr($filename, 0, 200); // truncate
        $dst = $targetDir.DIRECTORY_SEPARATOR.$filename;
        file_put_contents($dst, $data);
        $rel = 'images/products/'.$filename;
        DB::table('marketplace_product_images')->where('product_id', $product->id)->delete();
        DB::table('marketplace_product_images')->insert([
            'product_id'=>$product->id,'image_path'=>$rel,'is_primary'=>1,'is_placeholder'=>0,'sort_order'=>1,'created_at'=>$now,'updated_at'=>$now,
        ]);
        echo "OK [$sku]: $name -> $rel\n";
        break;
    }
}
echo "Done.\n";

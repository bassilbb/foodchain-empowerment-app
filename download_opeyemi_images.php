<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Marketplace\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

$htmlFile='c:/Users/Devops-Bassey/Downloads/opeyemi_foods_product_image_catalogue_with_images.html';
$dom=new DOMDocument(); libxml_use_internal_errors(true); $dom->loadHTMLFile($htmlFile); libxml_clear_errors();
$entries=[];
foreach($dom->getElementsByTagName('article') as $art){
 if(strpos((string)$art->getAttribute('class'),'card')===false)continue;
 $num=(int)$art->getAttribute('data-num');
 $name='';$h3s=$art->getElementsByTagName('h3');
 if($h3s->length>0)$name=trim($h3s->item(0)->textContent);
 if($name==='')$name=trim($art->getAttribute('data-name'));
 if($name==='')continue;
 $imgSrc=null;$imgs=$art->getElementsByTagName('img');
 if($imgs->length>0){$src=trim($imgs->item(0)->getAttribute('src'));if($src!=='')$imgSrc=$src;}
 if($imgSrc) $entries[]=['num'=>$num,'name'=>$name,'img'=>$imgSrc];
}
$targetDir=public_path('images/products');
if(!is_dir($targetDir)) @mkdir($targetDir,0775,true);
$now=now()->toDateTimeString();
foreach($entries as $entry){
 $name=$entry['name']; $num=$entry['num']?:0;
 $baseSlug=Str::slug($name); if($baseSlug==='')$baseSlug='product-'.$num;
 // find product
 $product=Product::where('sku','OPEY-'.str_pad((string)$num,4,'0',STR_PAD_LEFT))->first();
 if(!$product){ echo "product not found: $name\n"; continue; }
 $url=$entry['img'];
 // download
 $ctx=stream_context_create(['http'=>['timeout'=>30,'user_agent'=>'Mozilla/5.0'],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
 $data=@file_get_contents($url,false,$ctx);
 if($data===false){ echo "FAILED download: $url\n"; continue; }
 // determine extension
 $ext='jpg';
 $pathPart=parse_url($url,PHP_URL_PATH);
 if($pathPart){ $e=pathinfo($pathPart,PATHINFO_EXTENSION); if($e!=='') $ext=strtolower($e); }
 if($ext==='webp' || $ext==='jpeg'){}
 if(!in_array($ext,['jpg','jpeg','png','gif','webp'])) $ext='jpg';
 // clean query-ish ext
 $ext=preg_replace('/[^a-z0-9]/','',$ext);
 $filename='opeyemi-'.Str::slug($name).'.'.$ext;
 $dst=$targetDir.DIRECTORY_SEPARATOR.$filename;
 file_put_contents($dst,$data);
 $rel='images/products/'.$filename;
 // update image row
 DB::table('marketplace_product_images')->where('product_id',$product->id)->delete();
 DB::table('marketplace_product_images')->insert([
   'product_id'=>$product->id,'image_path'=>$rel,'is_primary'=>1,'is_placeholder'=>0,'sort_order'=>1,'created_at'=>$now,'updated_at'=>$now,
 ]);
 echo "OK: $name -> $rel (".strlen($data)." bytes)\n";
}
echo "Done.\n";

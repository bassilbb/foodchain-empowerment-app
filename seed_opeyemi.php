<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use App\Support\ProductPlaceholder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
$htmlFile='c:/Users/Devops-Bassey/Downloads/opeyemi_foods_product_image_catalogue_with_images.html';
if(!is_file($htmlFile)){echo "not found\n";exit(1);}
$dom=new DOMDocument();libxml_use_internal_errors(true);$dom->loadHTMLFile($htmlFile);libxml_clear_errors();
$entries=[];
foreach($dom->getElementsByTagName('article') as $art){
 if(!($art instanceof DOMElement))continue;
 if(strpos((string)$art->getAttribute('class'),'card')===false)continue;
 $num=(int)$art->getAttribute('data-num');
 $name='';$h3s=$art->getElementsByTagName('h3');
 if($h3s->length>0)$name=trim($h3s->item(0)->textContent);
 if($name==='')$name=trim($art->getAttribute('data-name'));
 if($name==='')continue;
 $imgSrc=null;$imgs=$art->getElementsByTagName('img');
 if($imgs->length>0){$src=trim($imgs->item(0)->getAttribute('src'));if($src!=='')$imgSrc=$src;}
 $entries[]=['num'=>$num,'name'=>$name,'img'=>$imgSrc];
}
echo "Parsed: ".count($entries)."\n";
$catBySlug=[];foreach(Category::all() as $c)$catBySlug[$c->slug]=$c->id;
$fallbackCatId=reset($catBySlug)?:null;
if(!$fallbackCatId){$fallbackCatId=Category::create(['name'=>'General','slug'=>'general','is_active'=>true])->id;$catBySlug['general']=$fallbackCatId;}
function mapCategory(string $name,array $catBySlug,int $fallback):int{
 $n=strtolower($name);
 $map=['oil'=>'oils-fats','soya'=>'oils-fats','vegetable'=>'oils-fats','palm'=>'oils-fats','rice'=>'rice','semolina'=>'flour-baking','semo'=>'flour-baking','flour'=>'flour-baking','milk'=>'eggs-dairy','tomato'=>'tomatoes-condiments','pepper'=>'pepper-mixes','spice'=>'seasonings','seasoning'=>'seasonings','sardine'=>'fish-seafood','fish'=>'fish-seafood','seafood'=>'fish-seafood','noodle'=>'noodles','spag'=>'spaghetti','pasta'=>'pasta','macaroni'=>'macaroni','cereal'=>'cereals','biscuit'=>'biscuits','shortbread'=>'biscuits','water'=>'water','juice'=>'juices','malt'=>'malt-drinks','tea'=>'tea-coffee','coffee'=>'tea-coffee','sugar'=>'salt-sugar','salt'=>'salt-sugar','garri'=>'garri-flour','beans'=>'beans-legumes','meat'=>'fresh-meat','chicken'=>'fresh-meat','egg'=>'eggs-dairy','soap'=>'cleaning','detergent'=>'cleaning','cleaning'=>'cleaning','baby'=>'baby-food','infant'=>'infant-cereal'];
 foreach($map as $kw=>$slug){if(strpos($n,$kw)!==false&&isset($catBySlug[$slug]))return $catBySlug[$slug];}
 return $fallback;
}
$vendorUser=User::updateOrCreate(['email'=>'opeyemi@foodchain.ng'],['first_name'=>'Opeyemi','last_name'=>'Foods','phone'=>'08000000000','password'=>bcrypt('password'),'referral_code'=>strtoupper(Str::random(8)),'is_active'=>true]);
$vendor=Vendor::updateOrCreate(['email'=>'opeyemi@foodchain.ng'],['user_id'=>$vendorUser->id,'name'=>'Opeyemi Foods and Distribution Limited','slug'=>'opeyemi-foods-and-distribution-limited','business_name'=>'Opeyemi Foods and Distribution Limited','phone'=>'08000000000','city'=>'Lagos','state'=>'Lagos','description'=>'Opeyemi Foods and Distribution Limited registered vendor.','status'=>'approved','commission_rate'=>10.0,'wallet_balance'=>0]);
echo "Vendor ID: {$vendor->id}\n";
$now=now()->toDateTimeString();$productIds=[];
DB::transaction(function()use($entries,$vendor,$catBySlug,$fallbackCatId,$now,&$productIds){
 foreach($entries as $entry){
  $name=$entry['name'];$num=$entry['num']?:0;
  $baseSlug=Str::slug($name);if($baseSlug==='')$baseSlug='product-'.$num;
  $slug=$baseSlug;if(Product::where('slug',$slug)->exists())$slug=$baseSlug.'-'.$num;
  $i=0;while(Product::where('slug',$slug)->exists()){$i++;$slug=$baseSlug.'-'.$num.'-'.$i;}
  $categoryId=mapCategory($name,$catBySlug,$fallbackCatId);
  $product=Product::updateOrCreate(['slug'=>$slug],['vendor_id'=>$vendor->id,'category_id'=>$categoryId,'brand_id'=>null,'name'=>$name,'sku'=>'OPEY-'.str_pad((string)$num,4,'0',STR_PAD_LEFT),'unit'=>'pack','price'=>1000.00,'member_price'=>900.00,'stock_quantity'=>50,'low_stock_threshold'=>5,'description'=>$name,'is_featured'=>false,'is_active'=>true,'approval_status'=>'approved','approved_at'=>now()]);
  $productIds[]=$product->id;
  DB::table('marketplace_product_images')->where('product_id',$product->id)->delete();
  $rows=[];$sort=0;
  if(!empty($entry['img'])){$sort++;$rows[]=['product_id'=>$product->id,'image_path'=>$entry['img'],'is_primary'=>1,'is_placeholder'=>0,'sort_order'=>$sort,'created_at'=>$now,'updated_at'=>$now];}
  else{$placeholderPath=ProductPlaceholder::store($slug,$name,Category::find($categoryId)?->slug??'default');$sort++;$rows[]=['product_id'=>$product->id,'image_path'=>$placeholderPath,'is_primary'=>1,'is_placeholder'=>1,'sort_order'=>$sort,'created_at'=>$now,'updated_at'=>$now];}
  if($rows){foreach(array_chunk($rows,500) as $chunk){DB::table('marketplace_product_images')->insert($chunk);}}
 }
});
echo "Products: ".count($productIds)."\n";
try{\Illuminate\Support\Facades\Artisan::call('marketplace:sync-state-pricing');echo "State pricing synced.\n";}catch(\Throwable $e){echo "sync skipped: ".$e->getMessage()."\n";}
echo "Done.\n";

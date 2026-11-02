<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Category;
use App\Support\ProductPlaceholder;
use Illuminate\Support\Facades\DB;

// products still pointing at dead remote URLs
$dead = DB::table('marketplace_product_images')
  ->where('image_path','like','https://beta.ebeosi.com.ng/%')
  ->get();
$now=now()->toDateTimeString();
foreach($dead as $row){
  $product = Product::find($row->product_id);
  if(!$product){ echo "missing product {$row->product_id}\n"; continue; }
  DB::table('marketplace_product_images')->where('id',$row->id)->delete();
  $cat = Category::find($product->category_id);
  $path = ProductPlaceholder::store($product->slug, $product->name, $cat?->slug ?? 'default');
  DB::table('marketplace_product_images')->insert([
    'product_id'=>$product->id,'image_path'=>$path,'is_primary'=>1,'is_placeholder'=>1,'sort_order'=>1,'created_at'=>$now,'updated_at'=>$now,
  ]);
  echo "placeholder set for product {$product->id} ({$product->name}) -> $path\n";
}
echo "Done.\n";

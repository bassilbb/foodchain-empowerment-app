<?php
require 'vendor/autoload.php';
$app=require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use Illuminate\Support\Facades\DB;
$v = Vendor::where('name','like','%Opeyemi%')->first();
$p = Product::where('vendor_id',$v->id)->where('sku','OPEY-0003')->first();
DB::table('marketplace_product_images')->where('product_id',$p->id)->delete();
DB::table('marketplace_product_images')->insert([
    'product_id'=>$p->id,'image_path'=>'images/products/opeyemi-active-gold-oil-5lt4.png','is_primary'=>1,'is_placeholder'=>0,'sort_order'=>1,'created_at'=>now(),'updated_at'=>now(),
]);
echo "ok";

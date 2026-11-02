<?php
require 'vendor/autoload.php';
$app=require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\Product;
$v = Vendor::where('name','like','%Opeyemi%')->first();
$count = Product::where('vendor_id',$v->id)->count();
$withImg = Product::where('vendor_id',$v->id)->has('images')->count();
$real = Product::where('vendor_id',$v->id)->whereHas('images',fn($q)=>$q->where('is_placeholder',0))->count();
echo "total:$count withImg:$withImg real:$real\n";

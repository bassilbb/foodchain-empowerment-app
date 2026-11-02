<?php
require 'vendor/autoload.php';
$app=require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
$v = Vendor::where('name','like','%Opeyemi%')->first();
$p = Product::where('vendor_id',$v->id)->where('sku','OPEY-0003')->first();
var_dump($p ? $p->name : null);
var_dump($p && $p->images()->first() ? $p->images()->first()->image_path : null);
var_dump($p && $p->images()->first() ? $p->images()->first()->is_placeholder : null);

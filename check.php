<?php
require 'vendor/autoload.php';
$app=require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Models\Marketplace\Product::where('sku','OPEY-0009')->first();
var_dump($p ? $p->name : null);
$img = $p->images()->first();
var_dump($img ? $img->image_path : null);
var_dump($img ? $img->is_placeholder : null);

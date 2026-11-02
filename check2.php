<?php
require 'vendor/autoload.php';
$app=require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$p = App\Models\Marketplace\Product::where('sku','OPEY-0013')->first();
var_dump($p ? $p->name : null);
$img = $p ? $p->images()->first() : null;
var_dump($img ? $img->image_path : null);
var_dump($img ? $img->is_placeholder : null);

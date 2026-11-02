<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$rows=Illuminate\Support\Facades\DB::table('marketplace_product_images')->where('is_placeholder',0)->get();
foreach($rows as $r) echo $r->product_id.' | '.$r->image_path."\n";
echo "count: ".$rows->count()."\n";

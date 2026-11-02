<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo 'Fumi products: '.App\Models\Marketplace\Product::whereHas('vendor',fn($q)=>$q->where('name','Fumi Store'))->count()."\n";
echo 'Opeyemi products: '.App\Models\Marketplace\Product::whereHas('vendor',fn($q)=>$q->where('name','like','%Opeyemi%'))->count()."\n";
echo 'Fumi vendor id: '.App\Models\Marketplace\Vendor::where('name','Fumi Store')->value('id')."\n";
echo 'Opeyemi vendor id: '.App\Models\Marketplace\Vendor::where('name','like','%Opeyemi%')->value('id')."\n";

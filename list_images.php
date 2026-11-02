<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$dom=new DOMDocument(); libxml_use_internal_errors(true);
$dom->loadHTMLFile('c:/Users/Devops-Bassey/Downloads/opeyemi_foods_product_image_catalogue_with_images.html');
libxml_clear_errors();
foreach($dom->getElementsByTagName('article') as $a){
 if(strpos((string)$a->getAttribute('class'),'card')===false)continue;
 $h=$a->getElementsByTagName('h3');
 $name=$h->length?trim($h->item(0)->textContent):trim($a->getAttribute('data-name'));
 $i=$a->getElementsByTagName('img');
 if($i->length){
   $src=trim($i->item(0)->getAttribute('src'));
   if($src!=='') echo $name." || ".$src."\n";
 }
}

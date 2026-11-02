<?php
require 'vendor/autoload.php';
$app=require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$htmlFile='c:/Users/Devops-Bassey/Downloads/opeyemi_foods_product_image_catalogue_with_images.html';
$dom=new DOMDocument();libxml_use_internal_errors(true);$dom->loadHTMLFile($htmlFile);libxml_clear_errors();
$c=0;
foreach($dom->getElementsByTagName('article') as $art){
  if(strpos((string)$art->getAttribute('class'),'card')===false)continue;
  $imgs=$art->getElementsByTagName('img');
  if($imgs->length>0){$src=trim($imgs->item(0)->getAttribute('src'));if($src!=='')$c++;}
}
echo $c;

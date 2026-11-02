<?php
$u='https://beta.ebeosi.com.ng/public/uploads/products/photos/ezxuYTwSIRniseyLF2YzKP0zOl9M4rXcTB2ENfj8.jpeg';
$ch=curl_init($u);
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',CURLOPT_TIMEOUT=>30]);
$d=curl_exec($ch);
echo 'HTTP '.curl_getinfo($ch,CURLINFO_HTTP_CODE).' len '.strlen($d ?: '').' err '.curl_error($ch)."\n";
if($d) file_put_contents('c:/laragon/www/foodchain-empowerment-app-main/test_ebeosi.jpg',$d);

<?php
$q = urlencode('vegetable oil bottle');
$url = "https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrnamespace=6&gsrsearch={$q}&gsrlimit=1&prop=imageinfo&iiprop=url|mime&format=json";
$ctx = stream_context_create(['http'=>['timeout'=>15,'user_agent'=>'FoodChainBot/1.0'],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
$json = @file_get_contents($url, false, $ctx);
echo $json ? substr($json,0,500) : "NO";

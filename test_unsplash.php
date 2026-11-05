<?php
$q = urlencode('vegetable oil bottle product');
$url = "https://unsplash.com/napi/search/photos?query={$q}&per_page=1";
$ctx = stream_context_create(['http'=>['timeout'=>15,'user_agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36','header'=>"Accept: application/json\r\n"],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
$json = @file_get_contents($url, false, $ctx);
if(!$json) { echo "NO\n"; exit; }
$d = json_decode($json, true);
if(isset($d['results'][0]['urls']['regular'])) echo $d['results'][0]['urls']['regular']."\n";
else echo "NONE\n";

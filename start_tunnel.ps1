# Start cloudflared tunnel detached
$exe = "C:\laragon\bin\cloudflared\cloudflared.exe"
$log = "C:\laragon\www\foodchain-empowerment-app-main\tunnel.log"
$err = "C:\laragon\www\foodchain-empowerment-app-main\tunnel.err.log"
Remove-Item $log, $err -ErrorAction SilentlyContinue
Start-Process -FilePath $exe -ArgumentList "tunnel --url http://127.0.0.1:8000 --no-autoupdate" -RedirectStandardOutput $log -RedirectStandardError $err -WindowStyle Hidden
Write-Output "started"
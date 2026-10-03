# Start Laravel dev server detached
$env:PHPRC = "C:\Users\Bassey\AppData\Local\Temp\opencode\php.ini"
$work = "C:\laragon\www\foodchain-empowerment-app-main"
$out = Join-Path $work "laravel-serve.log"
Remove-Item $out -ErrorAction SilentlyContinue
Start-Process -FilePath "C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" -ArgumentList "artisan serve --host=127.0.0.1 --port=8000" -WorkingDirectory $work -RedirectStandardOutput $out -RedirectStandardError $out -WindowStyle Hidden
Write-Output "server start attempted"
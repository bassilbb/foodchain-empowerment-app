@php
    $appCssUrl = vite_built_asset_url('resources/css/app.css');
@endphp
@if ($appCssUrl)
    <link rel="stylesheet" href="{{ $appCssUrl }}">
@endif

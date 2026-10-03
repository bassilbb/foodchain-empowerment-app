{{--
    Shared SEO head output: canonical, description, Open Graph, Twitter Card, JSON-LD.

    Optional parameters (all optional, sensible defaults provided):
      $seoTitle        string  Default: <title> content or app name.
      $seoDesc         string  Default: '' (tag omitted when empty).
      $seoUrl          string  Default: current URL without query string.
      $seoType         string  website | article | ...
      $seoImage        string  Absolute URL used by social shares.
      $seoSite         string  Site name (OG site_name).
      $seoAuthor       string  Article author (article type only).
      $seoPublished    string  ISO-8601 publish date (article type only).
      $seoModified     string  ISO-8601 modified date (article type only).
      $seoSection      string  Article section/category.
      $schema          array   JSON-LD object(s) to emit verbatim.
--}}
@php
    $seoTitle = $seoTitle ?? (isset($__env) ? trim(strip_tags($__env->yieldContent('title') ?? '')) : '');
    if ($seoTitle === '' && isset($title)) { $seoTitle = $title; }
    $seoTitle = $seoTitle !== '' ? $seoTitle : config('app.name', 'FoodChain Community Empowerment Initiative');
    $seoDesc = $seoDesc ?? '';
    $seoUrl = $seoUrl ?? rtrim(url()->current(), '/');
    $seoType = $seoType ?? 'website';
    $seoImage = $seoImage ?? asset('assets/landing/images/FCCEILOGO.png');
    $seoSite = $seoSite ?? config('app.name', 'FoodChain Community Empowerment Initiative');
    $seoAuthor = $seoAuthor ?? null;
    $seoPublished = $seoPublished ?? null;
    $seoModified = $seoModified ?? null;
    $seoSection = $seoSection ?? 'News';
    $schema = $schema ?? [];
@endphp
<link rel="canonical" href="{{ $seoUrl }}" />
@if($seoDesc)
<meta name="description" content="{{ $seoDesc }}" />
@endif
<meta property="og:type" content="{{ $seoType }}" />
<meta property="og:site_name" content="{{ $seoSite }}" />
<meta property="og:title" content="{{ $seoTitle }}" />
@if($seoDesc)
<meta property="og:description" content="{{ $seoDesc }}" />
@endif
<meta property="og:url" content="{{ $seoUrl }}" />
<meta property="og:image" content="{{ $seoImage }}" />
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}" />
<meta name="twitter:card" content="{{ $seoType === 'article' || str_contains($seoImage, 'about') ? 'summary_large_image' : 'summary' }}" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
@if($seoDesc)
<meta name="twitter:description" content="{{ $seoDesc }}" />
@endif
<meta name="twitter:image" content="{{ $seoImage }}" />
@if($seoType === 'article')
<meta property="article:published_time" content="{{ $seoPublished }}" />
@if($seoModified)
<meta property="article:modified_time" content="{{ $seoModified }}" />
@endif
@if($seoAuthor)
<meta property="article:author" content="{{ $seoAuthor }}" />
@endif
<meta property="article:section" content="{{ $seoSection }}" />
@endif
@if(! empty($schema))
<script type="application/ld+json">{!! json_encode(array_values($schema), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif
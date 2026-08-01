@props([
    'profile',
    'services',
    'socialLinks',
    'activePage' => 'home',
    'title' => null,
    'description' => null,
    'seo' => null,
    'openGraphType' => 'website',
])

@php
    $fallbackTitle = ($profile?->public_name ?? config('app.name')).' | '.($title ?? __('company-profile.public.hero.marketing_title'));
    $pageTitle = $seo?->translated('meta_title') ?: $fallbackTitle;
    $pageDescription = $seo?->translated('meta_description') ?: ($description ?? __('company-profile.public.hero.marketing_description'));
    $canonicalUrl = $seo?->canonical_url ?: url()->current();
    $robots = (($seo?->robots_index ?? true) ? 'index' : 'noindex').','.(($seo?->robots_follow ?? true) ? 'follow' : 'nofollow');
    $openGraphTitle = $seo?->translated('open_graph_title') ?: $pageTitle;
    $openGraphDescription = $seo?->translated('open_graph_description') ?: $pageDescription;
    $structuredData = $seo?->structured_data
        ? json_encode($seo->structured_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        : null;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app('App\\Modules\\CompanyProfile\\Services\\LanguageResolver')->activeLanguages()->firstWhere('code', app()->getLocale())?->direction ?? 'ltr' }}"
    data-theme="{{ config('theme.default') }}" data-theme-default="{{ config('theme.default') }}"
    data-theme-options="{{ implode(',', array_keys(config('theme.themes'))) }}"
    data-theme-storage-key="{{ config('theme.storage_key') }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="analytics-endpoint" content="{{ route('analytics.events.store') }}">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="theme-color" content="#0aa8a7">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="{{ $openGraphType }}">
    <meta property="og:title" content="{{ $openGraphTitle }}">
    <meta property="og:description" content="{{ $openGraphDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:card" content="{{ $seo?->twitter_card ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $openGraphTitle }}">
    <meta name="twitter:description" content="{{ $openGraphDescription }}">
    <link rel="icon" type="{{ $profile?->faviconMedia?->mime_type ?? 'image/png' }}"
        href="{{ $profile?->faviconMedia
            ? $profile->faviconMedia->publicUrl().'?v='.$profile->faviconMedia->updated_at->timestamp
            : asset('vendor/bigspring/images/favicon.png') }}">
    <title>{{ $pageTitle }}</title>
    @if ($structuredData)
        <script type="application/ld+json">{!! $structuredData !!}</script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bigspring-home min-h-screen antialiased">
    <x-public.header :profile="$profile" :active-page="$activePage" />

    <main>
        {{ $slot }}
    </main>

    <x-public.footer :profile="$profile" :services="$services" :social-links="$socialLinks" />
</body>

</html>

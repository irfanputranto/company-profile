@props([
    'profile',
    'services',
    'socialLinks',
    'activePage' => 'home',
    'title' => null,
    'description' => null,
])

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
    <meta name="description" content="{{ $description ?? __('company-profile.public.hero.marketing_description') }}">
    <meta name="theme-color" content="#0aa8a7">
    <link rel="icon" type="{{ $profile?->faviconMedia?->mime_type ?? 'image/png' }}"
        href="{{ $profile?->faviconMedia
            ? $profile->faviconMedia->publicUrl().'?v='.$profile->faviconMedia->updated_at->timestamp
            : asset('vendor/bigspring/images/favicon.png') }}">
    <title>{{ $profile?->public_name ?? config('app.name') }} |
        {{ $title ?? __('company-profile.public.hero.marketing_title') }}</title>
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

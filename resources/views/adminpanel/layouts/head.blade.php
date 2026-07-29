<meta charset="utf-8" />
<meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="robots" content="noindex, nofollow" />
<title>{{ $title ?? '-' }} | {{ config('app.name', 'Laravel Skeleton') }}</title>
<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

<meta name="description" content="{{ config('app.description', 'Laravel application skeleton') }}" />
<meta name="keywords" content="{{ config('app.keywords', 'Laravel, application, skeleton') }}" />
<meta name="author" content="{{ config('app.author', 'Laravel Skeleton') }}" />
<meta name="canonical" content="{{ config('app.url') }}" />
<meta name="language" content="{{ app()->getLocale() }}" />
<meta name="theme-color" content="{{ config('app.theme', '#2f2838') }}" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

@vite(['resources/css/app.css', 'resources/js/app.js'])

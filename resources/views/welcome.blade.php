<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ config('theme.default') }}"
    data-theme-default="{{ config('theme.default') }}"
    data-theme-options="{{ implode(',', array_keys(config('theme.themes'))) }}"
    data-theme-storage-key="{{ config('theme.storage_key') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Laravel application skeleton dengan autentikasi, RBAC, upload privat, activity log, dan keamanan siap pakai.">
    <title>{{ config('app.name') }} | Laravel Application Skeleton</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="min-h-screen bg-[radial-gradient(circle_at_15%_15%,rgba(34,211,238,0.16),transparent_28%),radial-gradient(circle_at_85%_25%,rgba(139,92,246,0.15),transparent_26%)]">
        <header class="mx-auto flex max-w-6xl items-center justify-between px-5 py-6 sm:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-cyan-400 text-slate-950"><span class="icon-[tabler--template] size-6"></span></span>
                <div><p class="font-bold text-white">{{ config('app.name') }}</p><p class="text-xs text-slate-400">Laravel application skeleton</p></div>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-full px-6">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary rounded-full px-6">Masuk</a>
            @endauth
        </header>

        <main>
            <section class="mx-auto grid max-w-6xl items-center gap-12 px-5 py-16 sm:px-8 lg:grid-cols-[1.2fr_0.8fr] lg:py-24">
                <div>
                    <span class="inline-flex rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-sm font-medium text-cyan-200">Fondasi siap untuk proyek berikutnya</span>
                    <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">Mulai dari fitur penting, lalu bangun domain Anda.</h1>
                    <p class="mt-6 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">Template Laravel yang netral dengan autentikasi, pengelolaan user, role dan permission, profil, upload privat, activity log, keamanan, Docker, serta fondasi import/export.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn btn-primary rounded-full px-7">Mulai sekarang</a>
                        <a href="#features" class="btn btn-outline rounded-full border-white/20 px-7 text-white hover:border-cyan-300">Lihat fondasi</a>
                    </div>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-cyan-950/40 backdrop-blur sm:p-8">
                    <div class="flex items-center gap-3 border-b border-white/10 pb-5"><span class="size-3 rounded-full bg-rose-400"></span><span class="size-3 rounded-full bg-amber-300"></span><span class="size-3 rounded-full bg-emerald-400"></span><span class="ml-2 text-sm text-slate-400">skeleton/app</span></div>
                    <div class="mt-6 space-y-4">
                        @foreach (['Authentication & profile', 'User, role & permission', 'Private file uploads', 'Activity logging', 'Security headers & throttling', 'Import & export foundation'] as $feature)
                            <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/70 px-4 py-3"><span class="icon-[tabler--circle-check-filled] size-5 text-emerald-400"></span><span>{{ $feature }}</span></div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="features" class="mx-auto max-w-6xl px-5 pb-20 sm:px-8">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['icon-[tabler--shield-lock]', 'Akses terkendali', 'RBAC berbasis Spatie Permission untuk mengatur akses dengan fleksibel.'],
                        ['icon-[tabler--photo-shield]', 'Upload privat', 'Validasi dan optimasi gambar dengan penyimpanan yang tidak terekspos langsung.'],
                        ['icon-[tabler--activity]', 'Audit siap pakai', 'Aktivitas penting tercatat dan dapat ditelusuri dari admin panel.'],
                    ] as [$icon, $title, $description])
                        <article class="rounded-2xl border border-white/10 bg-white/5 p-6"><span class="{{ $icon }} size-7 text-cyan-300"></span><h2 class="mt-4 text-lg font-bold text-white">{{ $title }}</h2><p class="mt-2 text-sm leading-6 text-slate-400">{{ $description }}</p></article>
                    @endforeach
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 px-5 py-6 text-center text-sm text-slate-500">© {{ date('Y') }} {{ config('app.name') }}.</footer>
    </div>
</body>
</html>

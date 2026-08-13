@props([
    'code',
    'title',
    'message',
    'retry' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ config('theme.default') }}"
    data-theme-default="{{ config('theme.default') }}"
    data-theme-options="{{ implode(',', array_keys(config('theme.themes'))) }}"
    data-theme-storage-key="{{ config('theme.storage_key') }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#087f7d">
    <title>{{ $code }} — {{ $title }} | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen overflow-x-hidden bg-[#edf6f5] font-sans text-[#17212b] antialiased">
    <main class="relative isolate flex min-h-screen items-center overflow-hidden px-4 py-8 sm:px-6 lg:px-8">
        <div class="pointer-events-none absolute inset-0 -z-20 bg-[radial-gradient(circle_at_12%_12%,rgba(10,168,167,0.18),transparent_26%),radial-gradient(circle_at_88%_82%,rgba(255,123,109,0.14),transparent_25%)]"></div>
        <div class="pointer-events-none absolute inset-0 -z-10 opacity-35 [background-image:linear-gradient(rgba(8,127,125,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(8,127,125,0.08)_1px,transparent_1px)] [background-size:32px_32px] [mask-image:linear-gradient(to_bottom,black,transparent_82%)]"></div>

        <section {{ $attributes->class(['mx-auto grid w-full max-w-6xl overflow-hidden rounded-[2rem] border border-white/80 bg-white/85 shadow-[0_32px_90px_rgba(23,33,43,0.14)] backdrop-blur md:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)]']) }}>
            <div class="relative flex min-h-80 items-center justify-center overflow-hidden bg-[#dff2ef] p-6 sm:min-h-96 sm:p-10 md:min-h-[36rem]">
                <span class="absolute left-7 top-7 size-3 rounded-full bg-[#ff7b6d] shadow-[0_0_0_7px_rgba(255,123,109,0.14)]"></span>
                <span class="absolute bottom-10 right-10 size-4 rounded-full bg-[#087f7d] shadow-[0_0_0_9px_rgba(8,127,125,0.12)]"></span>
                <img src="{{ asset('assets/img/illustrations/error-anime.svg') }}" alt=""
                    class="relative z-10 w-full max-w-lg drop-shadow-[0_24px_30px_rgba(8,127,125,0.16)]"
                    aria-hidden="true">
            </div>

            <div class="flex flex-col justify-center p-7 sm:p-12 lg:p-16">
                <a href="{{ url('/') }}" class="group mb-10 inline-flex w-fit items-center gap-3 rounded-full focus:outline-none focus-visible:ring-4 focus-visible:ring-[#0aa8a7]/25">
                    <span class="flex size-11 items-center justify-center rounded-2xl bg-[#087f7d] text-white shadow-lg shadow-[#087f7d]/20 transition group-hover:-translate-y-0.5">
                        <span class="icon-[tabler--sparkles] size-5" aria-hidden="true"></span>
                    </span>
                    <span>
                        <span class="block text-base font-extrabold tracking-tight text-[#17212b]">{{ config('app.name') }}</span>
                        <span class="block text-[0.65rem] font-bold uppercase tracking-[0.18em] text-[#087f7d]">{{ __('errors.error_center') }}</span>
                    </span>
                </a>

                <div class="flex items-center gap-3">
                    <span class="rounded-full bg-[#087f7d]/10 px-3 py-1 text-xs font-extrabold uppercase tracking-[0.18em] text-[#087f7d]">
                        {{ __('errors.status') }} {{ $code }}
                    </span>
                    <span class="h-px w-12 bg-[#087f7d]/25"></span>
                </div>

                <h1 class="mt-6 max-w-xl text-4xl font-black leading-[1.08] tracking-[-0.04em] text-[#17212b] sm:text-5xl">
                    {{ $title }}
                </h1>
                <p class="mt-5 max-w-lg text-base leading-7 text-[#52616b] sm:text-lg sm:leading-8">
                    {{ $message }}
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ url('/') }}"
                        class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#087f7d] px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-[#087f7d]/20 transition hover:-translate-y-0.5 hover:bg-[#066b69] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#0aa8a7]/30">
                        <span class="icon-[tabler--home] size-5" aria-hidden="true"></span>
                        {{ __('errors.back_home') }}
                    </a>

                    @if ($retry)
                        <a href="{{ request()->fullUrl() }}"
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-[#087f7d]/25 bg-white px-6 py-3 text-sm font-extrabold text-[#087f7d] transition hover:-translate-y-0.5 hover:border-[#087f7d]/50 hover:bg-[#effaf8] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#0aa8a7]/20">
                            <span class="icon-[tabler--refresh] size-5" aria-hidden="true"></span>
                            {{ __('errors.try_again') }}
                        </a>
                    @endif
                </div>

                <p class="mt-10 flex items-center gap-2 text-xs font-semibold text-[#718087]">
                    <span class="size-2 rounded-full bg-[#0aa8a7]"></span>
                    {{ __('errors.reassurance') }}
                </p>
            </div>
        </section>
    </main>
</body>

</html>

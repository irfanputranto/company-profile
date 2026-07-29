@props([
    'model',
    'title',
    'description',
    'eyebrow' => null,
    'icon' => 'icon-[tabler--book-2]',
    'titleId' => 'report-guide-title',
    'dialogId' => null,
    'confirmLabel' => null,
])

@php
    $eyebrow ??= __('admin.guide.report_eyebrow');
    $confirmLabel ??= __('admin.guide.understood');
@endphp

<template x-teleport="body">
    <div
        @if ($dialogId) id="{{ $dialogId }}" @endif
        x-cloak
        x-show="{{ $model }}"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 p-3 backdrop-blur-sm sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $titleId }}"
        @keydown.escape.window="{{ $model }} = false"
    >
        <div class="absolute inset-0" aria-hidden="true" @click="{{ $model }} = false"></div>

        <section
            x-show="{{ $model }}"
            x-transition:enter="transition duration-200 ease-out"
            x-transition:enter-start="translate-y-4 scale-[0.98] opacity-0"
            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
            x-transition:leave="transition duration-150 ease-in"
            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
            x-transition:leave-end="translate-y-4 scale-[0.98] opacity-0"
            class="relative z-10 flex max-h-[calc(100dvh-1.5rem)] w-full max-w-6xl flex-col overflow-hidden rounded-3xl border border-base-content/10 bg-base-100 shadow-2xl sm:max-h-[calc(100dvh-3rem)]"
        >
            <header class="relative isolate shrink-0 border-b border-white/15 bg-gradient-to-r from-emerald-700 via-teal-700 to-cyan-800 px-5 py-6 text-white sm:px-7 sm:py-7">
                <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden rounded-t-3xl" aria-hidden="true">
                    <div class="absolute -right-12 -top-20 size-56 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-24 right-32 size-48 rounded-full border border-white/15"></div>
                </div>

                <div class="grid grid-cols-[minmax(0,1fr)_auto] items-start gap-4 sm:gap-6">
                    <div class="flex min-w-0 items-start gap-4">
                        <div class="hidden size-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/25 sm:flex">
                            <span class="{{ $icon }} size-7" aria-hidden="true"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase leading-5 tracking-[0.2em] text-emerald-100">{{ $eyebrow }}</p>
                            <h2 id="{{ $titleId }}" class="mt-1 break-words text-xl font-bold leading-tight sm:text-2xl">{{ $title }}</h2>
                            <p class="mt-2 max-w-4xl break-words text-sm leading-6 text-emerald-50/90">{{ $description }}</p>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn btn-circle btn-sm shrink-0 border-white/20 bg-white/10 text-white hover:bg-white/20"
                        title="{{ __('admin.guide.close_title') }}"
                        aria-label="{{ __('admin.guide.close_title') }}"
                        @click="{{ $model }} = false"
                    >
                        <span class="icon-[tabler--x] size-5" aria-hidden="true"></span>
                    </button>
                </div>
            </header>

            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-5 sm:p-7">
                {{ $slot }}
            </div>

            <footer class="flex shrink-0 items-center justify-between gap-3 border-t border-base-content/10 bg-base-200/40 px-5 py-4 sm:px-7">
                <p class="hidden text-xs text-base-content/55 sm:block">{{ __('admin.guide.close_hint') }}</p>
                <button type="button" class="btn btn-primary ms-auto" @click="{{ $model }} = false">
                    <span class="icon-[tabler--check] size-5" aria-hidden="true"></span>
                    {{ $confirmLabel }}
                </button>
            </footer>
        </section>
    </div>
</template>

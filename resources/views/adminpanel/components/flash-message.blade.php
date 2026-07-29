@props([
    'message' => null,
    'type' => null,
    'duration' => 6500,
])

@php
    $message ??= session('success_message') ?? session('error_message');
    $type ??= session('error_message') ? 'error' : 'success';
    $type = in_array($type, ['success', 'error', 'warning', 'info'], true) ? $type : 'info';
    $displayDuration = in_array($type, ['error', 'warning'], true)
        ? max((int) $duration, 9000)
        : max((int) $duration, 6500);

    $style = match ($type) {
        'error' => [
            'panel' => 'border-[#dc2626] border-s-4 bg-[#fee2e2] text-[#7f1d1d] shadow-[0_12px_30px_rgba(127,29,29,0.22)]',
            'iconBox' => 'bg-[#dc2626] text-white',
            'icon' => 'icon-[tabler--alert-triangle]',
            'title' => __('admin.flash.error'),
            'role' => 'alert',
            'live' => 'assertive',
        ],
        'warning' => [
            'panel' => 'border-[#d97706] border-s-4 bg-[#fef3c7] text-[#78350f] shadow-[0_12px_30px_rgba(120,53,15,0.22)]',
            'iconBox' => 'bg-[#f59e0b] text-[#451a03]',
            'icon' => 'icon-[tabler--alert-circle]',
            'title' => __('admin.flash.warning'),
            'role' => 'alert',
            'live' => 'assertive',
        ],
        'info' => [
            'panel' => 'border-[#2563eb] border-s-4 bg-[#dbeafe] text-[#1e3a8a] shadow-[0_12px_30px_rgba(30,58,138,0.22)]',
            'iconBox' => 'bg-[#2563eb] text-white',
            'icon' => 'icon-[tabler--info-circle]',
            'title' => __('admin.flash.info'),
            'role' => 'status',
            'live' => 'polite',
        ],
        default => [
            'panel' => 'border-[#16a34a] border-s-4 bg-[#dcfce7] text-[#14532d] shadow-[0_12px_30px_rgba(20,83,45,0.22)]',
            'iconBox' => 'bg-[#16a34a] text-white',
            'icon' => 'icon-[tabler--circle-check]',
            'title' => __('admin.flash.success'),
            'role' => 'status',
            'live' => 'polite',
        ],
    };
@endphp

@if ($message)
    <div x-data="{ open: true }" x-show="open" x-init="setTimeout(() => open = false, {{ $displayDuration }})"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave-end="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        class="pointer-events-none fixed inset-x-4 top-4 z-[100] flex justify-end sm:start-auto sm:top-20 sm:end-5 sm:w-full sm:max-w-sm"
        role="{{ $style['role'] }}" aria-live="{{ $style['live'] }}" aria-atomic="true">
        <div {{ $attributes->class([
            'pointer-events-auto flex w-full items-center gap-3.5 rounded-xl border p-4',
            $style['panel'],
        ]) }}>
            <span class="{{ $style['iconBox'] }} flex size-10 shrink-0 items-center justify-center rounded-lg shadow-md"
                aria-hidden="true">
                <span class="{{ $style['icon'] }} size-5.5"></span>
            </span>

            <div class="min-w-0 flex-1 self-center">
                <p class="text-sm font-extrabold leading-5">{{ $style['title'] }}</p>
                <p class="mt-1 text-sm font-semibold leading-5">{{ $message }}</p>
            </div>

            <button type="button" x-on:click="open = false"
                class="flex size-8 shrink-0 items-center justify-center rounded-lg text-current transition-colors hover:bg-black/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-current"
                title="{{ __('admin.common.close_notification') }}" aria-label="{{ __('admin.common.close_notification') }}">
                <span class="icon-[tabler--x] size-4.5"></span>
            </button>
        </div>
    </div>
@endif

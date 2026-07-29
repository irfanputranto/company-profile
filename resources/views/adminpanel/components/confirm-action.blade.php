@props([
    'action',
    'title',
    'description',
    'confirmLabel' => null,
    'buttonLabel' => null,
    'icon' => 'icon-[tabler--check]',
    'buttonClass' => 'btn-primary',
    'reason' => false,
    'tooltip' => null,
])

@php
    $confirmLabel ??= __('admin.confirm_action.confirm');
    $dialogTitleId = 'confirm-action-' . md5($action . $title);
    $buttonTooltip = $tooltip ?? rtrim($title, '?');
@endphp

<div x-data="confirmDelete" {{ $attributes->merge(['class' => 'inline-flex']) }}>
    <button type="button" class="btn btn-sm {{ $buttonLabel ? '' : 'btn-square' }} {{ $buttonClass }}" @click="show" title="{{ $buttonTooltip }}" aria-label="{{ $buttonTooltip }}">
        <span class="{{ $icon }} size-5"></span>@if($buttonLabel)<span>{{ $buttonLabel }}</span>@endif
    </button>
    <template x-teleport="body">
        <div x-cloak x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="{{ $dialogTitleId }}" @keydown.escape.window="close">
            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/55 backdrop-blur-[2px]" @click="close"></div>
            <div x-show="open" x-transition class="bg-base-100 relative z-10 w-full max-w-md rounded-2xl p-6 text-center shadow-2xl">
                <div class="bg-primary/10 text-primary mx-auto flex size-16 items-center justify-center rounded-full"><span class="{{ $icon }} size-8"></span></div>
                <h2 id="{{ $dialogTitleId }}" class="text-base-content mt-5 text-xl font-semibold">{{ $title }}</h2>
                <p class="text-base-content/60 mt-2 text-sm leading-6">{{ $description }}</p>
                <form method="POST" action="{{ $action }}" class="mt-5">@csrf @if($reason)<textarea name="reason" rows="3" class="textarea w-full text-start" minlength="5" maxlength="1000" placeholder="{{ __('admin.confirm_action.reason_placeholder') }}" required></textarea>@endif<div class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center"><button type="button" class="btn btn-soft min-w-28" @click="close">{{ __('admin.common.cancel') }}</button><button type="submit" class="btn btn-primary min-w-28"><span class="{{ $icon }} size-5"></span>{{ $confirmLabel }}</button></div></form>
            </div>
        </div>
    </template>
</div>

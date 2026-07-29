@props([
    'title',
    'description' => null,
    'actionsFirst' => false,
])

<div {{ $attributes->merge(['class' => 'flex w-full flex-col gap-4 md:flex-row md:items-start md:justify-between']) }}>
    @if ($actionsFirst && isset($actions))
        <div class="flex w-full flex-wrap items-center gap-2 md:w-auto md:shrink-0">
            {{ $actions }}
        </div>
    @endif

    <div class="min-w-0 {{ $actionsFirst ? 'md:text-end' : '' }}">
        <h1 class="text-base-content text-xl font-semibold tracking-tight sm:text-2xl">{{ $title }}</h1>
        @if ($description)
            <p class="text-base-content/60 mt-1 max-w-2xl text-sm leading-5">{{ $description }}</p>
        @endif
    </div>

    @if (! $actionsFirst && isset($actions))
        <div class="flex w-full flex-wrap items-center gap-2 md:w-auto md:shrink-0 md:justify-end">
            {{ $actions }}
        </div>
    @endif
</div>

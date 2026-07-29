@props([
    'user' => auth()->user(),
    'size' => 'md',
    'imageUrl' => null,
])

@php
    $displayName = trim((string) ($user?->name ?: 'Pengguna'));
    $nameParts = \Illuminate\Support\Str::of($displayName)->squish()->explode(' ')->filter()->values();
    $initials = $nameParts->count() > 1
        ? \Illuminate\Support\Str::substr($nameParts->first(), 0, 1) . \Illuminate\Support\Str::substr($nameParts->last(), 0, 1)
        : \Illuminate\Support\Str::substr($nameParts->first() ?: 'P', 0, 1);
    $initials = \Illuminate\Support\Str::upper($initials);
    $imageUrl ??= $user?->avatarUrl();
    $sizeClass = match ($size) {
        'sm' => 'size-9 text-xs',
        'lg' => 'size-17 text-xl',
        'xl' => 'size-24 text-2xl',
        default => 'size-10 text-sm',
    };
@endphp

<span x-data="{ failed: false }" {{ $attributes->merge(['class' => "avatar avatar-placeholder shrink-0 {$sizeClass}"]) }}>
    <span class="bg-primary text-primary-content size-full rounded-full font-semibold">
        @if ($imageUrl)
            <img x-show="!failed" src="{{ $imageUrl }}" alt="Foto {{ $displayName }}"
                class="size-full rounded-full object-cover" x-on:error="failed = true">
        @endif
        <span x-show="{{ $imageUrl ? 'failed' : 'true' }}" aria-hidden="true">{{ $initials }}</span>
        <span class="sr-only">Avatar {{ $displayName }}</span>
    </span>
</span>

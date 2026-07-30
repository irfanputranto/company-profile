@props(['profile'])

@if ($profile?->logoMedia)
    <img src="{{ $profile->logoMedia->publicUrl().'?v='.$profile->logoMedia->updated_at->timestamp }}"
        alt="" width="36" height="36" {{ $attributes->class('bg-white object-contain') }}>
@else
    <span {{ $attributes->class('flex items-center justify-center bg-[#0aa8a7] text-white') }}>
        <span class="icon-[tabler--code] size-5"></span>
    </span>
@endif

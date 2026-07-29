@props(['mobileLabel' => 'Geser tabel ke samping untuk melihat data lainnya.'])

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <p class="text-base-content/50 border-base-content/10 border-b px-4 py-2 text-xs sm:hidden">
        <span class="icon-[tabler--arrows-horizontal] me-1 inline-block size-4 align-text-bottom"></span>
        {{ $mobileLabel }}
    </p>
    <div class="w-full overflow-x-auto overscroll-x-contain">
        {{ $slot }}
    </div>
</div>

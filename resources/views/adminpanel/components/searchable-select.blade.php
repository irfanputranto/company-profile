@props([
    'placeholder' => 'Pilih data',
    'searchPlaceholder' => 'Cari data...',
    'noResultsText' => 'Data tidak ditemukan',
    'detailed' => false,
    'dark' => false,
    'invalid' => false,
])

@php
    $darkToggleClasses = $invalid
        ? 'border-error ring-2 ring-error/20'
        : 'border-[#766d80] focus:border-primary focus:ring-2 focus:ring-primary/25';

    $toggleTag = $detailed
        ? '<button type="button" aria-expanded="false"><span class="block min-w-0 overflow-visible! text-clip! whitespace-pre-line! break-words text-left leading-5" data-title></span></button>'
        : '<button type="button" aria-expanded="false"></button>';

    $configuration = [
        'placeholder' => $placeholder,
        'toggleTag' => $toggleTag,
        'toggleClasses' => $dark
            ? 'advance-select-toggle min-h-12 rounded-lg bg-[#352e3e] py-2.5 pe-10 ps-4 text-left text-white select-disabled:pointer-events-none select-disabled:opacity-40 '.$darkToggleClasses
            : 'advance-select-toggle select-disabled:pointer-events-none select-disabled:opacity-40',
        'hasSearch' => true,
        'searchPlaceholder' => $searchPlaceholder,
        'searchNoResultText' => $noResultsText,
        'dropdownClasses' => $dark
            ? 'advance-select-menu max-h-72 overflow-y-auto border border-[#766d80] bg-[#352e3e] pt-0 text-white shadow-2xl shadow-black/40'
            : 'advance-select-menu max-h-60 overflow-y-auto pt-0',
        'optionClasses' => $dark
            ? 'advance-select-option selected:select-active px-3 py-3 text-white hover:bg-white/5'
            : 'advance-select-option selected:select-active',
        'optionTemplate' => $detailed
            ? '<div class="flex w-full min-w-0 items-start gap-3"><span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/15 text-primary"><span class="icon-[tabler--building-store] size-5"></span></span><span class="min-w-0 grow text-left"><span class="block whitespace-normal break-words font-semibold leading-5" data-title></span><span class="mt-1 block whitespace-normal break-words text-xs leading-4 text-[#c9c1d0] empty:hidden" data-description></span></span><span class="icon-[tabler--check] mt-1 hidden size-4 shrink-0 text-primary selected:block"></span></div>'
            : '<div class="flex w-full items-center justify-between gap-2"><span class="truncate" data-title></span><span class="icon-[tabler--check] text-primary hidden size-4 shrink-0 selected:block"></span></div>',
        'extraMarkup' => '<span class="icon-[tabler--chevron-down] '.($dark ? 'text-[#c9c1d0]' : 'text-base-content').' pointer-events-none absolute end-3 top-1/2 size-4 shrink-0 -translate-y-1/2"></span>',
    ];

    if ($dark) {
        $configuration['searchWrapperClasses'] = 'sticky top-0 z-10 border-b border-white/10 bg-[#352e3e] px-3 py-3';
        $configuration['searchClasses'] = 'block w-full rounded-lg border border-[#766d80] bg-[#292331] px-3 py-2.5 text-sm text-white outline-none placeholder:text-[#aaa1b2] focus:border-primary focus:ring-2 focus:ring-primary/25';
    }
@endphp

<select data-select='{!! json_encode($configuration, JSON_HEX_APOS | JSON_HEX_AMP) !!}'
    {{ $attributes->class(['hidden']) }}>
    {{ $slot }}
</select>

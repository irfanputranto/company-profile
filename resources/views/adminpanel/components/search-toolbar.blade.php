@props([
    'action',
    'name' => 'q',
    'value' => null,
    'placeholder' => 'Cari data...',
    'perPage' => 10,
    'preserve' => [],
])

<form method="GET" action="{{ $action }}" x-data="dataTableFilters" x-ref="filterForm"
    {{ $attributes->merge(['class' => 'w-full']) }}>
    @foreach ($preserve as $preserveName => $preserveValue)
        @if (filled($preserveValue))
            <input type="hidden" name="{{ $preserveName }}" value="{{ $preserveValue }}">
        @endif
    @endforeach

    <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2 text-sm">
                <span class="text-base-content/60 whitespace-nowrap">Tampilkan</span>
                <select name="limit" class="select select-bordered select-sm w-20" aria-label="Jumlah data per halaman"
                    @change="applyFilters">
                    @foreach ([10, 25, 50, 100] as $option)
                        <option value="{{ $option }}" @selected((int) $perPage === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <span class="text-base-content/60">data</span>
            </div>
        </div>

        <div class="flex w-full items-center sm:w-auto">
            <label class="input input-bordered flex h-10 w-full items-center gap-3 px-3 sm:w-80 xl:w-96">
                <span class="icon-[tabler--search] text-base-content/50 size-4 shrink-0"></span>
                <input name="{{ $name }}" type="search" value="{{ $value }}"
                    class="grow text-sm bg-transparent border-0 focus:outline-none focus:ring-0"
                    placeholder="{{ $placeholder }}" autocomplete="off" aria-label="Pencarian"
                    @input.debounce.500ms="applyFilters">
            </label>
        </div>

    </div>
</form>

@props(['paginator'])

@if ($paginator->total() > 0)
    <nav {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between gap-3']) }}
        aria-label="Navigasi halaman">
        <p class="text-base-content/60 me-auto text-sm">
            Menampilkan <span class="text-base-content font-medium">{{ $paginator->firstItem() }}</span>–<span
                class="text-base-content font-medium">{{ $paginator->lastItem() }}</span> dari
            <span class="text-base-content font-medium">{{ $paginator->total() }}</span> data
        </p>

        @if ($paginator->hasPages())
            @php
                $visiblePages = collect([
                    1,
                    $paginator->currentPage() - 1,
                    $paginator->currentPage(),
                    $paginator->currentPage() + 1,
                    $paginator->lastPage(),
                ])->filter(fn ($page) => $page >= 1 && $page <= $paginator->lastPage())->unique()->sort()->values();
                $previousVisiblePage = null;
            @endphp

            <div class="ms-auto flex items-center gap-x-1">
                @if ($paginator->onFirstPage())
                    <span class="btn btn-soft max-sm:btn-square btn-disabled" title="Tidak ada halaman sebelumnya" aria-disabled="true">
                        <span class="icon-[tabler--chevron-left] size-5 rtl:rotate-180 sm:hidden"></span>
                        <span class="hidden sm:inline">Previous</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-soft max-sm:btn-square"
                        title="Halaman sebelumnya" aria-label="Halaman sebelumnya">
                        <span class="icon-[tabler--chevron-left] size-5 rtl:rotate-180 sm:hidden"></span>
                        <span class="hidden sm:inline">Previous</span>
                    </a>
                @endif

                <div class="flex items-center gap-x-1">
                    @foreach ($visiblePages as $page)
                        @if ($previousVisiblePage !== null && $page - $previousVisiblePage > 1)
                            @php
                                $skippedPages = $page - $previousVisiblePage - 1;
                                $jumpPage = $previousVisiblePage + 1;
                            @endphp
                            <div class="tooltip inline-block">
                                <a href="{{ $paginator->url($jumpPage) }}"
                                    class="tooltip-toggle btn btn-soft btn-square group" title="Halaman lainnya" aria-label="Halaman lainnya">
                                    <span class="icon-[tabler--dots] size-5 group-hover:hidden"></span>
                                    <span
                                        class="icon-[tabler--chevrons-right] hidden size-5 shrink-0 group-hover:block rtl:rotate-180"></span>
                                    <span class="tooltip-content tooltip-shown:visible tooltip-shown:opacity-100" role="tooltip">
                                        <span class="tooltip-body">Next {{ $skippedPages }} pages</span>
                                    </span>
                                </a>
                            </div>
                        @endif

                        <a href="{{ $paginator->url($page) }}"
                            class="btn btn-soft btn-square aria-[current='page']:text-bg-soft-primary"
                            title="Halaman {{ $page }}" aria-label="Halaman {{ $page }}" @if ($page === $paginator->currentPage()) aria-current="page" @endif>
                            {{ $page }}
                        </a>

                        @php($previousVisiblePage = $page)
                    @endforeach
                </div>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-soft max-sm:btn-square"
                        title="Halaman berikutnya" aria-label="Halaman berikutnya">
                        <span class="hidden sm:inline">Next</span>
                        <span class="icon-[tabler--chevron-right] size-5 rtl:rotate-180 sm:hidden"></span>
                    </a>
                @else
                    <span class="btn btn-soft max-sm:btn-square btn-disabled" title="Tidak ada halaman berikutnya" aria-disabled="true">
                        <span class="hidden sm:inline">Next</span>
                        <span class="icon-[tabler--chevron-right] size-5 rtl:rotate-180 sm:hidden"></span>
                    </span>
                @endif
            </div>
        @endif
    </nav>
@endif

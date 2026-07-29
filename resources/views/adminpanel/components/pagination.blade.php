@props(['paginator'])

@if ($paginator->total() > 0)
    <nav {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between gap-3']) }}
        aria-label="{{ __('admin.pagination.navigation') }}">
        <p class="text-base-content/60 me-auto text-sm">
            {{ __('admin.pagination.summary', [
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]) }}
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
                    <span class="btn btn-soft max-sm:btn-square btn-disabled" title="{{ __('admin.pagination.no_previous') }}" aria-disabled="true">
                        <span class="icon-[tabler--chevron-left] size-5 rtl:rotate-180 sm:hidden"></span>
                        <span class="hidden sm:inline">{{ __('admin.pagination.previous') }}</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-soft max-sm:btn-square"
                        title="{{ __('admin.pagination.previous_page') }}" aria-label="{{ __('admin.pagination.previous_page') }}">
                        <span class="icon-[tabler--chevron-left] size-5 rtl:rotate-180 sm:hidden"></span>
                        <span class="hidden sm:inline">{{ __('admin.pagination.previous') }}</span>
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
                                    class="tooltip-toggle btn btn-soft btn-square group" title="{{ __('admin.pagination.more') }}" aria-label="{{ __('admin.pagination.more') }}">
                                    <span class="icon-[tabler--dots] size-5 group-hover:hidden"></span>
                                    <span
                                        class="icon-[tabler--chevrons-right] hidden size-5 shrink-0 group-hover:block rtl:rotate-180"></span>
                                    <span class="tooltip-content tooltip-shown:visible tooltip-shown:opacity-100" role="tooltip">
                                        <span class="tooltip-body">{{ __('admin.pagination.next_pages', ['count' => $skippedPages]) }}</span>
                                    </span>
                                </a>
                            </div>
                        @endif

                        <a href="{{ $paginator->url($page) }}"
                            class="btn btn-soft btn-square aria-[current='page']:text-bg-soft-primary"
                            title="{{ __('admin.pagination.page', ['page' => $page]) }}" aria-label="{{ __('admin.pagination.page', ['page' => $page]) }}" @if ($page === $paginator->currentPage()) aria-current="page" @endif>
                            {{ $page }}
                        </a>

                        @php($previousVisiblePage = $page)
                    @endforeach
                </div>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-soft max-sm:btn-square"
                        title="{{ __('admin.pagination.next_page') }}" aria-label="{{ __('admin.pagination.next_page') }}">
                        <span class="hidden sm:inline">{{ __('admin.pagination.next') }}</span>
                        <span class="icon-[tabler--chevron-right] size-5 rtl:rotate-180 sm:hidden"></span>
                    </a>
                @else
                    <span class="btn btn-soft max-sm:btn-square btn-disabled" title="{{ __('admin.pagination.no_next') }}" aria-disabled="true">
                        <span class="hidden sm:inline">{{ __('admin.pagination.next') }}</span>
                        <span class="icon-[tabler--chevron-right] size-5 rtl:rotate-180 sm:hidden"></span>
                    </span>
                @endif
            </div>
        @endif
    </nav>
@endif

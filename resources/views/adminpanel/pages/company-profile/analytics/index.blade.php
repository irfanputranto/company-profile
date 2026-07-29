<x-adminpanel::layouts.app :title="__('admin.analytics.title')">
    <x-adminpanel::components.page-header :title="__('admin.analytics.title')" :description="__('admin.analytics.description')" />

    <form method="GET" action="{{ route('company-profile.analytics.index') }}"
        class="card mt-6 grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-4">
        <select name="period" class="select">
            @foreach (['day', 'week', 'month', 'year'] as $value)
                <option value="{{ $value }}" @selected($period === $value)>{{ __("admin.analytics.{$value}") }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="input" aria-label="{{ __('admin.analytics.start_date') }}">
        <input type="date" name="to" value="{{ request('to') }}" class="input" aria-label="{{ __('admin.analytics.end_date') }}">
        <button class="btn btn-primary"><span class="icon-[tabler--filter] size-5"></span>{{ __('admin.analytics.apply') }}</button>
    </form>

    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ([
            ['label' => __('admin.analytics.page_views'), 'value' => $totals->page_views, 'icon' => 'icon-[tabler--eye]'],
            ['label' => __('admin.analytics.unique_visitors'), 'value' => $totals->unique_visitors, 'icon' => 'icon-[tabler--users]'],
            ['label' => __('admin.analytics.sessions'), 'value' => $totals->sessions, 'icon' => 'icon-[tabler--activity]'],
        ] as $metric)
            <article class="card p-5 shadow-md">
                <div class="flex items-center gap-4">
                    <span class="bg-primary/10 text-primary flex size-11 items-center justify-center rounded-xl">
                        <span class="{{ $metric['icon'] }} size-6"></span>
                    </span>
                    <div>
                        <p class="text-base-content/60 text-sm">{{ $metric['label'] }}</p>
                        <p class="mt-1 text-2xl font-semibold">{{ \Illuminate\Support\Number::format($metric['value']) }}</p>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        @foreach ([
            ['title' => __('admin.analytics.menu_activity'), 'items' => $menuStats, 'icon' => 'icon-[tabler--click]'],
            ['title' => __('admin.analytics.section_activity'), 'items' => $sectionStats, 'icon' => 'icon-[tabler--viewport-wide]'],
        ] as $eventGroup)
            <article class="card overflow-hidden shadow-md">
                <header class="border-base-content/10 flex items-center gap-3 border-b px-5 py-4">
                    <span class="bg-primary/10 text-primary flex size-9 items-center justify-center rounded-lg">
                        <span class="{{ $eventGroup['icon'] }} size-5"></span>
                    </span>
                    <h2 class="font-semibold">{{ $eventGroup['title'] }}</h2>
                </header>
                <x-adminpanel::components.responsive-table>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('admin.analytics.event') }}</th>
                                <th class="text-end">{{ __('admin.analytics.interactions') }}</th>
                                <th class="text-end">{{ __('admin.analytics.unique_visitors') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($eventGroup['items'] as $event)
                                <tr>
                                    <td class="font-medium">{{ $event['label'] }}</td>
                                    <td class="text-end">{{ \Illuminate\Support\Number::format($event['page_views']) }}</td>
                                    <td class="text-end">{{ \Illuminate\Support\Number::format($event['unique_visitors']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-base-content/55 py-10 text-center">
                                        {{ __('admin.analytics.no_interactions') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-adminpanel::components.responsive-table>
            </article>
        @endforeach
    </section>

    <section class="card shadow-base-300/10 mt-6 overflow-hidden shadow-md">
        <x-adminpanel::components.responsive-table>
            <table class="table min-w-[700px]">
                <thead><tr><th>{{ __('admin.analytics.period') }}</th><th class="text-end">{{ __('admin.analytics.page_views') }}</th><th class="text-end">{{ __('admin.analytics.unique_visitors') }}</th><th class="text-end">{{ __('admin.analytics.sessions') }}</th></tr></thead>
                <tbody>
                    @forelse ($list as $aggregate)
                        <tr>
                            <td class="font-medium">{{ $aggregate->period_start->translatedFormat('d M Y') }}</td>
                            <td class="text-end">{{ \Illuminate\Support\Number::format($aggregate->page_views) }}</td>
                            <td class="text-end">{{ \Illuminate\Support\Number::format($aggregate->unique_visitors) }}</td>
                            <td class="text-end">{{ \Illuminate\Support\Number::format($aggregate->sessions) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-14 text-center"><span class="icon-[tabler--chart-bar-off] text-base-content/30 mx-auto size-10"></span><p class="mt-3 font-medium">{{ __('admin.analytics.empty') }}</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-adminpanel::components.responsive-table>
        <div class="border-base-content/10 border-t px-4 py-4 sm:px-5">
            <x-adminpanel::components.pagination :paginator="$list" />
        </div>
    </section>
</x-adminpanel::layouts.app>

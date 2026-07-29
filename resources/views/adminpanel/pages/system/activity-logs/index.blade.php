<x-adminpanel::layouts.app :title="__('admin.activity_log.title')">
    <div class="space-y-5 sm:space-y-6">
        <x-adminpanel::components.page-header :title="__('admin.activity_log.title')"
            :description="__('admin.activity_log.description')" />

        <section class="card overflow-hidden border border-base-content/10 shadow-sm">
            <form method="GET" action="{{ route('system.activity-logs.index') }}"
                class="grid grid-cols-1 gap-4 border-b border-base-content/10 bg-base-200/40 p-4 sm:grid-cols-2 sm:p-5 xl:grid-cols-6">
                <label class="form-control xl:col-span-2">
                    <span class="label-text mb-1.5">{{ __('admin.activity_log.search') }}</span>
                    <input type="search" name="q" value="{{ request('q') }}" class="input"
                        placeholder="{{ __('admin.activity_log.search_placeholder') }}">
                </label>
                <label class="form-control">
                    <span class="label-text mb-1.5">{{ __('admin.activity_log.action') }}</span>
                    <select name="event" class="select">
                        <option value="">{{ __('admin.activity_log.all_actions') }}</option>
                        @foreach (['created', 'updated', 'deleted', 'restored'] as $value)
                            <option value="{{ $value }}" @selected(request('event') === $value)>{{ __("admin.activity_log.{$value}") }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="label-text mb-1.5">{{ __('admin.activity_log.user') }}</span>
                    <select name="causer_id" class="select">
                        <option value="">{{ __('admin.activity_log.all_users') }}</option>
                        @foreach ($causers as $causer)
                            <option value="{{ $causer->id }}" @selected((string) request('causer_id') === (string) $causer->id)>
                                {{ $causer->name }} ({{ $causer->username }})
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="label-text mb-1.5">{{ __('admin.activity_log.data_type') }}</span>
                    <select name="subject_type" class="select">
                        <option value="">{{ __('admin.activity_log.all_data') }}</option>
                        @foreach ($subjectTypes as $subjectType)
                            <option value="{{ $subjectType }}" @selected(request('subject_type') === $subjectType)>
                                {{ class_basename($subjectType) }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="label-text mb-1.5">{{ __('admin.activity_log.per_page') }}</span>
                    <select name="limit" class="select">
                        @foreach ([10, 25, 50, 100] as $limit)
                            <option value="{{ $limit }}" @selected((int) request('limit', 25) === $limit)>{{ $limit }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="label-text mb-1.5">{{ __('admin.activity_log.date_from') }}</span>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
                </label>
                <label class="form-control">
                    <span class="label-text mb-1.5">{{ __('admin.activity_log.date_to') }}</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
                </label>
                <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-4">
                    <button type="submit" class="btn btn-primary gap-2">
                        <span class="icon-[tabler--filter] size-4.5"></span>{{ __('admin.common.filter') }}
                    </button>
                    <a href="{{ route('system.activity-logs.index') }}" class="btn btn-soft">{{ __('admin.common.reset') }}</a>
                </div>
            </form>

            <x-adminpanel::components.responsive-table>
                <table class="table min-w-[980px]">
                    <thead>
                        <tr>
                            <th>{{ __('admin.activity_log.time') }}</th>
                            <th>{{ __('admin.activity_log.user') }}</th>
                            <th>{{ __('admin.activity_log.action') }}</th>
                            <th>{{ __('admin.activity_log.data') }}</th>
                            <th>{{ __('admin.activity_log.changes') }}</th>
                            <th class="text-center">{{ __('admin.activity_log.details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td class="whitespace-nowrap">
                                    <p class="font-medium">{{ $activity->created_at?->format('d/m/Y') }}</p>
                                    <p class="text-base-content/55 text-xs">{{ $activity->created_at?->format('H:i:s') }}</p>
                                </td>
                                <td>
                                    <p class="font-medium">{{ $activity->causer?->name ?? __('admin.activity_log.system') }}</p>
                                    <p class="text-base-content/55 text-xs">{{ $activity->causer?->username ?? __('admin.activity_log.no_user') }}</p>
                                </td>
                                <td>
                                    <span @class([
                                        'badge badge-soft',
                                        'badge-success' => $activity->event === 'created',
                                        'badge-info' => $activity->event === 'updated',
                                        'badge-error' => $activity->event === 'deleted',
                                        'badge-warning' => $activity->event === 'restored',
                                    ])>
                                        {{ __("admin.activity_log.{$activity->event}") }}
                                    </span>
                                </td>
                                <td>
                                    <p class="font-medium">{{ class_basename($activity->subject_type ?? __('admin.activity_log.system')) }}</p>
                                    <p class="text-base-content/55 text-xs">ID: {{ $activity->subject_id ?? '—' }}</p>
                                </td>
                                <td>
                                    <p>{{ $activity->description }}</p>
                                    <p class="text-base-content/55 mt-1 text-xs">
                                        {{ collect($activity->properties->get('attributes', $activity->properties->get('old', [])))->keys()->take(4)->implode(', ') ?: __('admin.common.no_attributes') }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('system.activity-logs.show', $activity) }}"
                                        class="btn btn-square btn-text btn-sm" title="{{ __('admin.activity_log.view_detail') }}" aria-label="{{ __('admin.activity_log.view_detail') }}">
                                        <span class="icon-[tabler--eye] size-5"></span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <span class="icon-[tabler--history-off] text-base-content/30 mx-auto size-10"></span>
                                    <p class="mt-3 font-medium">{{ __('admin.activity_log.empty') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-adminpanel::components.responsive-table>

            <div class="border-t border-base-content/10 p-4">
                <x-adminpanel::components.pagination :paginator="$activities" />
            </div>
        </section>
    </div>
</x-adminpanel::layouts.app>

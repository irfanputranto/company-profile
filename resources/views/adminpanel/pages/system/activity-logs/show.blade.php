<x-adminpanel::layouts.app :title="__('admin.activity_log.detail_title')">
    <div class="space-y-5 sm:space-y-6">
        <x-adminpanel::components.page-header :title="__('admin.activity_log.detail_title')"
            :description="__('admin.activity_log.detail_description', ['id' => $activity->id])">
            <x-slot:actions>
                <a href="{{ route('system.activity-logs.index') }}" class="btn btn-soft gap-2">
                    <span class="icon-[tabler--arrow-left] size-4.5"></span>{{ __('admin.common.back') }}
                </a>
            </x-slot:actions>
        </x-adminpanel::components.page-header>

        <section class="card border border-base-content/10 p-4 shadow-sm sm:p-6">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <div>
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">{{ __('admin.activity_log.time') }}</dt>
                    <dd class="mt-1.5 font-medium">{{ $activity->created_at?->format('d/m/Y H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">{{ __('admin.activity_log.user') }}</dt>
                    <dd class="mt-1.5 font-medium">{{ $activity->causer?->name ?? __('admin.activity_log.system') }}</dd>
                    <dd class="text-base-content/55 text-xs">{{ $activity->causer?->username ?? __('admin.activity_log.no_user') }}</dd>
                </div>
                <div>
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">{{ __('admin.activity_log.action') }}</dt>
                    <dd class="mt-1.5">{{ __("admin.activity_log.{$activity->event}") }}</dd>
                </div>
                <div>
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">{{ __('admin.activity_log.data') }}</dt>
                    <dd class="mt-1.5 font-medium">{{ class_basename($activity->subject_type ?? __('admin.activity_log.system')) }} #{{ $activity->subject_id ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2 xl:col-span-4">
                    <dt class="text-base-content/55 text-xs font-medium uppercase tracking-wide">{{ __('admin.activity_log.description_label') }}</dt>
                    <dd class="mt-1.5">{{ $activity->description }}</dd>
                </div>
            </dl>
        </section>

        <section class="card overflow-hidden border border-base-content/10 shadow-sm">
            <div class="border-b border-base-content/10 p-4 sm:p-5">
                <h2 class="font-semibold">{{ __('admin.activity_log.values_title') }}</h2>
                <p class="text-base-content/55 mt-1 text-sm">{{ __('admin.activity_log.values_description') }}</p>
            </div>
            <x-adminpanel::components.responsive-table>
                <table class="table min-w-[760px]">
                    <thead>
                        <tr>
                            <th>{{ __('admin.activity_log.attribute') }}</th>
                            <th>{{ __('admin.activity_log.old_value') }}</th>
                            <th>{{ __('admin.activity_log.new_value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($changedAttributes as $change)
                            <tr>
                                <td class="font-mono text-xs font-semibold">{{ $change['attribute'] }}</td>
                                <td class="max-w-sm whitespace-normal break-words font-mono text-xs">
                                    @if (is_bool($change['old']))
                                        {{ $change['old'] ? 'true' : 'false' }}
                                    @elseif (is_array($change['old']) || is_object($change['old']))
                                        {{ json_encode($change['old'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
                                    @else
                                        {{ $change['old'] ?? '—' }}
                                    @endif
                                </td>
                                <td class="max-w-sm whitespace-normal break-words font-mono text-xs">
                                    @if (is_bool($change['new']))
                                        {{ $change['new'] ? 'true' : 'false' }}
                                    @elseif (is_array($change['new']) || is_object($change['new']))
                                        {{ json_encode($change['new'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
                                    @else
                                        {{ $change['new'] ?? '—' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-10 text-center">{{ __('admin.activity_log.no_changes') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-adminpanel::components.responsive-table>
        </section>
    </div>
</x-adminpanel::layouts.app>

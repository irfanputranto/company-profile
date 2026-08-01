@php
    $columns = [
        'backlog' => ['icon' => 'icon-[tabler--list-check]', 'accent' => 'bg-base-content/40'],
        'in_progress' => ['icon' => 'icon-[tabler--loader-2]', 'accent' => 'bg-info'],
        'review' => ['icon' => 'icon-[tabler--eye-check]', 'accent' => 'bg-warning'],
        'done' => ['icon' => 'icon-[tabler--circle-check]', 'accent' => 'bg-success'],
        'blocked' => ['icon' => 'icon-[tabler--alert-triangle]', 'accent' => 'bg-error'],
    ];
    $features = $project->phases->flatMap->features;
@endphp

<x-adminpanel::layouts.app :title="__('project-management.board.title').' · '.$project->name" :fullscreen="true" :wide="true">
    <div class="flex h-full min-h-0 flex-col gap-4" x-data="projectKanban"
        data-move-error="{{ __('project-management.board.move_failed') }}">
        <x-adminpanel::components.page-header :title="__('project-management.board.title')" :description="$project->code.' · '.$project->name.' · '.$project->clientCompany->name">
            <x-slot:actions>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('project-management.projects.show', $project) }}" class="btn btn-text">
                        <span class="icon-[tabler--arrow-left] size-5"></span>
                        {{ __('project-management.board.back_to_detail') }}
                    </a>
                    @can('update_managed_projects')
                        <a href="{{ route('project-management.projects.show', $project) }}#phases" class="btn btn-primary">
                            <span class="icon-[tabler--plus] size-5"></span>
                            {{ __('project-management.features.add') }}
                        </a>
                    @endcan
                </div>
            </x-slot:actions>
        </x-adminpanel::components.page-header>

        <x-adminpanel::components.flash-message />

        <div class="flex flex-col gap-3 rounded-xl border border-base-content/10 bg-base-100 p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium">{{ __('project-management.board.description') }}</p>
                <p class="mt-1 text-xs text-base-content/60">{{ __('project-management.board.drag_hint') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($project->phases as $phase)
                    <a href="{{ route('project-management.projects.show', $project) }}#phases"
                        class="badge badge-soft gap-2 py-3">
                        <span class="size-2 rounded-full bg-primary"></span>
                        <span>{{ $phase->name }}</span>
                        <strong data-phase-progress="{{ $phase->id }}">{{ $phase->progress }}%</strong>
                    </a>
                @endforeach
            </div>
        </div>

        <div x-cloak x-show="error" class="alert alert-error py-3" role="alert">
            <span class="icon-[tabler--alert-circle] size-5"></span>
            <span x-text="error"></span>
        </div>

        <div class="relative min-h-0 flex-1">
            <div class="h-full overflow-x-auto pb-3">
                <div class="flex h-full min-w-max items-start gap-4">
                    @foreach($columns as $status => $column)
                        @php($columnFeatures = $features->where('status', $status))
                        <section class="flex max-h-full w-[19rem] shrink-0 flex-col overflow-hidden rounded-2xl border border-base-content/10 bg-base-200/70 shadow-sm"
                            data-kanban-column data-status="{{ $status }}"
                            @dragenter.prevent="activeStatus = '{{ $status }}'"
                            @dragleave.self="activeStatus = null"
                            @dragover.prevent
                            @drop.prevent="dropCard($event, '{{ $status }}')"
                            :class="activeStatus === '{{ $status }}' ? 'ring-2 ring-primary/50' : ''">
                            <div class="border-b border-base-content/10 bg-base-100 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $column['icon'] }} size-5"></span>
                                        <h2 class="font-semibold">{{ __('project-management.status.features.'.$status) }}</h2>
                                    </div>
                                    <span class="badge badge-sm bg-base-200">
                                        <span data-card-count>{{ $columnFeatures->count() }}</span>
                                    </span>
                                </div>
                                <div class="mt-3 h-1 overflow-hidden rounded-full bg-base-200">
                                    <div class="h-full w-full {{ $column['accent'] }}"></div>
                                </div>
                            </div>

                            <div class="min-h-28 flex-1 space-y-3 overflow-y-auto p-3" data-card-list>
                                @foreach($columnFeatures as $feature)
                                    @php($phase = $project->phases->firstWhere('id', $feature->project_phase_id))
                                    <article @can('update_managed_projects') draggable="true" @endcan
                                        data-kanban-card data-feature-id="{{ $feature->id }}"
                                        data-current-status="{{ $feature->status }}"
                                        data-move-url="{{ route('project-management.projects.phases.features.move', [$project, $phase, $feature]) }}"
                                        @can('update_managed_projects')
                                            @dragstart="startDrag($event)" @dragend="endDrag"
                                        @endcan
                                        class="group cursor-default rounded-xl border border-base-content/10 bg-base-100 p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md @can('update_managed_projects') sm:cursor-grab sm:active:cursor-grabbing @endcan">
                                        <div class="flex items-start justify-between gap-2">
                                            <span class="badge badge-primary badge-soft badge-sm max-w-[13rem] truncate">
                                                {{ $phase->name }}
                                            </span>
                                            @can('update_managed_projects')
                                                <span class="icon-[tabler--grip-vertical] size-4 shrink-0 text-base-content/30"></span>
                                            @endcan
                                        </div>

                                        <h3 class="mt-3 text-sm font-semibold leading-5">{{ $feature->name }}</h3>
                                        @if($feature->description)
                                            <p class="mt-2 line-clamp-3 text-xs leading-5 text-base-content/65">{{ $feature->description }}</p>
                                        @endif

                                        @if($feature->acceptance_criteria)
                                            <div class="mt-3 rounded-lg bg-base-200/60 p-2.5">
                                                <p class="flex items-center gap-1.5 text-[0.7rem] font-semibold uppercase tracking-wide text-base-content/55">
                                                    <span class="icon-[tabler--checks] size-3.5"></span>
                                                    {{ __('project-management.fields.acceptance_criteria') }}
                                                </p>
                                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-base-content/70">{{ $feature->acceptance_criteria }}</p>
                                            </div>
                                        @endif

                                        <div class="mt-3 flex items-center justify-between gap-2 border-t border-base-content/10 pt-3">
                                            <div class="flex items-center gap-1 text-xs text-base-content/55">
                                                <span class="icon-[tabler--calendar-due] size-4"></span>
                                                <span>{{ $phase->due_at?->isoFormat('D MMM') ?: '—' }}</span>
                                            </div>
                                            <a href="{{ route('project-management.projects.show', $project) }}#phases"
                                                class="btn btn-text btn-square btn-xs" title="{{ __('project-management.features.edit') }}">
                                                <span class="icon-[tabler--pencil] size-4"></span>
                                            </a>
                                        </div>

                                        @can('update_managed_projects')
                                            <label class="mt-3 block sm:hidden">
                                                <span class="sr-only">{{ __('project-management.status.features.'.$feature->status) }}</span>
                                                <select class="select select-sm w-full" data-status-select @change="moveFromSelect($event)" @click.stop>
                                                    @foreach($columns as $optionStatus => $option)
                                                        <option value="{{ $optionStatus }}" @selected($feature->status === $optionStatus)>
                                                            {{ __('project-management.status.features.'.$optionStatus) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </label>
                                        @endcan
                                    </article>
                                @endforeach

                                <div data-empty-state @class(['hidden' => $columnFeatures->isNotEmpty(), 'rounded-xl border border-dashed border-base-content/20 p-6 text-center text-xs text-base-content/50'])>
                                    <span class="icon-[tabler--inbox] mx-auto mb-2 block size-7"></span>
                                    {{ __('project-management.board.empty') }}
                                </div>
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>

            <div x-cloak x-show="saving" class="absolute inset-0 z-10 grid place-items-center rounded-xl bg-base-100/35 backdrop-blur-[1px]">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>
        </div>
    </div>
</x-adminpanel::layouts.app>

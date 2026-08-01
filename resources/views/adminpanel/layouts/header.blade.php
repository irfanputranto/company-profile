@php($pageGuide ??= [])

<div class="bg-base-100 border-base-content/20 sticky top-0 z-50 flex border-b transition-[padding] duration-300"
    :class="sidebarCollapsed ? 'lg:ps-0' : 'lg:ps-75'">
    <div class="w-full min-w-0">
        <nav class="navbar w-full px-4 py-2 sm:px-6 lg:px-8">
            <div class="navbar-start items-center gap-2">
                <button type="button" class="btn btn-soft btn-square btn-sm lg:hidden" aria-controls="layout-sidebar" data-overlay="#layout-sidebar" title="{{ __('admin.navigation.open') }}" aria-label="{{ __('admin.navigation.open') }}">
                    <span class="icon-[tabler--menu-2] size-4.5"></span>
                </button>
            </div>

            <div class="navbar-end items-center gap-3">
                <x-language-switcher />

                @can('view_managed_projects')
                    <div class="dropdown relative inline-flex [--offset:8] [--placement:bottom-end]">
                        <button type="button" class="dropdown-toggle btn btn-soft btn-square btn-sm relative" aria-label="{{ __('project-management.notifications.title') }}">
                            <span class="icon-[tabler--bell] size-5"></span>
                            @if ($projectNotificationsCount > 0)
                                <span class="badge badge-error badge-sm absolute -end-2 -top-2 min-w-5 px-1 text-[10px]">{{ min($projectNotificationsCount, 99) }}</span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-open:opacity-100 hidden w-88 max-w-[calc(100vw-2rem)] p-2" role="menu">
                            <div class="flex items-center justify-between gap-3 px-3 py-2">
                                <p class="font-semibold">{{ __('project-management.notifications.title') }}</p>
                                @if ($projectNotificationsCount > 0)
                                    <form method="POST" action="{{ route('project-management.notifications.read-all') }}">@csrf<button class="link link-primary text-xs">{{ __('project-management.notifications.read_all') }}</button></form>
                                @endif
                            </div>
                            @forelse ($projectNotifications as $notification)
                                <form method="POST" action="{{ route('project-management.notifications.read', $notification->id) }}">
                                    @csrf
                                    <button class="hover:bg-base-200 flex w-full gap-3 rounded-lg px-3 py-2 text-start">
                                        <span class="icon-[tabler--server-2] text-warning mt-0.5 size-5 shrink-0"></span>
                                        <span class="min-w-0"><span class="block truncate text-sm font-medium">{{ $notification->data['server_name'] }}</span><span class="text-base-content/60 block text-xs">{{ __('project-management.notifications.expires', ['date' => $notification->data['expires_at']]) }}</span></span>
                                    </button>
                                </form>
                            @empty
                                <p class="text-base-content/60 px-3 py-5 text-center text-sm">{{ __('project-management.notifications.empty') }}</p>
                            @endforelse
                        </div>
                    </div>
                @endcan

                <div class="dropdown relative inline-flex [--offset:8] [--placement:bottom-end]">
                    <button id="theme-dropdown" type="button" class="dropdown-toggle btn btn-soft btn-sm gap-2"
                        aria-haspopup="menu" aria-expanded="false" aria-label="{{ __('admin.header.change_theme') }}">
                        <span class="icon-[tabler--palette] size-5"></span>
                        <span class="hidden sm:inline" data-theme-current-label>{{ config('theme.themes.'.config('theme.default')) }}</span>
                        <span class="icon-[tabler--chevron-down] size-4"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-open:opacity-100 hidden max-h-96 w-64 overflow-y-auto p-2"
                        role="menu" aria-labelledby="theme-dropdown">
                        <li class="dropdown-header px-3 py-2">
                            <span class="text-base-content font-semibold">{{ __('admin.header.theme') }}</span>
                        </li>
                        @foreach (config('theme.themes') as $theme => $label)
                            <li>
                                <button type="button" class="dropdown-item flex w-full items-center justify-between gap-3"
                                    data-theme-value="{{ $theme }}" data-theme-label="{{ $label }}"
                                    aria-pressed="{{ config('theme.default') === $theme ? 'true' : 'false' }}">
                                    <span>{{ $label }}</span>
                                    <span data-theme="{{ $theme }}"
                                        class="border-base-content/15 bg-base-100 flex shrink-0 gap-0.5 rounded border p-1">
                                        <span class="bg-primary size-2.5"></span>
                                        <span class="bg-secondary size-2.5"></span>
                                        <span class="bg-accent size-2.5"></span>
                                        <span class="bg-neutral size-2.5"></span>
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if (! empty($pageGuide))
                    <button type="button" class="btn btn-soft btn-sm gap-2" @click="pageGuideOpen = true" aria-controls="global-page-guide-dialog">
                        <span class="icon-[tabler--help-circle] size-5"></span><span class="hidden sm:inline">{{ __('admin.header.guide') }}</span>
                    </button>
                @endif

                <div class="dropdown relative inline-flex [--offset:21]">
                    <button id="profile-dropdown" type="button" class="dropdown-toggle hover:bg-base-200 flex max-w-64 items-center gap-3 rounded-lg px-2 py-1.5 text-start transition" aria-haspopup="menu" aria-expanded="false">
                        <div class="hidden min-w-0 sm:block">
                            <p class="truncate text-sm font-semibold">{{ auth()->user()?->name ?? __('admin.header.user') }}</p>
                            <p class="text-base-content/60 truncate text-xs">{{ auth()->user()?->roles->first()?->name ?? __('admin.header.no_role') }}</p>
                        </div>
                        <x-adminpanel::components.user-avatar size="sm" />
                        <span class="icon-[tabler--chevron-down] text-base-content/60 hidden size-4 sm:block"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-open:opacity-100 hidden w-80 max-w-[calc(100vw-2rem)] space-y-0.5" role="menu" aria-labelledby="profile-dropdown">
                        <li class="dropdown-header mb-1 gap-4 px-5 pb-3.5 pt-4">
                            <x-adminpanel::components.user-avatar />
                            <div class="min-w-0">
                                <h6 class="line-clamp-2 break-words font-semibold">{{ auth()->user()?->name ?? __('admin.header.user') }}</h6>
                                <p class="text-base-content/60 truncate text-sm">{{ auth()->user()?->email ?? '—' }}</p>
                            </div>
                        </li>
                        <li><a class="dropdown-item px-3" href="{{ route('profile') }}"><span class="icon-[tabler--user-circle] size-5"></span><span>{{ __('admin.header.my_profile') }}</span></a></li>
                        <li><hr class="border-base-content/20 -mx-2 my-1"></li>
                        <li class="dropdown-footer p-2 pt-1">
                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="text-error hover:bg-error/10 flex h-11 w-full items-center gap-2 rounded-lg px-3 text-start">
                                    <span class="icon-[tabler--logout] size-5"></span><span>{{ __('admin.header.logout') }}</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

@if (! empty($pageGuide))
    <x-adminpanel::components.page-guide :guide="$pageGuide" />
@endif

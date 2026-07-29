<aside id="layout-sidebar"
    class="overlay overlay-open:translate-x-0 drawer drawer-start sm:w-75 inset-y-0 start-0 hidden h-full [--auto-close:lg] lg:z-50 lg:block lg:shadow-none lg:transition-transform lg:duration-300"
    :class="sidebarCollapsed ? 'lg:-translate-x-full' : 'lg:translate-x-0'"
    aria-label="{{ __('admin.navigation.sidebar') }}" tabindex="-1">
    <div class="drawer-body border-base-content/20 h-full border-e p-0">
        <div class="flex h-full max-h-full flex-col">
            <button type="button" class="btn btn-text btn-circle btn-sm absolute end-3 top-3 lg:hidden" title="{{ __('admin.navigation.close') }}" aria-label="{{ __('admin.navigation.close') }}" data-overlay="#layout-sidebar">
                <span class="icon-[tabler--x] size-4.5"></span>
            </button>

            <div class="text-base-content border-base-content/20 flex flex-col items-center gap-3 border-b px-4 py-5">
                <x-adminpanel::components.user-avatar size="lg" />
                <div class="min-w-0 text-center">
                    <h3 class="truncate text-lg font-semibold">{{ auth()->user()?->name ?? __('admin.header.user') }}</h3>
                    <p class="text-base-content/60 truncate text-sm">{{ auth()->user()?->email ?? '—' }}</p>
                </div>
            </div>

            <nav class="h-full overflow-y-auto p-3" aria-label="{{ __('admin.navigation.main') }}">
                <ul class="menu menu-sm gap-1">
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'menu-active' : '' }} inline-flex w-full items-center gap-2 p-2 text-sm font-normal">
                            <span class="icon-[tabler--dashboard] size-4.5"></span><span>{{ __('admin.navigation.dashboard') }}</span>
                        </a>
                    </li>

                    @php
                        $companyProfileGroups = collect(\App\Modules\CompanyProfile\Support\CompanyProfileNavigation::groups())
                            ->map(function (array $group): array {
                                $group['items'] = collect($group['items'])
                                    ->filter(fn (array $item): bool => auth()->user()?->can($item['permission']) ?? false)
                                    ->values()
                                    ->all();

                                return $group;
                            })
                            ->filter(fn (array $group): bool => $group['items'] !== []);
                    @endphp

                    @if ($companyProfileGroups->isNotEmpty())
                        <li class="menu-title mt-3 px-2 text-xs uppercase tracking-wider">{{ __('admin.navigation.company_profile') }}</li>
                        @foreach ($companyProfileGroups as $group)
                            @php
                                $isGroupActive = collect($group['items'])->contains(function (array $item): bool {
                                    if (($item['route'] ?? null) === 'company-profile.content.index') {
                                        return request()->routeIs('company-profile.content.*')
                                            && request()->route('resource') === $item['resource'];
                                    }

                                    return request()->routeIs(str($item['route'])->beforeLast('.')->append('.*')->toString());
                                });
                            @endphp
                            <x-adminpanel::components.navigation-group
                                :id="'company-profile-navigation-group-'.$loop->index"
                                :label="$group['label']"
                                :active="$isGroupActive">
                                @foreach ($group['items'] as $item)
                                    @php
                                        $routeParameters = isset($item['resource']) ? ['resource' => $item['resource']] : [];
                                        $isActive = isset($item['resource'])
                                            ? request()->routeIs('company-profile.content.*') && request()->route('resource') === $item['resource']
                                            : request()->routeIs(str($item['route'])->beforeLast('.')->append('.*')->toString());
                                    @endphp
                                    <li>
                                        <a href="{{ route($item['route'], $routeParameters) }}" class="{{ $isActive ? 'menu-active' : '' }}">
                                            <span class="icon-[tabler--point-filled] size-3"></span>
                                            <span>{{ $item['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </x-adminpanel::components.navigation-group>
                        @endforeach
                    @endif

                    @canany(['view_users', 'view_roles', 'view_permissions'])
                        <li class="menu-title mt-3 px-2 text-xs uppercase tracking-wider">{{ __('admin.navigation.access_management') }}</li>
                        @can('view_users')
                            <li><a href="{{ route('master.users.index') }}" class="{{ request()->routeIs('master.users.*') ? 'menu-active' : '' }}"><span class="icon-[tabler--users] size-4.5"></span><span>{{ __('admin.navigation.users') }}</span></a></li>
                        @endcan
                        @can('view_roles')
                            <li><a href="{{ route('master.roles.index') }}" class="{{ request()->routeIs('master.roles.*') ? 'menu-active' : '' }}"><span class="icon-[tabler--user-shield] size-4.5"></span><span>{{ __('admin.navigation.roles') }}</span></a></li>
                        @endcan
                        @can('view_permissions')
                            <li><a href="{{ route('master.permissions.index') }}" class="{{ request()->routeIs('master.permissions.*') ? 'menu-active' : '' }}"><span class="icon-[tabler--shield-lock] size-4.5"></span><span>{{ __('admin.navigation.permissions') }}</span></a></li>
                        @endcan
                    @endcanany

                    @can('view_activity_logs')
                        <li class="menu-title mt-3 px-2 text-xs uppercase tracking-wider">{{ __('admin.navigation.system') }}</li>
                        <li><a href="{{ route('system.activity-logs.index') }}" class="{{ request()->routeIs('system.activity-logs.*') ? 'menu-active' : '' }}"><span class="icon-[tabler--activity] size-4.5"></span><span>{{ __('admin.navigation.activity_log') }}</span></a></li>
                    @endcan
                </ul>
            </nav>
        </div>
    </div>
</aside>

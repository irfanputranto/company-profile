<aside id="layout-sidebar"
    class="overlay overlay-open:translate-x-0 drawer drawer-start sm:w-75 inset-y-0 start-0 hidden h-full [--auto-close:lg] lg:z-50 lg:block lg:shadow-none lg:transition-transform lg:duration-300"
    :class="sidebarCollapsed ? 'lg:-translate-x-full' : 'lg:translate-x-0'"
    aria-label="Sidebar" tabindex="-1">
    <div class="drawer-body border-base-content/20 h-full border-e p-0">
        <div class="flex h-full max-h-full flex-col">
            <button type="button" class="btn btn-text btn-circle btn-sm absolute end-3 top-3 lg:hidden" title="Tutup menu navigasi" aria-label="Tutup menu navigasi" data-overlay="#layout-sidebar">
                <span class="icon-[tabler--x] size-4.5"></span>
            </button>

            <div class="text-base-content border-base-content/20 flex flex-col items-center gap-3 border-b px-4 py-5">
                <x-adminpanel::components.user-avatar size="lg" />
                <div class="min-w-0 text-center">
                    <h3 class="truncate text-lg font-semibold">{{ auth()->user()?->name ?? 'Pengguna' }}</h3>
                    <p class="text-base-content/60 truncate text-sm">{{ auth()->user()?->email ?? '—' }}</p>
                </div>
            </div>

            <nav class="h-full overflow-y-auto p-3" aria-label="Navigasi utama">
                <ul class="menu menu-sm gap-1">
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'menu-active' : '' }} inline-flex w-full items-center gap-2 p-2 text-sm font-normal">
                            <span class="icon-[tabler--dashboard] size-4.5"></span><span>Dashboard</span>
                        </a>
                    </li>

                    @canany(['view_users', 'view_roles', 'view_permissions'])
                        <li class="menu-title mt-3 px-2 text-xs uppercase tracking-wider">Manajemen Akses</li>
                        @can('view_users')
                            <li><a href="{{ route('master.users.index') }}" class="{{ request()->routeIs('master.users.*') ? 'menu-active' : '' }}"><span class="icon-[tabler--users] size-4.5"></span><span>Pengguna</span></a></li>
                        @endcan
                        @can('view_roles')
                            <li><a href="{{ route('master.roles.index') }}" class="{{ request()->routeIs('master.roles.*') ? 'menu-active' : '' }}"><span class="icon-[tabler--user-shield] size-4.5"></span><span>Role</span></a></li>
                        @endcan
                        @can('view_permissions')
                            <li><a href="{{ route('master.permissions.index') }}" class="{{ request()->routeIs('master.permissions.*') ? 'menu-active' : '' }}"><span class="icon-[tabler--shield-lock] size-4.5"></span><span>Permission</span></a></li>
                        @endcan
                    @endcanany

                    @can('view_activity_logs')
                        <li class="menu-title mt-3 px-2 text-xs uppercase tracking-wider">Sistem</li>
                        <li><a href="{{ route('system.activity-logs.index') }}" class="{{ request()->routeIs('system.activity-logs.*') ? 'menu-active' : '' }}"><span class="icon-[tabler--activity] size-4.5"></span><span>Activity Log</span></a></li>
                    @endcan
                </ul>
            </nav>
        </div>
    </div>
</aside>

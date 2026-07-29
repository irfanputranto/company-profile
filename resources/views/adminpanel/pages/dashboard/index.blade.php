<x-adminpanel::layouts.app :title="__('admin.dashboard.title')">
    <div class="space-y-6">
        <section class="relative overflow-hidden rounded-2xl border border-primary/15 bg-linear-to-br from-primary/10 via-base-100 to-base-100 p-5 shadow-sm sm:p-7">
            <span class="absolute -end-16 -top-20 size-56 rounded-full bg-primary/10 blur-3xl"></span>
            <div class="relative">
                <p class="text-sm font-medium text-primary">{{ $greeting }}, {{ auth()->user()->name }}</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">{{ __('admin.dashboard.hero_title') }}</h1>
                <p class="text-base-content/60 mt-3 max-w-2xl text-sm leading-6 sm:text-base">
                    {{ __('admin.dashboard.hero_description') }}
                </p>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article class="card border border-base-content/10 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div><p class="text-base-content/60 text-sm">{{ __('admin.dashboard.active_users') }}</p><p class="mt-2 text-3xl font-bold">{{ number_format($activeUsersCount) }}</p></div>
                    <span class="bg-primary/10 text-primary flex size-12 items-center justify-center rounded-xl"><span class="icon-[tabler--users] size-6"></span></span>
                </div>
            </article>
            <article class="card border border-base-content/10 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div><p class="text-base-content/60 text-sm">{{ __('admin.dashboard.roles') }}</p><p class="mt-2 text-3xl font-bold">{{ number_format($rolesCount) }}</p></div>
                    <span class="bg-info/10 text-info flex size-12 items-center justify-center rounded-xl"><span class="icon-[tabler--user-shield] size-6"></span></span>
                </div>
            </article>
            <article class="card border border-base-content/10 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div><p class="text-base-content/60 text-sm">{{ __('admin.dashboard.permissions') }}</p><p class="mt-2 text-3xl font-bold">{{ number_format($permissionsCount) }}</p></div>
                    <span class="bg-success/10 text-success flex size-12 items-center justify-center rounded-xl"><span class="icon-[tabler--shield-check] size-6"></span></span>
                </div>
            </article>
        </section>
    </div>
</x-adminpanel::layouts.app>

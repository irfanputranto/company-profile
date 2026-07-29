@props(['title', 'fullscreen' => false, 'kiosk' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ config('theme.default') }}" data-theme-default="{{ config('theme.default') }}"
    data-theme-options="{{ implode(',', array_keys(config('theme.themes'))) }}"
    data-theme-storage-key="{{ config('theme.storage_key') }}" dir="{{ app('App\\Modules\\CompanyProfile\\Services\\LanguageResolver')->activeLanguages()->firstWhere('code', app()->getLocale())?->direction ?? 'ltr' }}" class="scroll-smooth">

<head>
    <x-adminpanel::layouts.head :title="$title" />
</head>

<body>
    <div x-data="sidebarLayout" @class([
        'bg-base-200 flex min-h-screen flex-col',
        'lg:h-dvh lg:min-h-0 lg:overflow-hidden' => $fullscreen,
    ])>
        @unless ($kiosk)
            <!-- Layout Navbar -->
            @include('adminpanel.layouts.header')

            <!-- Layout Menu -->
            <x-adminpanel::layouts.sidebar />

            <button type="button"
                class="btn btn-circle btn-sm border-base-content/15 bg-base-100 text-base-content/70 hover:bg-primary hover:text-primary-content fixed top-24 z-[60] hidden shadow-md transition-[inset-inline-start,background-color,color] duration-300 lg:inline-flex"
                :class="sidebarCollapsed ? 'start-3' : 'start-[17.75rem]'"
                @click="toggleSidebar"
                :title="sidebarCollapsed ? @js(__('admin.navigation.show')) : @js(__('admin.navigation.hide'))"
                :aria-label="sidebarCollapsed ? @js(__('admin.navigation.show')) : @js(__('admin.navigation.hide'))"
                :aria-expanded="(!sidebarCollapsed).toString()"
                aria-controls="layout-sidebar">
                <span x-show="!sidebarCollapsed" class="icon-[tabler--chevrons-left] size-4.5"></span>
                <span x-cloak x-show="sidebarCollapsed" class="icon-[tabler--chevrons-right] size-4.5"></span>
            </button>
        @endunless

        <!-- Layout Container -->
        <div @class([
            'flex grow flex-col transition-[padding] duration-300',
            'lg:min-h-0' => $fullscreen,
        ])
            @unless ($kiosk)
                :class="sidebarCollapsed ? 'lg:ps-0' : 'lg:ps-75'"
            @endunless>
            <!-- Content -->
            <main @class([
                'mx-auto w-full max-w-[1280px] flex-1 grow space-y-5 p-4 sm:space-y-6 sm:p-5 lg:p-6',
                'max-w-none p-2 sm:p-3 lg:min-h-0 lg:overflow-hidden' => $kiosk,
                'lg:min-h-0 lg:overflow-hidden' => $fullscreen && ! $kiosk,
            ])>
                <!-- Stats -->
                {{ $slot }}
            </main>
            <!-- / Content -->

            @unless ($kiosk)
                @if ($fullscreen)
                    <div class="lg:hidden">
                        <x-adminpanel::layouts.footer />
                    </div>
                @else
                    <x-adminpanel::layouts.footer />
                @endif
            @endunless
            <!-- Footer: End -->
        </div>
        <!-- / Layout Container -->

    </div>
    <!-- / Layout Wrapper -->

    @unless ($kiosk)
        <button id="scrollToTopBtn"
            class="btn btn-circle btn-soft btn-secondary/20 bottom-15 end-15 motion-preset-slide-right motion-duration-800 motion-delay-100 fixed absolute z-[3] hidden"
            title="{{ __('admin.common.back_to_top') }}" aria-label="{{ __('admin.common.back_to_top') }}"><span class="icon-[tabler--chevron-up] size-5 shrink-0"></span></button>
    @endunless
</body>

</html>

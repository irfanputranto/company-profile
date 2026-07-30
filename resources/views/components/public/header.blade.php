@props(['profile', 'activePage' => 'home'])

@php
    $navigationItems = [
        ['key' => 'home', 'label' => __('company-profile.public.navigation.home'), 'url' => route('home')],
        ['key' => 'services', 'label' => __('company-profile.public.navigation.services'), 'url' => route('home').'#services'],
        ['key' => 'projects', 'label' => __('company-profile.public.navigation.projects'), 'url' => route('projects.index')],
        ['key' => 'blog', 'label' => __('company-profile.public.navigation.blog'), 'url' => route('blog.index')],
        ['key' => 'pricing', 'label' => __('company-profile.public.navigation.pricing'), 'url' => route('pricing.index')],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-[#dcebea]/80 bg-white/95 backdrop-blur-xl"
    x-data="publicNavigation('{{ $activePage }}')">
    <div class="bs-container">
        <nav class="navbar min-h-16 px-0" aria-label="{{ __('company-profile.public.navigation.main') }}">
            <div class="navbar-start">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5">
                    <x-public.brand-mark :profile="$profile"
                        class="size-9 shrink-0 rounded-lg shadow-md shadow-teal-600/15" />
                    <span class="truncate text-base font-extrabold text-[#17212b]">
                        {{ $profile?->public_name ?? config('app.name') }}
                    </span>
                </a>
            </div>

            <div class="navbar-center hidden lg:flex">
                <ul class="flex items-center gap-6 text-sm font-bold text-[#17212b]">
                    @foreach ($navigationItems as $item)
                        <li>
                            <a href="{{ $item['url'] }}"
                                data-analytics-scope="menu" data-analytics-event="{{ $item['key'] }}"
                                class="relative block py-5 transition-colors hover:text-[#0aa8a7]"
                                :class="{ 'text-[#078786]': active === '{{ $item['key'] }}' }"
                                :aria-current="active === '{{ $item['key'] }}' ? 'page' : null"
                                @click="activate('{{ $item['key'] }}')">
                                {{ $item['label'] }}
                                <span class="absolute inset-x-0 bottom-3 mx-auto h-0.5 rounded-full bg-[#0aa8a7] transition-all"
                                    :class="active === '{{ $item['key'] }}' ? 'w-full opacity-100' : 'w-0 opacity-0'"></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="navbar-end gap-2">
                <x-language-switcher />
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm hidden rounded-full px-5 sm:inline-flex">
                        {{ __('company-profile.public.navigation.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('home') }}#contact" data-analytics-scope="menu" data-analytics-event="contact"
                        class="btn btn-primary btn-sm hidden rounded-full px-5 sm:inline-flex">
                        {{ __('company-profile.public.navigation.contact') }}
                    </a>
                @endauth
                <button type="button" class="btn btn-square btn-text btn-sm lg:hidden" @click="open = ! open"
                    :aria-expanded="open.toString()" aria-controls="public-mobile-navigation"
                    aria-label="{{ __('company-profile.public.navigation.open') }}">
                    <span :class="open ? 'icon-[tabler--x]' : 'icon-[tabler--menu-2]'" class="size-5"></span>
                </button>
            </div>
        </nav>
    </div>

    <div id="public-mobile-navigation" x-cloak x-show="open" x-transition.origin.top
        class="border-t border-[#dcebea] bg-white lg:hidden">
        <div class="bs-container grid gap-1 py-4 text-sm font-bold text-[#17212b]">
            @foreach ($navigationItems as $item)
                <a href="{{ $item['url'] }}" data-analytics-scope="menu" data-analytics-event="{{ $item['key'] }}"
                    class="flex items-center justify-between rounded-lg px-3 py-2.5 hover:bg-[#edf6f5]"
                    :class="{ 'bg-[#edf6f5] text-[#078786]': active === '{{ $item['key'] }}' }"
                    :aria-current="active === '{{ $item['key'] }}' ? 'page' : null"
                    @click="activate('{{ $item['key'] }}'); open = false">
                    {{ $item['label'] }}
                    <span x-show="active === '{{ $item['key'] }}'" class="icon-[tabler--point-filled] size-4"></span>
                </a>
            @endforeach
            <a href="{{ route('home') }}#contact"
                data-analytics-scope="menu" data-analytics-event="contact"
                class="flex items-center justify-between rounded-lg px-3 py-2.5 hover:bg-[#edf6f5]"
                :class="{ 'bg-[#edf6f5] text-[#078786]': active === 'contact' }"
                @click="activate('contact'); open = false">
                {{ __('company-profile.public.navigation.contact') }}
            </a>
        </div>
    </div>
</header>

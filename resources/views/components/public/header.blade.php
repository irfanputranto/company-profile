@props(['profile', 'activePage' => 'home'])

@php
    $navigationItems = [
        ['key' => 'home', 'label' => __('company-profile.public.navigation.home'), 'url' => route('home')],
        ['key' => 'about', 'label' => __('company-profile.public.navigation.about'), 'url' => route('about')],
        ['key' => 'services', 'label' => __('company-profile.public.navigation.services'), 'url' => route('home').'#services'],
        ['key' => 'projects', 'label' => __('company-profile.public.navigation.projects'), 'url' => route('projects.index')],
        ['key' => 'blog', 'label' => __('company-profile.public.navigation.blog'), 'url' => route('blog.index')],
        ['key' => 'pricing', 'label' => __('company-profile.public.navigation.pricing'), 'url' => route('pricing.index')],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-[#dcebea]/80 bg-white/95 backdrop-blur-xl"
    x-data="publicNavigation(@js($activePage))">
    <div class="bs-container">
        <nav class="flex min-h-16 items-center justify-between gap-3 px-0 lg:grid lg:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)]"
            aria-label="{{ __('company-profile.public.navigation.main') }}">
            <div class="min-w-0 lg:justify-self-start">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5">
                    <x-public.brand-mark :profile="$profile"
                        class="size-9 shrink-0 rounded-lg shadow-md shadow-teal-600/15" />
                    <span class="truncate text-base font-extrabold text-[#17212b]">
                        {{ $profile?->public_name ?? config('app.name') }}
                    </span>
                </a>
            </div>

            <div class="hidden lg:block lg:justify-self-center">
                <ul class="flex items-center gap-5 whitespace-nowrap text-sm font-bold text-[#17212b] xl:gap-6">
                    @foreach ($navigationItems as $item)
                        <li>
                            <a href="{{ $item['url'] }}"
                                data-analytics-scope="menu" data-analytics-event="{{ $item['key'] }}"
                                class="relative block py-5 transition-colors hover:text-[#0aa8a7]"
                                :class="{ 'text-[#078786]': active === @js($item['key']) }"
                                :aria-current="active === @js($item['key']) ? 'page' : null"
                                @click="activate(@js($item['key']))">
                                {{ $item['label'] }}
                                <span class="absolute inset-x-0 bottom-3 mx-auto h-0.5 rounded-full bg-[#0aa8a7] transition-all"
                                    :class="active === @js($item['key']) ? 'w-full opacity-100' : 'w-0 opacity-0'"></span>
                            </a>
                        </li>
                    @endforeach
                    @if($navigationPages->isNotEmpty())
                        <li class="relative" @keydown.escape.window="closeMore">
                            <button type="button"
                                class="relative flex items-center gap-1 py-5 transition-colors hover:text-[#0aa8a7]"
                                :class="active.startsWith('page:') ? 'text-[#078786]' : ''"
                                :aria-expanded="moreOpen.toString()" aria-haspopup="true" aria-controls="public-more-navigation" @click="toggleMore">
                                {{ __('company-profile.public.navigation.more') }}
                                <span class="icon-[tabler--chevron-down] size-4 transition-transform" :class="moreOpen ? 'rotate-180' : ''"></span>
                                <span class="absolute inset-x-0 bottom-3 mx-auto h-0.5 rounded-full bg-[#0aa8a7] transition-all"
                                    :class="active.startsWith('page:') ? 'w-full opacity-100' : 'w-0 opacity-0'"></span>
                            </button>
                            <div id="public-more-navigation" x-cloak x-show="moreOpen" x-transition.origin.top.right @click.outside="closeMore"
                                class="absolute end-0 top-14 z-50 w-64 overflow-hidden rounded-2xl border border-[#dcebea] bg-white p-2 shadow-xl shadow-slate-900/10">
                                @foreach($navigationPages as $navigationPage)
                                    @php($pageKey = 'page:'.$navigationPage->slug)
                                    <a href="{{ route('pages.show', $navigationPage) }}" data-analytics-scope="menu" data-analytics-event="{{ $pageKey }}"
                                        class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition hover:bg-[#edf6f5] hover:text-[#078786]"
                                        :class="active === @js($pageKey) ? 'bg-[#edf6f5] text-[#078786]' : 'text-[#17212b]'"
                                        @click="activate(@js($pageKey))">
                                        <span class="truncate">{{ $navigationPage->translated('title') }}</span>
                                        <span class="icon-[tabler--arrow-up-right] size-4 shrink-0 text-[#078786]"></span>
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="flex items-center justify-end gap-2 lg:justify-self-end">
                <x-language-switcher with-flags />
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
                    :class="{ 'bg-[#edf6f5] text-[#078786]': active === @js($item['key']) }"
                    :aria-current="active === @js($item['key']) ? 'page' : null"
                    @click="activate(@js($item['key'])); open = false">
                    {{ $item['label'] }}
                    <span x-show="active === @js($item['key'])" class="icon-[tabler--point-filled] size-4"></span>
                </a>
            @endforeach
            @if($navigationPages->isNotEmpty())
                <div class="my-2 border-t border-[#dcebea]"></div>
                <p class="px-3 pb-1 pt-1 text-[0.68rem] font-extrabold uppercase tracking-[0.14em] text-slate-400">
                    {{ __('company-profile.public.navigation.more') }}
                </p>
                @foreach($navigationPages as $navigationPage)
                    @php($pageKey = 'page:'.$navigationPage->slug)
                    <a href="{{ route('pages.show', $navigationPage) }}" data-analytics-scope="menu" data-analytics-event="{{ $pageKey }}"
                        class="flex items-center justify-between rounded-lg px-3 py-2.5 hover:bg-[#edf6f5]"
                        :class="{ 'bg-[#edf6f5] text-[#078786]': active === @js($pageKey) }"
                        :aria-current="active === @js($pageKey) ? 'page' : null"
                        @click="activate(@js($pageKey)); open = false">
                        {{ $navigationPage->translated('title') }}
                        <span x-show="active === @js($pageKey)" class="icon-[tabler--point-filled] size-4"></span>
                    </a>
                @endforeach
            @endif
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

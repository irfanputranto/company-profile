@props(['profile', 'services', 'socialLinks'])

<footer class="border-t border-[#dcebea] bg-[#edf6f5] text-slate-600">
    <div class="bs-container grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <h2 class="font-extrabold text-[#17212b]">{{ __('company-profile.public.footer.navigation') }}</h2>
            <nav class="mt-5 grid gap-3 text-sm" aria-label="{{ __('company-profile.public.footer.navigation') }}">
                <a class="hover:text-[#078786]" href="{{ route('home') }}">{{ __('company-profile.public.navigation.home') }}</a>
                <a class="hover:text-[#078786]" href="{{ route('projects.index') }}">{{ __('company-profile.public.navigation.projects') }}</a>
                <a class="hover:text-[#078786]" href="{{ route('blog.index') }}">{{ __('company-profile.public.navigation.blog') }}</a>
                <a class="hover:text-[#078786]" href="{{ route('pricing.index') }}">{{ __('company-profile.public.navigation.pricing') }}</a>
            </nav>
        </div>

        <div>
            <h2 class="font-extrabold text-[#17212b]">{{ __('company-profile.public.footer.services') }}</h2>
            <div class="mt-5 grid gap-3 text-sm">
                @forelse ($services->take(4) as $service)
                    <a class="hover:text-[#078786]" href="{{ route('home') }}#services">{{ $service->translated('title') }}</a>
                @empty
                    <a class="hover:text-[#078786]" href="{{ route('home') }}#services">{{ __('company-profile.public.navigation.services') }}</a>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="font-extrabold text-[#17212b]">{{ __('company-profile.public.footer.contact') }}</h2>
            <div class="mt-5 grid gap-3 text-sm">
                @if ($profile?->email)
                    <a class="break-all hover:text-[#078786]" href="mailto:{{ $profile->email }}">{{ $profile->email }}</a>
                @endif
                @if ($profile?->phone)
                    <a class="hover:text-[#078786]" href="tel:{{ preg_replace('/[^0-9+]/', '', $profile->phone) }}">{{ $profile->phone }}</a>
                @endif
                @if ($profile?->location)
                    <span>{{ $profile->location }}</span>
                @endif
            </div>
        </div>

        <div>
            <div class="flex items-center gap-2.5">
                <x-public.brand-mark :profile="$profile" class="size-9 shrink-0 rounded-lg" />
                <p class="text-lg font-extrabold text-[#17212b]">{{ $profile?->public_name ?? config('app.name') }}</p>
            </div>
            <p class="mt-4 text-sm leading-6">{{ __('company-profile.public.footer.description') }}</p>
            @if ($socialLinks->isNotEmpty())
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($socialLinks as $social)
                        @php
                            $socialIcon = match (str($social->platform)->lower()->toString()) {
                                'github' => 'icon-[tabler--brand-github]',
                                'linkedin' => 'icon-[tabler--brand-linkedin]',
                                'instagram' => 'icon-[tabler--brand-instagram]',
                                'x', 'twitter' => 'icon-[tabler--brand-x]',
                                'youtube' => 'icon-[tabler--brand-youtube]',
                                default => 'icon-[tabler--world-www]',
                            };
                        @endphp
                        <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer"
                            class="btn btn-circle btn-sm border border-[#b9d8d5] bg-white text-[#078786] hover:border-[#0aa8a7]"
                            aria-label="{{ $social->label }}">
                            <span class="{{ $socialIcon }} size-4.5"></span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="bs-container">
        <div class="border-t border-[#cfe3e1] py-5 text-center text-xs text-slate-500">
            © {{ date('Y') }} {{ $profile?->public_name ?? config('app.name') }}. {{ __('company-profile.public.footer.rights') }}
        </div>
    </div>
</footer>

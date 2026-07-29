@props(['profile', 'services', 'projects'])

@php
    $availability = $profile?->availability_status ?? 'available';
    $availabilityLabel = __("company-profile.public.availability.{$availability}");
    $contactUrl = $profile?->email ? 'mailto:'.$profile->email : '#contact';
@endphp

<section id="about" class="bs-section relative" data-nav-section="home">
    <div class="pointer-events-none absolute start-0 top-8 size-72 rounded-full bg-teal-200/25 blur-3xl"></div>
    <div class="bs-container relative text-center">
        <div class="mx-auto max-w-4xl" data-reveal>
            <span class="bs-kicker">
                <span class="relative flex size-2.5">
                    <span class="absolute inline-flex size-full animate-ping rounded-full bg-[#0aa8a7] opacity-60"></span>
                    <span class="relative inline-flex size-2.5 rounded-full bg-[#0aa8a7]"></span>
                </span>
                {{ $availabilityLabel }}
            </span>

            <p class="mt-6 text-sm font-bold uppercase tracking-[0.18em] text-[#078786]">
                {{ __('company-profile.public.hero.marketing_eyebrow') }}
            </p>
            <h1 class="bs-heading mt-4 text-4xl sm:text-5xl lg:text-[3.5rem]">
                {{ __('company-profile.public.hero.marketing_heading') }}
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                {{ __('company-profile.public.hero.marketing_description') }}
            </p>

            <ul class="mx-auto mt-6 flex max-w-3xl flex-wrap justify-center gap-x-6 gap-y-3 text-sm font-semibold text-slate-600">
                @foreach (__('company-profile.public.hero.selling_points') as $sellingPoint)
                    <li class="flex items-center gap-2">
                        <span class="flex size-5 items-center justify-center rounded-full bg-[#0aa8a7]/10 text-[#078786]">
                            <span class="icon-[tabler--check] size-3.5"></span>
                        </span>
                        {{ $sellingPoint }}
                    </li>
                @endforeach
            </ul>

            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ $contactUrl }}" class="btn btn-primary rounded-full px-7">
                    {{ __('company-profile.public.hero.marketing_primary_action') }}
                    <span class="icon-[tabler--arrow-up-right] size-4.5"></span>
                </a>
                <a href="#services" class="btn btn-outline btn-primary rounded-full px-7">
                    {{ __('company-profile.public.hero.marketing_secondary_action') }}
                </a>
            </div>

            <p class="mt-5 text-sm text-slate-500">
                <span class="font-bold text-[#17212b]">{{ $profile?->public_name ?? config('app.name') }}</span>
                <span aria-hidden="true"> · </span>
                {{ $profile?->translated('headline') ?? __('company-profile.public.heading') }}
            </p>
        </div>

        <div class="relative mx-auto mt-8 max-w-3xl" data-reveal>
            <span class="absolute -end-4 -top-4 size-24 rounded-full border border-[#0aa8a7]/20"></span>
            <span class="absolute -bottom-5 -start-5 size-36 rounded-full bg-[#edf6f5]"></span>
            <img class="bs-hero-art relative mx-auto w-full max-w-[43rem]" src="{{ asset('vendor/bigspring/images/banner-art.svg') }}"
                width="750" height="390" alt="" fetchpriority="high">
        </div>

        <dl class="mx-auto mt-8 grid max-w-2xl grid-cols-3 divide-x divide-[#dcebea] border-t border-[#dcebea] pt-6" data-reveal>
            <div class="px-2">
                <dd class="text-2xl font-black text-[#17212b]">{{ $profile?->years_experience ?? 0 }}+</dd>
                <dt class="mt-1 text-xs leading-5 text-slate-500">{{ __('company-profile.public.hero.years') }}</dt>
            </div>
            <div class="px-2">
                <dd class="text-2xl font-black text-[#17212b]">{{ $projects->count() }}</dd>
                <dt class="mt-1 text-xs leading-5 text-slate-500">{{ __('company-profile.public.hero.projects') }}</dt>
            </div>
            <div class="px-2">
                <dd class="text-2xl font-black text-[#17212b]">{{ $services->count() }}</dd>
                <dt class="mt-1 text-xs leading-5 text-slate-500">{{ __('company-profile.public.hero.services') }}</dt>
            </div>
        </dl>
    </div>
</section>

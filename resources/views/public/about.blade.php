<x-public.layout :profile="$profile" :services="$services" :social-links="$socialLinks" active-page="about"
    :title="__('company-profile.public.about_section.meta_title')"
    :description="__('company-profile.public.about_section.meta_description')">
    <section class="bs-section bs-section-light">
        <div class="bs-container grid items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">
            <div class="relative mx-auto w-full max-w-md" data-reveal>
                <div class="absolute -start-5 -top-5 size-24 rounded-full border border-[#0aa8a7]/25"></div>
                <div class="absolute -bottom-6 -end-6 size-36 rounded-full bg-[#0aa8a7]/10"></div>

                <div class="relative overflow-hidden rounded-3xl border border-[#dcebea] bg-white p-7 shadow-xl shadow-slate-900/5 sm:p-9">
                    <span class="flex size-14 items-center justify-center rounded-2xl bg-[#0aa8a7]/10 text-[#078786]">
                        <span class="icon-[tabler--user-code] size-7"></span>
                    </span>

                    <p class="mt-7 text-lg font-bold leading-8 text-[#17212b]">
                        {{ $profile?->translated('short_bio') ?: __('company-profile.public.about_section.summary') }}
                    </p>

                    <dl class="mt-7 grid grid-cols-2 gap-4 border-t border-[#dcebea] pt-6">
                        <div>
                            <dd class="text-2xl font-black text-[#17212b]">{{ $profile?->years_experience ?? 0 }}+</dd>
                            <dt class="mt-1 text-xs leading-5 text-slate-500">{{ __('company-profile.public.hero.years') }}</dt>
                        </div>
                        <div>
                            <dd class="flex items-center gap-1.5 text-base font-extrabold text-[#17212b]">
                                <span class="icon-[tabler--map-pin] size-5 text-[#0aa8a7]"></span>
                                {{ $profile?->location ?: __('company-profile.public.about_section.location_fallback') }}
                            </dd>
                            <dt class="mt-1 text-xs leading-5 text-slate-500">{{ __('company-profile.public.about_section.location') }}</dt>
                        </div>
                    </dl>
                </div>
            </div>

            <div data-reveal>
                <span class="bs-kicker">{{ __('company-profile.public.about_section.eyebrow') }}</span>
                <h1 class="bs-heading mt-5 text-4xl sm:text-5xl">{{ __('company-profile.public.about_section.title') }}</h1>
                <p class="mt-5 whitespace-pre-line text-base leading-8 text-slate-600 sm:text-lg">{{ $profile?->translated('about') ?: __('company-profile.public.about_section.description') }}</p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('projects.index') }}" class="btn btn-primary rounded-full px-7">
                        {{ __('company-profile.public.about_section.primary_action') }}
                        <span class="icon-[tabler--arrow-right] size-4.5"></span>
                    </a>
                    <a href="{{ route('home') }}#contact" class="btn btn-outline btn-primary rounded-full px-7">
                        {{ __('company-profile.public.about_section.secondary_action') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <x-public.experience :experiences="$experiences" />
</x-public.layout>

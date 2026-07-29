@props(['profile'])

<section id="contact" class="bs-section px-4" data-nav-section="contact">
    <div class="bs-cta bs-container grid items-center gap-8 overflow-hidden p-7 sm:p-10 md:grid-cols-[0.8fr_1.2fr] lg:px-16" data-reveal>
        <img class="mx-auto w-full max-w-sm" src="{{ asset('vendor/bigspring/images/cta.svg') }}" alt="" loading="lazy" width="325" height="206">
        <div class="text-center md:text-start">
            <h2 class="bs-heading text-3xl sm:text-4xl">{{ __('company-profile.public.cta.title') }}</h2>
            <p class="mt-5 leading-8">{{ $profile?->translated('about') ?: __('company-profile.public.cta.description') }}</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3 md:justify-start">
                <a href="{{ $profile?->email ? 'mailto:'.$profile->email : route('login') }}" class="btn btn-primary rounded-full px-7">
                    {{ __('company-profile.public.cta.action') }}
                    <span class="icon-[tabler--mail] size-5"></span>
                </a>
                @if ($profile?->phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $profile->phone) }}" class="btn btn-outline rounded-full px-7">
                        <span class="icon-[tabler--phone] size-5"></span>{{ $profile->phone }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

@props(['services', 'profile'])

@if ($services->isNotEmpty())
    @php
        $slides = ['service-slide-1.webp', 'service-slide-2.webp', 'service-slide-3.webp'];
    @endphp

    <section id="services" data-nav-section="services">
        <div class="bs-container pt-16 text-center" data-reveal>
            <span class="bs-kicker">{{ __('company-profile.public.services_section.eyebrow') }}</span>
            <h2 class="bs-heading mx-auto mt-5 max-w-3xl text-3xl sm:text-4xl">{{ __('company-profile.public.services_section.title') }}</h2>
            <p class="mx-auto mt-4 max-w-2xl leading-7">{{ __('company-profile.public.services_section.description') }}</p>
        </div>

        @foreach ($services as $service)
            @php
                $serviceIcon = match ($service->icon) {
                    'database', 'database-cog' => 'icon-[tabler--database-cog]',
                    'cloud', 'cloud-code' => 'icon-[tabler--cloud-code]',
                    'api', 'plug-connected' => 'icon-[tabler--plug-connected]',
                    'performance', 'speedometer' => 'icon-[tabler--gauge]',
                    'device-desktop-code', 'frontend' => 'icon-[tabler--device-desktop-code]',
                    default => 'icon-[tabler--server-cog]',
                };
            @endphp
            <article @class(['bs-section', 'bs-section-light' => $loop->index % 2 === 1])>
                <div class="bs-container grid items-center gap-10 md:grid-cols-2 lg:gap-16">
                    <div @class(['bs-service-visual', 'md:order-2' => $loop->index % 2 === 0]) x-data="bigspringCarousel({{ count($slides) }})" data-reveal>
                        @foreach ($slides as $slide)
                            <img x-cloak x-show="active === {{ $loop->index }}" x-transition.opacity.duration.500ms
                                src="{{ asset('vendor/bigspring/images/'.$slide) }}" alt="" loading="lazy">
                        @endforeach
                        <div class="absolute inset-x-0 bottom-4 z-10 flex justify-center gap-2">
                            @foreach ($slides as $slide)
                                <button type="button" class="size-2.5 rounded-full border border-[#0aa8a7]"
                                    :class="active === {{ $loop->index }} ? 'bg-[#0aa8a7]' : 'bg-white'"
                                    @click="goTo({{ $loop->index }})" aria-label="{{ $service->translated('title') }} {{ $loop->iteration }}"></button>
                            @endforeach
                        </div>
                    </div>

                    <div @class(['md:order-1' => $loop->index % 2 === 0]) data-reveal>
                        @if ($service->icon)
                            <span class="mb-5 flex size-12 items-center justify-center rounded-xl bg-[#0aa8a7]/10 text-[#078786]">
                                <span class="{{ $serviceIcon }} size-6"></span>
                            </span>
                        @endif
                        <h3 class="bs-heading text-3xl">{{ $service->translated('title') }}</h3>
                        <p class="mt-5 leading-8 text-slate-600">
                            {{ $service->translated('content') ?: $service->translated('summary') }}
                        </p>
                        <a href="{{ $service->call_to_action_url ?: ($profile?->email ? 'mailto:'.$profile->email : '#contact') }}"
                            class="mt-6 inline-flex items-center gap-2 font-bold text-[#078786] transition-all hover:gap-3">
                            {{ $service->translated('call_to_action_label') ?: __('company-profile.public.services_section.action') }}
                            <span class="icon-[tabler--arrow-right] size-5"></span>
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </section>
@endif

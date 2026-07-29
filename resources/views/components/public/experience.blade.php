@props(['experiences'])

@if ($experiences->isNotEmpty())
    <section id="experience" class="bs-section">
        <div class="bs-container grid gap-12 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
            <div data-reveal>
                <span class="bs-kicker">{{ __('company-profile.public.experience_section.eyebrow') }}</span>
                <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.experience_section.title') }}</h2>
                <p class="mt-5 leading-8">{{ __('company-profile.public.experience_section.description') }}</p>
            </div>

            <ol class="bs-timeline relative space-y-8 ps-10">
                @foreach ($experiences as $experience)
                    <li class="relative" data-reveal>
                        <span class="absolute -start-[2.15rem] top-1 flex size-5 items-center justify-center rounded-full border-4 border-white bg-[#0aa8a7] shadow"></span>
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#078786]">
                            {{ $experience->started_at?->translatedFormat('M Y') }}
                            —
                            {{ $experience->is_current ? __('company-profile.public.experience_section.current') : $experience->ended_at?->translatedFormat('M Y') }}
                        </p>
                        <h3 class="mt-2 text-xl font-extrabold text-[#17212b]">{{ $experience->translated('role') }}</h3>
                        <p class="mt-1 font-semibold text-slate-500">{{ $experience->company }}@if ($experience->location) · {{ $experience->location }}@endif</p>
                        @if ($experience->translated('summary'))
                            <p class="mt-3 leading-7 text-slate-600">{{ $experience->translated('summary') }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
        <div class="bs-workflow-art relative mt-16 overflow-hidden" data-reveal>
            <img class="absolute inset-x-0 bottom-0 w-full" src="{{ asset('vendor/bigspring/images/banner.svg') }}"
                alt="" loading="lazy" width="1920" height="296">
        </div>
    </section>
@endif

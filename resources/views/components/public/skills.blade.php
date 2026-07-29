@props(['skills'])

@php($icons = ['code.svg', 'oop.svg', 'user-clock.svg', 'love.svg', 'speedometer.svg', 'cloud.svg'])

<section class="bs-section bs-section-light" id="skills">
    <div class="bs-container">
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <span class="bs-kicker">{{ __('company-profile.public.skills.eyebrow') }}</span>
            <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.skills.title') }}</h2>
            <p class="mt-4 leading-7">{{ __('company-profile.public.skills.description') }}</p>
        </div>

        <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($skills as $skill)
                <article class="bs-feature-card" data-reveal>
                    <img class="mx-auto size-9 object-contain" src="{{ asset('vendor/bigspring/images/'.$icons[$loop->index % count($icons)]) }}" alt="">
                    <h3 class="mt-5 text-lg font-extrabold text-[#17212b]">{{ $skill->translated('name') }}</h3>
                    @if ($skill->category)
                        <p class="mt-1 text-sm text-slate-500">{{ $skill->category->translated('name') }}</p>
                    @endif
                    <progress class="progress progress-primary mx-auto mt-5 block h-2 max-w-52"
                        value="{{ min(100, max(0, $skill->proficiency)) }}" max="100"></progress>
                    <div class="mt-3 flex justify-center gap-3 text-xs font-semibold text-slate-500">
                        <span>{{ __('company-profile.public.skills.proficiency', ['value' => $skill->proficiency]) }}</span>
                        @if ($skill->years_experience)
                            <span>·</span>
                            <span>{{ __('company-profile.public.skills.experience', ['value' => $skill->years_experience]) }}</span>
                        @endif
                    </div>
                </article>
            @empty
                @foreach (__('company-profile.public.feature_items') as [$icon, $title, $description])
                    <article class="bs-feature-card" data-reveal>
                        <img class="mx-auto size-9 object-contain" src="{{ asset('vendor/bigspring/images/'.$icons[$loop->index % count($icons)]) }}" alt="">
                        <h3 class="mt-5 text-lg font-extrabold text-[#17212b]">{{ $title }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </article>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

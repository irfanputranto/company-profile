@props(['features'])

<section id="features" class="bs-section bs-section-light">
    <div class="bs-container">
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <span class="bs-kicker">{{ __('company-profile.public.features_section.eyebrow') }}</span>
            <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.features_section.title') }}</h2>
            <p class="mt-4 leading-7">{{ __('company-profile.public.features_section.description') }}</p>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($features as $feature)
                @php
                    $featureIcon = match ($feature->icon) {
                        'messages' => 'icon-[tabler--messages]',
                        'adjustments' => 'icon-[tabler--adjustments-horizontal]',
                        'code' => 'icon-[tabler--code]',
                        'shield-check' => 'icon-[tabler--shield-check]',
                        'timeline' => 'icon-[tabler--timeline]',
                        'headset' => 'icon-[tabler--headset]',
                        default => 'icon-[tabler--sparkles]',
                    };
                @endphp
                <article class="bs-feature-card" data-reveal>
                    <span class="{{ $featureIcon }} mx-auto size-9 text-[#0aa8a7]"></span>
                    <h3 class="mt-5 text-lg font-extrabold text-[#17212b]">{{ $feature->translated('title') }}</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">{{ $feature->translated('description') }}</p>
                </article>
            @empty
                @foreach (__('company-profile.public.feature_items') as [$icon, $title, $description])
                    <article class="bs-feature-card" data-reveal>
                        <span class="{{ $icon }} mx-auto size-9 text-[#0aa8a7]"></span>
                        <h3 class="mt-5 text-lg font-extrabold text-[#17212b]">{{ $title }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </article>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

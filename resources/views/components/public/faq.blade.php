@props(['faqs'])

@if ($faqs->isNotEmpty())
    <section class="bs-section bs-section-light">
        <div class="bs-container grid gap-10 lg:grid-cols-[0.75fr_1.25fr] lg:gap-16">
            <div data-reveal>
                <span class="bs-kicker">{{ __('company-profile.public.faq_section.eyebrow') }}</span>
                <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.faq_section.title') }}</h2>
            </div>
            <div class="divide-y divide-[#dcebea] rounded-2xl border border-[#dcebea] bg-white px-5 sm:px-7" data-reveal>
                @foreach ($faqs as $faq)
                    <details class="group py-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-extrabold text-[#17212b]">
                            {{ $faq->translated('question') }}
                            <span class="icon-[tabler--plus] size-5 shrink-0 text-[#078786] transition-transform group-open:rotate-45"></span>
                        </summary>
                        <p class="pt-4 leading-7 text-slate-600">{{ $faq->translated('answer') }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif

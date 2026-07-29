@props(['testimonials'])

@if ($testimonials->isNotEmpty())
    <section class="bs-section bs-section-light">
        <div class="bs-container">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <span class="bs-kicker">{{ __('company-profile.public.testimonials_section.eyebrow') }}</span>
                <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.testimonials_section.title') }}</h2>
            </div>
            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <figure class="rounded-2xl border border-[#dcebea] bg-white p-6 shadow-sm" data-reveal>
                        <div class="flex gap-1 text-amber-400" aria-label="{{ $testimonial->rating }}/5">
                            @for ($star = 1; $star <= 5; $star++)
                                <span class="icon-[tabler--star-filled] size-4 {{ $star > $testimonial->rating ? 'opacity-20' : '' }}"></span>
                            @endfor
                        </div>
                        <blockquote class="mt-5 leading-7 text-slate-600">“{{ $testimonial->translated('quote') }}”</blockquote>
                        <figcaption class="mt-6">
                            <p class="font-extrabold text-[#17212b]">{{ $testimonial->client_name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $testimonial->translated('client_role') }}@if ($testimonial->company) · {{ $testimonial->company }}@endif</p>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
@endif

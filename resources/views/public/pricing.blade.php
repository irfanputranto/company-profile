<x-public.layout :profile="$profile" :services="$services" :social-links="$socialLinks" active-page="pricing"
    :title="__('company-profile.public.pricing.meta_title')"
    :description="__('company-profile.public.pricing.meta_description')">
    <section class="bs-section">
        <div class="bs-container">
            <div class="mx-auto max-w-3xl text-center" data-reveal>
                <span class="bs-kicker">{{ __('company-profile.public.pricing.eyebrow') }}</span>
                <h1 class="bs-heading mt-5 text-4xl sm:text-5xl">{{ __('company-profile.public.pricing.title') }}</h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-8 sm:text-lg">
                    {{ __('company-profile.public.pricing.description') }}
                </p>
            </div>

            @if ($pricingPlans->isNotEmpty())
                <div class="mt-14 grid items-stretch gap-6 lg:grid-cols-3 lg:gap-0">
                    @foreach ($pricingPlans as $pricingPlan)
                        <article @class([
                            'relative flex flex-col border border-[#dcebea] bg-white p-7 text-center sm:p-9',
                            'rounded-2xl shadow-[0_1.75rem_4rem_-1.5rem_rgb(45_67_121_/_28%)] lg:z-10 lg:-my-6 lg:py-12' => $pricingPlan->is_featured,
                            'rounded-2xl lg:first:rounded-e-none lg:last:rounded-s-none' => ! $pricingPlan->is_featured,
                        ]) data-reveal>
                            @if ($pricingPlan->is_featured)
                                <span class="badge badge-primary absolute start-1/2 top-0 -translate-x-1/2 -translate-y-1/2 px-4 py-3">
                                    {{ __('company-profile.public.pricing.recommended') }}
                                </span>
                            @endif

                            <h2 class="text-2xl font-extrabold text-[#17212b]">{{ $pricingPlan->translated('title') }}</h2>
                            <p class="mt-2 min-h-12 text-sm leading-6 text-slate-500">{{ $pricingPlan->translated('tagline') }}</p>

                            <div class="mt-6">
                                @if ($pricingPlan->price !== null)
                                    <p class="text-4xl font-black tracking-tight text-[#17212b]">
                                        {{ \Illuminate\Support\Number::currency((float) $pricingPlan->price, in: $pricingPlan->currency, locale: app()->getLocale()) }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ __("company-profile.public.pricing.periods.{$pricingPlan->billing_period}") }}
                                    </p>
                                @else
                                    <p class="text-3xl font-black text-[#17212b]">{{ __('company-profile.public.pricing.custom_price') }}</p>
                                @endif
                            </div>

                            <p class="mt-5 text-sm leading-6 text-slate-500">{{ $pricingPlan->translated('description') }}</p>

                            <ul class="mt-7 grid gap-3 text-start text-sm">
                                @foreach ($pricingPlan->features as $feature)
                                    <li class="flex items-start gap-2.5">
                                        <span class="icon-[tabler--circle-check-filled] mt-0.5 size-5 shrink-0 text-[#0aa8a7]"></span>
                                        <span>{{ $feature->translated('title') }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <a href="{{ $pricingPlan->call_to_action_url ?: ($profile?->email ? 'mailto:'.$profile->email : route('home').'#contact') }}"
                                @class([
                                    'btn mt-8 rounded-full px-6',
                                    'btn-primary' => $pricingPlan->is_featured,
                                    'btn-outline btn-primary' => ! $pricingPlan->is_featured,
                                ])>
                                {{ $pricingPlan->translated('call_to_action_label') ?: __('company-profile.public.pricing.action') }}
                            </a>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-12 rounded-2xl border border-dashed border-[#b9d8d5] bg-[#edf6f5] p-10 text-center">
                    <p>{{ __('company-profile.public.pricing.empty') }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="pb-20 sm:pb-24">
        <div class="bs-cta bs-container grid items-center gap-8 overflow-hidden p-7 sm:p-10 md:grid-cols-2 lg:px-16" data-reveal>
            <img class="mx-auto w-full max-w-sm" src="{{ asset('vendor/bigspring/images/cta.svg') }}"
                alt="" loading="lazy" width="325" height="206">
            <div class="text-center md:text-start">
                <span class="bs-kicker">{{ __('company-profile.public.pricing.custom_eyebrow') }}</span>
                <h2 class="bs-heading mt-5 text-3xl sm:text-4xl">{{ __('company-profile.public.pricing.custom_title') }}</h2>
                <p class="mt-4 leading-7">{{ __('company-profile.public.pricing.custom_description') }}</p>
                <a href="{{ $profile?->email ? 'mailto:'.$profile->email : route('home').'#contact' }}"
                    class="btn btn-primary mt-7 rounded-full px-7">
                    {{ __('company-profile.public.pricing.custom_action') }}
                    <span class="icon-[tabler--arrow-up-right] size-4.5"></span>
                </a>
            </div>
        </div>
    </section>
</x-public.layout>

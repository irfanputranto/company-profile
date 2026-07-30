<x-public.layout :profile="$profile" :services="$services" :social-links="$socialLinks" active-page="home">
    <x-public.hero :profile="$profile" :services="$services" />
    <x-public.features :features="$features" />
    <x-public.services :services="$services" :profile="$profile" />
    <x-public.experience :experiences="$experiences" />
    <x-public.skills :skills="$skills" />
    <x-public.testimonials :testimonials="$testimonials" />
    <x-public.articles :articles="$articles" />
    <x-public.faq :faqs="$faqs" />
    <x-public.cta :profile="$profile" />
</x-public.layout>

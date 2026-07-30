<x-public.layout :profile="$profile" :services="$services" :social-links="$socialLinks" active-page="projects"
    :title="__('company-profile.public.projects_page.meta_title')"
    :description="__('company-profile.public.projects_page.meta_description')">
    <x-public.projects :projects="$projects" />
</x-public.layout>

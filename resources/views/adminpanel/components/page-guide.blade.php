@props([
    'guide',
    'model' => 'pageGuideOpen',
])

<x-adminpanel::components.report-guide-modal
    :model="$model"
    :title="$guide['title']"
    :description="$guide['description']"
    :eyebrow="$guide['eyebrow'] ?? __('admin.guide.eyebrow')"
    :icon="$guide['icon'] ?? 'icon-[tabler--help-circle]'"
    title-id="global-page-guide-title"
    dialog-id="global-page-guide-dialog"
    :confirm-label="__('admin.guide.close')"
>
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <section aria-labelledby="page-guide-steps-title">
            <div class="mb-4 flex items-start gap-3">
                <span class="bg-primary/10 text-primary flex size-10 shrink-0 items-center justify-center rounded-xl">
                    <span class="icon-[tabler--route] size-5" aria-hidden="true"></span>
                </span>
                <div>
                    <h3 id="page-guide-steps-title" class="text-base-content text-lg font-semibold">{{ __('admin.guide.usage_title') }}</h3>
                    <p class="text-base-content/60 mt-1 text-sm leading-6">{{ __('admin.guide.usage_description') }}</p>
                </div>
            </div>

            <ol class="space-y-3">
                @foreach ($guide['steps'] as $index => $step)
                    <li class="border-base-content/10 bg-base-200/35 flex gap-3 rounded-2xl border p-4">
                        <span class="bg-primary text-primary-content flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-bold">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0">
                            <h4 class="text-base-content font-semibold">{{ $step['title'] }}</h4>
                            <p class="text-base-content/65 mt-1 text-sm leading-6">{{ $step['description'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>

        <aside class="space-y-4">
            @if (! empty($guide['tips']))
                <section class="border-info/20 bg-info/5 rounded-2xl border p-4" aria-labelledby="page-guide-tips-title">
                    <div class="flex items-center gap-2 text-info">
                        <span class="icon-[tabler--bulb] size-5" aria-hidden="true"></span>
                        <h3 id="page-guide-tips-title" class="font-semibold">{{ __('admin.guide.tips_title') }}</h3>
                    </div>
                    <ul class="text-base-content/70 mt-3 space-y-2 text-sm leading-6">
                        @foreach ($guide['tips'] as $tip)
                            <li class="flex items-start gap-2">
                                <span class="icon-[tabler--check] text-info mt-1 size-4 shrink-0" aria-hidden="true"></span>
                                <span>{{ $tip }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if (! empty($guide['warning']))
                <section class="border-warning/30 bg-warning/10 rounded-2xl border p-4" aria-labelledby="page-guide-warning-title">
                    <div class="flex items-center gap-2 text-warning">
                        <span class="icon-[tabler--alert-triangle] size-5" aria-hidden="true"></span>
                        <h3 id="page-guide-warning-title" class="font-semibold">{{ __('admin.guide.warning_title') }}</h3>
                    </div>
                    <p class="text-base-content/70 mt-2 text-sm leading-6">{{ $guide['warning'] }}</p>
                </section>
            @endif

            <section class="border-base-content/10 rounded-2xl border p-4">
                <div class="flex items-center gap-2">
                    <span class="icon-[tabler--lifebuoy] text-primary size-5" aria-hidden="true"></span>
                    <h3 class="font-semibold">{{ __('admin.guide.help_title') }}</h3>
                </div>
                <p class="text-base-content/65 mt-2 text-sm leading-6">{{ __('admin.guide.help_description') }}</p>
            </section>
        </aside>
    </div>
</x-adminpanel::components.report-guide-modal>

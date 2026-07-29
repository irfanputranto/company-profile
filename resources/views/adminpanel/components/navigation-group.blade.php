@props(['id', 'label', 'active' => false])

<li x-data="{ open: @js((bool) $active) }">
    <button type="button"
        class="hover:bg-base-200 focus-visible:ring-primary flex w-full cursor-pointer items-center gap-2 rounded-md p-2 text-start text-sm font-normal transition focus-visible:outline-none focus-visible:ring-2"
        data-navigation-group-toggle
        aria-controls="{{ $id }}"
        :aria-expanded="open.toString()"
        @click="open = !open">
        <span class="icon-[tabler--folder] size-4.5 shrink-0"></span>
        <span class="min-w-0 flex-1 truncate">{{ $label }}</span>
        <span class="icon-[tabler--chevron-right] text-base-content/60 size-4 shrink-0 transition-transform duration-200"
            :class="{ 'rotate-90': open }"></span>
    </button>
    <ul id="{{ $id }}" x-cloak x-show="open" data-navigation-group-content
        class="before:bg-base-content/10">
        {{ $slot }}
    </ul>
</li>

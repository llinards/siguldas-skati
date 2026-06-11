@props(['title'])

@php
    $sectionId = 'site-setting-'.\Illuminate\Support\Str::slug($title);
@endphp

<div class="hs-accordion w-full overflow-hidden rounded-lg border border-gray-200" id="{{ $sectionId }}-heading">
    <button
        type="button"
        class="hs-accordion-toggle flex w-full cursor-pointer items-center justify-between gap-4 bg-gray-50 px-6 py-4 text-left transition-colors duration-200 hover:bg-gray-100"
        aria-expanded="false"
        aria-controls="{{ $sectionId }}-collapse"
    >
        <span class="text-h-sm-mob lg:text-h-mob leading-none">{{ $title }}</span>
        <x-accordion-arrows />
    </button>
    <div
        id="{{ $sectionId }}-collapse"
        class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300"
        role="region"
        aria-labelledby="{{ $sectionId }}-heading"
    >
        <div class="px-6 py-6">
            {{ $slot }}
        </div>
    </div>
</div>

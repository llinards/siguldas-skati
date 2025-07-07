<div class="bg-ss-dark flex flex-col rounded-3xl p-8">
    <div class="mb-3 flex items-center gap-x-6 md:mb-6 xl:gap-x-6">
        <img src="/icons/{{ $experienceSvg ?? '' }}.svg" class="h-12 w-12 xl:w-17" />
        <h3 class="text-3xl leading-none text-balance md:text-[2.5rem] lg:text-4xl xl:text-3xl 2xl:text-4xl">
            {{ $experienceTitle ?? '' }}
        </h3>
    </div>
    <p>{{ $experienceText ?? '' }}</p>
</div>

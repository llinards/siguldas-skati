<div class="bg-ss-dark rounded-3xl p-8 flex flex-col">
    <div class="flex items-center gap-x-6 xl:gap-x-6 mb-3 md:mb-6">
        <img src="/icons/{{ $experienceSvg ?? '' }}.svg" class="w-12 h-12 xl:w-17">
        <h3 class="text-3xl md:text-[2.5rem] lg:text-4xl xl:text-3xl 2xl:text-4xl leading-none text-balance">{{
            $experienceTitle ?? '' }}
        </h3>
    </div>
    <p>{{ $experienceText ?? ''}}</p>
</div>
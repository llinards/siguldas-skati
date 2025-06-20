{{-- DESKTOP --}}
<div class="hidden sm:block xl:grid grid-cols-2 2xl:grid-cols-5 gap-4 pb-12 xl:pb-30">
    <div class="2xl:col-span-3">
        <div class="relative inline-block mb-3">
            <h2 class="mb-3 leading-none text-h-sm-mob lg:text-h-mob">@lang('Ērtības un
                aprīkojums')</h2>
            <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
        </div>
        <p class="text-justify mb-6 lg:max-w-11/12">{{ $product->description }}</p>
        <x-product.description.facilities/>
    </div>
    <div class="2xl:col-span-2">
        <div class="relative inline-block mb-3">
            <h2 class="mt-6 xl:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-mob">@lang('Cenas un papildu
                informācija')</h2>
            <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
        </div>
        <x-product.description.pricing/>
        <div class="sm:mb-6 flex xl:justify-end">
            <x-btn-primary>@lang('Rezervēt')</x-btn-primary>
        </div>
    </div>

    <div class="hidden sm:block col-span-5 xl:border-b-2">
        <div class="relative inline-block mb-3">
            <h2 class="mt-6 xl:mt-0 mb-3 leading-none text-h-sm-mob lg:text-h-mob">@lang('Lietas, ko ņemt vērā')</h2>
            <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
        </div>
        <x-product.description.good-to-know/>
    </div>
</div>


{{-- MOBILE --}}
<div class="sm:hidden hs-accordion-group space-y-6 pb-6" data-hs-accordion-always-open="">
    <div class="hs-accordion active" id="hs-basic-with-arrow-heading-one">
        <button class="hs-accordion-toggle border-b-2 mb-3 pt-3 relative flex items-center w-full justify-between"
                aria-expanded="true" aria-controls="hs-basic-with-arrow-collapse-one">

            <h2 class="text-left leading-none text-h-sm-mob lg:text-h-mob">@lang('Ērtības un
                aprīkojums')</h2>
            <x-accordion-arrows/>

        </button>
        <div id="hs-basic-with-arrow-collapse-one"
             class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300"
             role="region" aria-labelledby="hs-basic-with-arrow-heading-one">
            <p class="text-justify mb-6 lg:max-w-11/12">{{ $product->description }}</p>
            <x-product.description.facilities/>
        </div>
    </div>

    <div class="hs-accordion" id="hs-basic-with-arrow-heading-two">
        <button class="hs-accordion-toggle border-b-2 mb-3 pt-3 relative flex items-center w-full justify-between"
                aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-two">
            <h2 class="text-left leading-none text-h-sm-mob lg:text-h-mob">@lang('Cenas un papildu
                informācija')</h2>
            <x-accordion-arrows/>
        </button>
        <div id="hs-basic-with-arrow-collapse-two"
             class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region"
             aria-labelledby="hs-basic-with-arrow-heading-two">
            <x-product.description.pricing/>
        </div>
    </div>

    <div class="hs-accordion" id="hs-basic-with-arrow-heading-three">
        <button class="hs-accordion-toggle border-b-2 mb-3 pt-3 relative flex items-center w-full justify-between"
                aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-three">
            <h2 class="text-left leading-none text-h-sm-mob lg:text-h-mob">@lang('Lietas, ko ņemt vērā')</h2>
            <x-accordion-arrows/>
        </button>
        <div id="hs-basic-with-arrow-collapse-three"
             class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region"
             aria-labelledby="hs-basic-with-arrow-heading-three">
            <x-product.description.good-to-know/>
        </div>
    </div>
</div>

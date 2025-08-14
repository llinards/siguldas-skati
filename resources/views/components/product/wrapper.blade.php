{{-- DESKTOP --}}
<div class="hidden grid-cols-2 gap-4 pb-12 sm:block xl:grid xl:pb-30 2xl:grid-cols-5">
    <div class="2xl:col-span-3">
        <div class="relative mb-3 inline-block">
            <h2 class="text-h-sm-mob lg:text-h-mob mb-3 leading-none border-b-2">
                {{-- prettier-ignore --}}
                @lang('Ērtības un aprīkojums')
            </h2>
        </div>
        <p class="mb-6 text-justify lg:max-w-11/12">
            {{ $product->description }}
        </p>
        <x-product.description.facilities :product="$product"/>
    </div>
    <div class="2xl:col-span-2">
        <div class="relative mb-3 inline-block">
            <h2 class="text-h-sm-mob lg:text-h-mob mt-6 mb-3 leading-none xl:mt-0 border-b-2">
                {{-- prettier-ignore --}}
                @lang('Cenas un papildu informācija')
            </h2>
        </div>
        <div class="product-pricelist">
            {!! $product->pricelist !!}
        </div>
        <div class="flex xl:justify-end">
            <x-btn-primary target="_blank"
                           href="https://www.booking.com/hotel/lv/siguldas-skati-sigulda.lv.html">@lang('Rezervēt')</x-btn-primary>
        </div>
    </div>

    <div class="col-span-5 hidden sm:block xl:border-b-2">
        <div class="relative mb-3 inline-block">
            <h2 class="text-h-sm-mob lg:text-h-mob mt-6 mb-3 leading-none xl:mt-0 border-b-2">
                @lang('Lietas, ko ņemt vērā')
            </h2>
        </div>
        <x-product.description.good-to-know/>
    </div>
</div>

{{-- MOBILE --}}
<div class="hs-accordion-group space-y-6 pb-6 sm:hidden" data-hs-accordion-always-open="">
    <div class="hs-accordion active" id="hs-basic-with-arrow-heading-one">
        <button class="hs-accordion-toggle relative mb-3 flex w-full items-center justify-between border-b-2 pt-3"
                aria-expanded="true" aria-controls="hs-basic-with-arrow-collapse-one">
            <h2 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                {{-- prettier-ignore --}}
                @lang('Ērtības un aprīkojums')
            </h2>
            <x-accordion-arrows/>
        </button>
        <div id="hs-basic-with-arrow-collapse-one"
             class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" role="region"
             aria-labelledby="hs-basic-with-arrow-heading-one">
            <p class="mb-6 text-justify lg:max-w-11/12">
                {{ $product->description }}
            </p>
            <x-product.description.facilities :product="$product"/>
        </div>
    </div>

    <div class="hs-accordion" id="hs-basic-with-arrow-heading-two">
        <button class="hs-accordion-toggle relative mb-3 flex w-full items-center justify-between border-b-2 pt-3"
                aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-two">
            <h2 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                {{-- prettier-ignore --}}
                @lang('Cenas un papildu informācija')
            </h2>
            <x-accordion-arrows/>
        </button>
        <div id="hs-basic-with-arrow-collapse-two"
             class="hs-accordion-content product-pricelist hidden w-full overflow-hidden transition-[height] duration-300"
             role="region" aria-labelledby="hs-basic-with-arrow-heading-two">
            {!! $product->pricelist !!}
        </div>
    </div>

    <div class="hs-accordion" id="hs-basic-with-arrow-heading-three">
        <button class="hs-accordion-toggle relative mb-3 flex w-full items-center justify-between border-b-2 pt-3"
                aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-three">
            <h2 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                @lang('Lietas, ko ņemt vērā')
            </h2>
            <x-accordion-arrows/>
        </button>
        <div id="hs-basic-with-arrow-collapse-three"
             class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region"
             aria-labelledby="hs-basic-with-arrow-heading-three">
            <x-product.description.good-to-know/>
        </div>
    </div>
</div>

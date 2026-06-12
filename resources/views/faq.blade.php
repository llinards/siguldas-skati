<x-app-layout :title="$faqTitle">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mt-26 lg:mt-30 xl:mt-36 mb-3 inline-block">
                <div class="flex sm:inline-block justify-center items-start border-b-2">
                    <x-btn-back class="pb-3 mr-5" />
                    <h1 class="text-h-mob lg:text-h-md leading-none">
                        {{ $faqTitle }}
                    </h1>
                </div>
            </div>
            <p class="text-ss-gray mb-12 text-sm leading-none">
                {{ $faqSubtitle }}
            </p>

            @if ($faqs->isNotEmpty())
                <div class="hs-accordion-group container mx-auto space-y-6" data-hs-accordion-always-open="">
                    @foreach ($faqs as $faq)
                        <div class="hs-accordion w-full" id="hs-faq-heading-{{ $faq->id }}">
                            <button
                                class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                                aria-expanded="false" aria-controls="hs-faq-collapse-{{ $faq->id }}">
                                <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                                    {{ $faq->question }}
                                </h3>
                                <x-accordion-arrows/>
                            </button>
                            <div id="hs-faq-collapse-{{ $faq->id }}"
                                 class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                                 role="region" aria-labelledby="hs-faq-heading-{{ $faq->id }}">
                                <div class="text-gray-700 pb-4">
                                    {!! $faq->answer !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout :title="$product->title" :description="$product->description">
    <div class="bg-ss">
        <div class="container mx-auto grid grid-cols-1 px-4">
            <x-product.title>
                <x-slot name="productTitle">{{ $product->title }}</x-slot>
                <x-slot name="productCapacityLong">
                    @lang('Paredzēts')
                    {{
                    $product->person_count === 1
                    ? __('1 personai')
                    : __(':count personām', ['count' => $product->person_count])
                    }}
                </x-slot>
            </x-product.title>

            <x-product.gallery.mobile :product="$product" />
            <div class="mt-6 mb-6 sm:hidden">
                <x-btn-primary>@lang('Rezervēt')</x-btn-primary>
            </div>

            <x-product.gallery.desktop :product="$product" />

            <x-product.wrapper :product="$product" />
        </div>
    </div>

    {{-- PRODUCTS CAROUSEL --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mb-3 inline-block">
                <h2 class="text-h-mob lg:text-h-md leading-none">
                    @lang('Apskati citas dizaina mājas!')
                </h2>
                <span class="bg-ss-dark absolute bottom-0 left-0 h-0.5 w-2/3"></span>
            </div>
            <p class="text-ss-gray pb-6 text-sm leading-none">
                @lang('Izsmalcināta atpūta starp pilsētu un dabu!')
            </p>
            <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
        </div>
    </div>

    <div id="modal"
        class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
        role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-modal-label">
        <div
            class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
            <div
                class="bg-ss flex max-h-[90vh] flex-col overflow-hidden rounded-xl border shadow-2xs w-xs sm:w-lg md:w-xl xl:w-2xl">
                <div class="border-ss-dark flex items-center justify-between border-b px-4 py-3">
                    <h3 id="modal-label" class="text-h-sm-mob lg:text-h-mob leading-none ">
                        @lang('Papildērtības')
                    </h3>
                    <span id="modalBtnClose" type="button"
                        class="bg-ss-dark hover:bg-white inline-flex size-8 items-center justify-center gap-x-2 rounded-full border border-transparent hover:border-ss-dark transition-all duration-200 cursor-pointer group"
                        aria-label="Close" tabindex="2">
                        <span class="sr-only">Close</span>
                        <svg class="size-4 shrink-0 stroke-white group-hover:stroke-ss-dark group-hover:duration-200"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </span>
                </div>

                <div class="overflow-y-auto p-4">
                    <ul class="sm:grid mb-6 grid-cols-2 grid-rows-5 space-y-3">
                        @foreach ($product->features as $feature)
                        <li class="flex items-center gap-x-4">
                            <img src="{{ Storage::url($feature->icon_image) }}" alt="{{ $feature->title }}"
                                class="h-8 w-8" />
                            {{ $feature->title }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="border-ss-gray gap-x-2 border-t px-4 py-3">
                    <x-btn-primary tabindex="3">@lang('Rezervēt')</x-btn-primary>
                </div>
            </div>
        </div>
    </div>

    <button type="button"
        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
        aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-vertically-centered-scrollable-modal"
        data-hs-overlay="#hs-vertically-centered-scrollable-modal">
        Vertically centered scrollable modal
    </button>

    <div id="hs-vertically-centered-scrollable-modal"
        class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
        role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-scrollable-modal-label">
        <div
            class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto h-[calc(100%-56px)] min-h-[calc(100%-56px)] flex items-center">
            <div
                class="w-full max-h-full overflow-hidden flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                <div
                    class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 id="hs-vertically-centered-scrollable-modal-label"
                        class="font-bold text-gray-800 dark:text-white">
                        Modal title
                    </h3>
                    <button type="button"
                        class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600"
                        aria-label="Close" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Be bold</h3>
                            <p class="mt-1 text-gray-800 dark:text-neutral-400">
                                Motivate teams to do their best work. Offer best practices to get users going in the
                                right
                                direction. Be bold and offer just enough help to get the work started, and then get out
                                of
                                the way. Give accurate information so users can make educated decisions. Know your
                                user's
                                struggles and desired outcomes and give just enough information to let them get where
                                they
                                need to go.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Be optimistic</h3>
                            <p class="mt-1 text-gray-800 dark:text-neutral-400">
                                Focusing on the details gives people confidence in our products. Weave a consistent
                                story
                                across our fabric and be diligent about vocabulary across all messaging by being brand
                                conscious across products to create a seamless flow across all the things. Let people
                                know
                                that they can jump in and start working expecting to find a dependable experience across
                                all
                                the things. Keep teams in the loop about what is happening by informing them of relevant
                                features, products and opportunities for success. Be on the journey with them and
                                highlight
                                the key points that will help them the most - right now. Be in the moment by focusing
                                attention on the important bits first.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Be practical, with a wink
                            </h3>
                            <p class="mt-1 text-gray-800 dark:text-neutral-400">
                                Keep our own story short and give teams just enough to get moving. Get to the point and
                                be
                                direct. Be concise - we tell the story of how we can help, but we do it directly and
                                with
                                purpose. Be on the lookout for opportunities and be quick to offer a helping hand. At
                                the
                                same time realize that novbody likes a nosy neighbor. Give the user just enough to know
                                that
                                something awesome is around the corner and then get out of the way. Write clear,
                                accurate,
                                and concise text that makes interfaces more usable and consistent - and builds trust. We
                                strive to write text that is understandable by anyone, anywhere, regardless of their
                                culture
                                or language so that everyone feels they are part of the team.
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                    <button type="button"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                        data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                        Close
                    </button>
                    <button type="button"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- <script type="module">
        const productModalBtnOpen = document.querySelectorAll('.modalBtnOpen');
            const productModalBtnClose = document.getElementById('modalBtnClose');
            const productModal = document.getElementById('modal');
            const productModalContainer = document.getElementById('modalContainer');
    
            function closeModal() {
                productModalContainer.classList.remove('opacity-100');
                productModalContainer.classList.add('opacity-0');
                productModalContainer.addEventListener('transitionend', function handler() {
                    productModal.close();
                    productModalContainer.removeEventListener('transitionend', handler);
                });
            }
    
            if (productModal) {
                productModalBtnOpen.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        productModalContainer.classList.add('modal-open');
                        document.body.classList.add('overflow-hidden');
                        productModal.showModal();
    
                        productModalContainer.classList.add('opacity-0');
                        productModalContainer.classList.remove('opacity-100');
    
                        // Ensures transition.
                        void productModalContainer.offsetWidth;
    
                        productModalContainer.classList.add('opacity-100');
                        productModalContainer.classList.remove('opacity-0');
                    });
                });
    
                productModalBtnClose.addEventListener('click', () => {
                    productModalContainer.classList.remove('modal-open');
                    document.body.classList.remove('overflow-hidden');
                    closeModal();
                });
    
                productModalContainer.addEventListener('click', function (e) {
                    if (e.target === productModalContainer) {
                        productModalContainer.classList.remove('modal-open');
                        document.body.classList.remove('overflow-hidden');
                        closeModal();
                    }
                });
            }
    </script>

    {{-- PRODUCT MODAL --}}
    {{-- <dialog id="modal">
        <summary tabindex="1" id="modalContainer"
            class="fixed z-130 flex h-full w-full items-center justify-center bg-black/50 opacity-0 transition-opacity duration-300">
            <div
                class="bg-ss flex max-h-[90vh] flex-col overflow-hidden rounded-xl border shadow-2xs w-xs sm:w-lg md:w-xl xl:w-2xl">
                <div class="border-ss-dark flex items-center justify-between border-b px-4 py-3">
                    <h3 id="modal-label" class="text-h-sm-mob lg:text-h-mob leading-none ">
                        @lang('Papildērtības')
                    </h3>
                    <span id="modalBtnClose" type="button"
                        class="bg-ss-dark hover:bg-white inline-flex size-8 items-center justify-center gap-x-2 rounded-full border border-transparent hover:border-ss-dark transition-all duration-200 cursor-pointer group"
                        aria-label="Close" tabindex="2">
                        <span class="sr-only">Close</span>
                        <svg class="size-4 shrink-0 stroke-white group-hover:stroke-ss-dark group-hover:duration-200"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </span>
                </div>

                <div class="overflow-y-auto p-4">
                    <ul class="sm:grid mb-6 grid-cols-2 grid-rows-5 space-y-3">
                        @foreach ($product->features as $feature)
                        <li class="flex items-center gap-x-4">
                            <img src="{{ Storage::url($feature->icon_image) }}" alt="{{ $feature->title }}"
                                class="h-8 w-8" />
                            {{ $feature->title }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="border-ss-gray gap-x-2 border-t px-4 py-3">
                    <x-btn-primary tabindex="3">@lang('Rezervēt')</x-btn-primary>
                </div>
            </div>
        </summary>
    </dialog> --}}


</x-app-layout>
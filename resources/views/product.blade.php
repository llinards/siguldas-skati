<x-app-layout :title="$product->title">
    <div class="bg-ss">
        <div class="container mx-auto px-4 grid grid-cols-1">
            <x-product.title>
                <x-slot name="productTitle">{{$product->title}}</x-slot>
                <x-slot name="productCapacityLong">@lang('Paredzēts') {{ $product->person_count === 1 ? __('1 personai')
                    : __(':count personām', ['count' =>
                    $product->person_count]) }}</x-slot>
            </x-product.title>

            <x-product.gallery.mobile />
            <div class="sm:hidden mt-6 mb-6">
                <x-btn-primary>@lang('Rezervēt')</x-btn-primary>
            </div>

            <x-product.gallery.desktop />

            <x-product.wrapper :product="$product" />

        </div>
    </div>

    {{-- PRODUCTS CAROUSEL --}}
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative inline-block mb-3">
                <h2 class="text-h-mob lg:text-h-md leading-none">@lang('Apskati citas dizaina mājas!')</h2>
                <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
            </div>
            <p class="text-sm text-ss-gray pb-6 leading-none">
                @lang('Izsmalcināta atpūta starp pilsētu un dabu!')</p>
            <x-carousels.products.wrapper :products="$products"></x-carousels.products.wrapper>
        </div>
    </div>

    {{-- PRODUCT MODAL --}}
    <dialog id="modal">
        <div id="modalContainer"
            class="fixed z-130 flex items-center justify-center h-full w-full bg-black/50 opacity-0 transition-opacity duration-300">
            <div class="flex flex-col border bg-ss shadow-2xs rounded-xl">
                <div class=" flex justify-between items-center py-3 px-4 border-b border-ss-dark">
                    <h3 id="modal-label" class="font-bold ">
                        @lang('Papildērtības')</h3>
                    <button id="modalBtnClose" type="button"
                        class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-ss-dark hover:bg-ss-gray transition-all duration-200"
                        aria-label="Close">
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-4 overflow-y-auto">
                    <ul class="xs:grid grid-cols-2 grid-rows-5 mb-6 space-y-3">
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/wi-fi.svg') }}" alt="WiFi"
                                class="w-8 h-8">@lang('Wi-Fi')</li>
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/microwave.svg') }}"
                                alt="@lang('Mikroviļņu krāsns')" class="w-8 h-8">@lang('Virtuves
                            zona ar
                            viesistabu')</li>
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/washing-machine.svg') }}"
                                alt="@lang('Veļas mašīna')" class="w-8 h-8">@lang('Veļas
                            mašīna')</li>
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/bone.svg') }}"
                                alt="@lang('Kauls')" class="w-8 h-8">@lang('Atļauts ar
                            mājdzīvniekiem')</li>
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/ac.svg') }}"
                                alt="@lang('Gaisa kondicionieris')" class="w-8 h-8">@lang('Gaisa
                            kondicionieris')
                        </li>
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/fire.svg') }}"
                                alt="@lang('Uguns')" class="w-8 h-8">@lang('Ugunskura
                            vieta')</li>
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/fridge.svg') }}"
                                alt="@lang('Ledusskapis')" class="w-8 h-8">@lang('Ledusskapis')
                        </li>
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/camera.svg') }}"
                                alt="@lang('Kamera')" class="w-8 h-8">@lang('Teritorija
                            tiek
                            apsargāta')</li>
                        <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/bicycle.svg') }}"
                                alt="@lang('Velosipēds')" class="w-8 h-8">@lang('Velosipēdu
                            novietne')
                        </li>
                    </ul>
                </div>

                <div class="gap-x-2 py-3 px-4 border-t border-ss-gray">
                    <x-btn-primary>@lang('Rezervēt')</x-btn-primary>
                </div>
            </div>
        </div>
    </dialog>

    <script type="module">
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
            productModalBtnOpen.forEach(btn => {
            btn.addEventListener('click', () => {
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
            closeModal();
        });
    
        productModalContainer.addEventListener('click', function (e) {
            if (e.target === productModalContainer) {
            closeModal();
                }
            })
        }
    </script>

</x-app-layout>
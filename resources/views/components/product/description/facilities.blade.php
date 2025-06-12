<p class="text-justify mb-6 lg:max-w-11/12">@lang('Pārvietojama koka karkasa moduļu māja. Šāda
    veida
    māju
    būvniecība ir
    salīdzinoši ātra
    un neaizņem ilgu projekta
    saskaņošanas laiku. Šim projektam nav nepieciešama būvatļauja (līdz apbūves platībai 60m²)
    un ir iespējams dzīvot
    uzreiz. Projektos ir iespējami dažādi iekšējās un ārējās apdares risinājumi, kā arī ir
    iespējams veikt izmaiņas telpu
    plānojumos.')</p>

<ul class="xs:grid grid-cols-2 grid-rows-5 mb-6 space-y-3">
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/wi-fi.svg') }}" alt="WiFi"
            class="w-8 h-8">@lang('Wi-Fi')</li>
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/microwave.svg') }}" alt="@lang('Mikroviļņu krāsns')"
            class="w-8 h-8">@lang('Virtuves
        zona ar
        viesistabu')</li>
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/washing-machine.svg') }}"
            alt="@lang('Veļas mašīna')" class="w-8 h-8">@lang('Veļas
        mašīna')</li>
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/bone.svg') }}" alt="@lang('Kauls')"
            class="w-8 h-8">@lang('Atļauts ar
        mājdzīvniekiem')</li>
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/ac.svg') }}" alt="@lang('Gaisa kondicionieris')"
            class="w-8 h-8">@lang('Gaisa
        kondicionieris')
    </li>
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/fire.svg') }}" alt="@lang('Uguns')"
            class="w-8 h-8">@lang('Ugunskura
        vieta')</li>
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/fridge.svg') }}" alt="@lang('Ledusskapis')"
            class="w-8 h-8">@lang('Ledusskapis')
    </li>
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/camera.svg') }}" alt="@lang('Kamera')"
            class="w-8 h-8">@lang('Teritorija
        tiek
        apsargāta')</li>
    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/bicycle.svg') }}" alt="@lang('Velosipēds')"
            class="w-8 h-8">@lang('Velosipēdu
        novietne')
    </li>
</ul>




<div class="flex mb-6">
    <x-btn-primary id="button" class="w-full" aria-haspopup="dialog" aria-expanded="false" aria-controls="modal"
        data-hs-overlay="#modal">@lang('Rādīt visas papildērtības (25)')
    </x-btn-primary>
</div>

<div id="modal"
    class="hs-overlay hidden size-full fixed top-0 start-0 z-130 overflow-x-hidden overflow-y-auto pointer-events-auto"
    role="dialog" tabindex="-1" aria-labelledby="modal-label">
    <div
        class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto h-[calc(100%-56px)] min-h-[calc(100%-56px)] flex items-center">
        <div
            class="w-full max-h-full overflow-hidden flex flex-col border bg-ss shadow-2xs rounded-xl pointer-events-auto">

            <div class="flex justify-between w-full items-center py-3 px-4 border-b border-ss-dark">
                <h3 id="modal-label" class="font-bold ">
                    @lang('Papildērtības')</h3>
                <button type="button"
                    class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-ss-dark hover:bg-ss-gray transition-all duration-200"
                    aria-label="Close" data-hs-overlay="#modal">
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
                    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/bone.svg') }}" alt="@lang('Kauls')"
                            class="w-8 h-8">@lang('Atļauts ar
                        mājdzīvniekiem')</li>
                    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/ac.svg') }}"
                            alt="@lang('Gaisa kondicionieris')" class="w-8 h-8">@lang('Gaisa
                        kondicionieris')
                    </li>
                    <li class="flex gap-x-4 items-center"><img src="{{ asset('icons/fire.svg') }}" alt="@lang('Uguns')"
                            class="w-8 h-8">@lang('Ugunskura
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

            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-ss-gray">
                <x-btn-primary>@lang('Rezervēt')</x-btn-primary>
            </div>
        </div>
    </div>
</div>
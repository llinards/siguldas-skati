<ul class="xs:grid mb-3 grid-cols-2 grid-rows-5 space-y-3">
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/wi-fi.svg') }}" alt="WiFi" class="h-8 w-8" />
        @lang('Wi-Fi')
    </li>
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/microwave.svg') }}" alt="@lang('Mikroviļņu krāsns')" class="h-8 w-8" />
        @lang('Virtuves
                    zona ar
                    viesistabu')
    </li>
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/washing-machine.svg') }}" alt="@lang('Veļas mašīna')" class="h-8 w-8" />
        @lang('Veļas
                    mašīna')
    </li>
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/bone.svg') }}" alt="@lang('Kauls')" class="h-8 w-8" />
        @lang('Atļauts ar
                    mājdzīvniekiem')
    </li>
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/ac.svg') }}" alt="@lang('Gaisa kondicionieris')" class="h-8 w-8" />
        @lang('Gaisa
                    kondicionieris')
    </li>
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/fire.svg') }}" alt="@lang('Uguns')" class="h-8 w-8" />
        @lang('Ugunskura
                    vieta')
    </li>
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/fridge.svg') }}" alt="@lang('Ledusskapis')" class="h-8 w-8" />
        @lang('Ledusskapis')
    </li>
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/camera.svg') }}" alt="@lang('Kamera')" class="h-8 w-8" />
        @lang('Teritorija
                    tiek
                    apsargāta')
    </li>
    <li class="flex items-center gap-x-4">
        <img src="{{ asset('icons/bicycle.svg') }}" alt="@lang('Velosipēds')" class="h-8 w-8" />
        @lang('Velosipēdu
                    novietne')
    </li>
</ul>

<x-btn-primary class="modalBtnOpen">
    @lang('Rādīt visas papildērtības')
    (25)
</x-btn-primary>

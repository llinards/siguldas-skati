<div class="grid-cols-2 sm:grid xl:grid-cols-3">
    <div class="mb-6">
        <ul class="space-y-3">
            <li>
                <h3>@lang('Mājokļa noteikumi')</h3>
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/clock.svg') }}" alt="Pulkstens" class="h-8 w-8" />
                @lang('Check-in pēc plkst. 16:00')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/clock.svg') }}" alt="Pulkstens" class="h-8 w-8" />
                @lang('Checkout līdz plkst. 12:00')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/door-enter.svg') }}" alt="Fire" class="h-8 w-8" />
                @lang('Pašreģistrēšanās (aizslēdzama kastīte)')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/fire.svg') }}" alt="@lang('Uguns')" class="h-8 w-8" />
                @lang('Smēķēt aizliegts')
            </li>
        </ul>
    </div>
    <div class="mb-6">
        <ul>
            <li>
                <h3>@lang('Drošība un īpašums')</h3>
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/washing-machine.svg') }}" alt="@lang('Dūmu detektors')" class="h-8 w-8" />
                @lang('Mājoklis aprīkots ar dūmu detektoru')
            </li>
        </ul>
    </div>
    <div>
        <ul>
            <li>
                <h3>@lang('Atcelšanas politika')</h3>
            </li>
            <li class="text-ss-gray">
                @lang('Rezervācijas atcelšanas gadījumā nauda netiek atgriezta.')
            </li>
        </ul>
    </div>
</div>

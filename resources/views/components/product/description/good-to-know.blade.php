<div class="grid-cols-2 sm:grid xl:grid-cols-3">
    <div class="mb-6">
        <ul class="space-y-3">
            <li>
                <h3>@lang('Mājokļa noteikumi')</h3>
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/clock.svg') }}" alt="Pulkstens" class="h-8 w-8"/>
                @lang('Check-in: 15:00 - 18:00')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/clock.svg') }}" alt="Pulkstens" class="h-8 w-8"/>
                @lang('Check-out: 08:00 - 11:00')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/fire.svg') }}" alt="@lang('Uguns')" class="h-8 w-8"/>
                @lang('Smēķēšana nav atļauta')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/clock.svg') }}" alt="@lang('Pulkstens')" class="h-8 w-8"/>
                @lang('Ballītes/pasākumi nav atļauti')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/clock.svg') }}" alt="@lang('Pulkstens')" class="h-8 w-8"/>
                @lang('Viesiem jāievēro klusums no plkst. 23:00 līdz 07:00')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/bone.svg') }}" alt="@lang('Kauls')" class="h-8 w-8"/>
                @lang('Mājdzīvnieki nav atļauti')
            </li>
        </ul>
    </div>
    <div class="mb-6">
        <ul>
            <li>
                <h3>@lang('Drošība un īpašums')</h3>
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/washing-machine.svg') }}" alt="@lang('Dūmu detektors')" class="h-8 w-8"/>
                @lang('Mājoklis aprīkots ar dūmu detektoru')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/camera.svg') }}" alt="@lang('Dūmu detektors')" class="h-8 w-8"/>
                @lang('Īpašuma drošību nodrošina Mustang apsardze')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/camera.svg') }}" alt="@lang('Dūmu detektors')" class="h-8 w-8"/>
                @lang('Tiek veikta video novērošana')
            </li>
            <li class="flex items-center gap-x-4">
                <img src="{{ asset('icons/camera.svg') }}" alt="@lang('Dūmu detektors')" class="h-8 w-8"/>
                @lang('Īpašumā drīkst uzturēties tikai viesi, kuri ir veikuši rezervāciju')
            </li>
        </ul>
    </div>
    <div>
        <ul>
            <li>
                <h3>@lang('Atcelšanas politika')</h3>
            </li>
            <li class="text-ss-gray">
                @lang('Atcelšanas politika pieejama Booking platformā <a href="https://www.booking.com/hotel/lv/siguldas-skati-sigulda.lv.html#availability" target="blank" class="underline">šeit</a>.')
            </li>
        </ul>
    </div>
</div>

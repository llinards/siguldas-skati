<x-app-layout :title="__('Sākums')">
    <div class="px-2 relative h-dvh bg-center bg-cover will-change-transform z-0 flex justify-center before:absolute before:inset-0 before:bg-black/30"
        style="background-image: url('{{ asset('images/siguldas-skati-houses.jpg') }}')">
        <div class="container mx-auto flex flex-col items-center px-4">
            <h1
                class="text-h-mob xs:text-6xl sm:text-7xl md:text-8xl xl:text-h max-w-7xl font-heading uppercase text-center text-white z-10 absolute top-48 sm:top-1/2 sm:-translate-y-1/2 leading-12 sm:leading-16 md:leading-24 xl:leading-28">
                @lang('Modernas
                brīvdienu
                dizaina
                mājas tavai
                atpūtai!')</h1>

            <x-btn-primary href="#" class="absolute bottom-48 sm:bottom-16 z-10">
                @lang('Uzzināt vairāk')
            </x-btn-primary>
        </div>


    </div>
    <div class="bg-ss">
        <div class="container mx-auto px-4 py-6">
            <div>
                <div class="relative inline-block mb-6">
                    <h2 class="text-h-mob lg:text-h-md leading-18 md:leading-22">@lang('Par mums')</h2>
                    <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
                </div>
                <p class="text-sm text-ss-gray mb-6">
                    @lang('Klusuma greznība Siguldas sirdī!')
                </p>
            </div>
            <div class="lg:grid lg:grid-cols-2 gap-6">
                <img class="h-full lg:h-240 object-cover rounded-3xl mb-6"
                    src="{{ asset('images/siguldas-skati-home-2.jpg') }}"
                    alt="@lang('Siguldas Skati - Moduļu māju parks')">
                <div class="lg:flex lg:flex-col lg:justify-center">
                    <h3 class="mb-6 text-h-sm-mob md:text-h-sm">@lang('Siguldas skati')</h3>
                    <div class="text-base leading-7.5 md:text-2xl space-y-6 md:space-y-10 md:leading-10 text-justify">
                        <p>@lang('Īpaša atpūtas vieta tiem, kuri meklē mieru, klātbūtnes un skaistuma sajūtu pašā
                            Siguldas
                            sirdī.
                            Mūsu
                            stāsts sākas
                            vietā, kur dizains saplūst ar dabu un miers kļūst par lielāko greznību.')</p>
                        <p>
                            @lang('Atrodoties tieši līdzās Panorāmas ratam un Svētku laukumam, esam radījuši modernas
                            brīvdienu
                            dizaina
                            mājas, kas piedāvā
                            ne tikai naktsmītni, bet arī sajūtu pieredzi. Šeit ainava kļūst par interjera daļu, un katrs
                            gadalaiks
                            sniedz jaunu
                            skatpunktu – no miglainiem rudens rītiem līdz sniegotām virsotnēm vai saulainām vasaras
                            dienām.')
                        </p>
                        <p> @lang('Mēs piedāvājam vietu, kur vienkāršība nozīmē kvalitāti, minimālisms – apzinātu
                            komfortu,
                            un
                            katrā
                            detaļā
                            jūtama
                            mīlestība pret vietu, kur dzīvojam. Šis projekts ir mūsu aicinājums atgriezties pie tā, kas
                            būtisks
                            –
                            miera, klātbūtnes
                            un skaistuma.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
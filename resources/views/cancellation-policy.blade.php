<x-app-layout :title="__('Atcelšanas politika')">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mt-26 lg:mt-30 xl:mt-36 mb-3 inline-block">
                <div class="flex sm:inline-block justify-center items-start border-b-2">
                    <x-btn-back class="pb-3 mr-5" />
                    <h1 class="text-h-mob lg:text-h-md leading-none">
                        {{-- prettier-ignore --}}
                        @lang('Atcelšanas politika')
                    </h1>
                </div>
            </div>
            <div class="text-base leading-7.5 md:text-xl md:leading-10 xl:text-2xl">
                <div class="mb-8">
                    <p class="my-6 text-justify">
                        @lang('Šī atcelšanas politika attiecas uz rezervācijām, kas veiktas mūsu mājaslapā. Rezervācijām, kas veiktas Booking.com platformā, ir spēkā Booking.com atcelšanas noteikumi.')
                    </p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('1. Bezmaksas atcelšana')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('1.1. Rezervāciju var atcelt bez maksas ar pilnu naudas atmaksu ne vēlāk kā 7 dienas pirms ierašanās dienas.')</p>
                    <p class="mb-2">@lang('1.2. Precīzs bezmaksas atcelšanas termiņš ir norādīts rezervācijas apstiprinājuma e-pastā.')</p>
                    <p class="mb-2">@lang('1.3. Atcelšanu var veikt tiešsaistē, izmantojot rezervācijas pārvaldības saiti, kas nosūtīta apstiprinājuma e-pastā.')</p>
                    <p class="mb-2">@lang('1.4. Bezmaksas atcelšanas gadījumā tiek atmaksāta visa samaksātā summa.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('2. Citi atcelšanas un neierašanās gadījumi')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('2.1. Ja līdz ierašanās dienai ir atlikušas mazāk nekā 7 dienas, atcelšana tiešsaistē vairs nav pieejama un samaksātā summa netiek atmaksāta.')</p>
                    <p class="mb-2">@lang('2.2. Neierašanās gadījumā samaksātā summa netiek atmaksāta.')</p>
                    <p class="mb-2">@lang('2.3. Ja rezervāciju nepieciešams atcelt neparedzētu apstākļu dēļ (piemēram, slimība, nelaimes gadījums vai citi ārkārtas apstākļi), lūdzam sazināties ar mums — katru situāciju izskatīsim individuāli.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('3. Datumu maiņa')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('3.1. Ja vēlaties mainīt rezervācijas datumus, lūdzam sazināties ar mums. Datumu maiņa iespējama atkarībā no pieejamības.')</p>
                    <p class="mb-2">@lang('3.2. Ja jaunajiem datumiem ir atšķirīga cena, starpība tiek attiecīgi piemaksāta vai atmaksāta.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('4. Atmaksas kārtība')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('4.1. Atmaksa tiek veikta uz to pašu maksājuma metodi, ar kuru tika apmaksāta rezervācija.')</p>
                    <p class="mb-2">@lang('4.2. Atmaksa parasti tiek saņemta 5–10 darba dienu laikā atkarībā no bankas.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('5. Saimnieku veikta atcelšana')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('5.1. Ja mēs nevaram nodrošināt uzturēšanos neparedzētu apstākļu dēļ, piedāvāsim citus datumus vai atmaksāsim visu samaksāto summu.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('6. Booking.com rezervācijas')
                </h2>
                <div class="mb-6 text-justify">
                    <p class="mb-2">@lang('6.1. Rezervācijām, kas veiktas Booking.com platformā, ir spēkā rezervācijas brīdī izvēlētie Booking.com atcelšanas noteikumi. Šīs rezervācijas var pārvaldīt un atcelt Booking.com sistēmā.')</p>
                </div>

                <h2 class="text-h-sm-mob lg:text-h-sm mt-8 mb-4 leading-none">
                    @lang('7. Saziņa ar mums')
                </h2>
                <div class="text-justify">
                    <p class="mb-2">@lang('Ja radušies jautājumi par rezervācijas atcelšanu vai atmaksu, lūdzam sazināties ar mums:')</p>
                    <p class="mb-2">
                        <x-link href="tel:+37125666622">+371 25666622</x-link>
                    </p>
                    <p>
                        <x-link href="mailto:siguldasskati@gmail.com">siguldasskati@gmail.com</x-link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

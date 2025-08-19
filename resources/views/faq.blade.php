<x-app-layout :title="__('Biežāk uzdotie jautājumi')">
    <div class="bg-ss">
        <div class="container mx-auto px-4">
            <div class="relative mt-26 lg:mt-30 xl:mt-36 mb-3 inline-block">
                <div class="flex sm:inline-block justify-center items-start border-b-2">
                    <x-btn-back class="pb-3 mr-5"/>
                    <h1 class="text-h-mob lg:text-h-md leading-none">
                        {{-- prettier-ignore --}}
                        @lang('Biežāk uzdotie jautājumi')
                    </h1>
                </div>
            </div>
            <p class="text-ss-gray mb-12 text-sm leading-none">
                {{-- prettier-ignore --}}
                @lang('Šeit ir atbildes uz biežāk uzdotajiem jautājumiem par mūsu brīvdienu dizaina mājām.')
            </p>

            <div class="hs-accordion-group container mx-auto space-y-6" data-hs-accordion-always-open="">
                {{-- Question 1 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-one">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-one">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Kur atrodas Siguldas Skati?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-one"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-one">
                        <p class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            @lang('Mūsu brīvdienu mājas atrodas Siguldas centrā, Cēsu ielā 17, blakus Siguldas Panorāmas ratam un Svētku laukumam.')
                        </p>
                    </div>
                </div>

                {{-- Question 2 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-two">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-two">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Vai pie brīvdienu mājām ir pieejama autostāvvieta?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-two"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-two">
                        <p class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            @lang('Jā, viesiem ir pieejama bezmaksas privātā autostāvvieta pie naktsmītnes.')
                        </p>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-three">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-three">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Vai varu ierasties ar bērniem?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-three"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-three">
                        <p class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            @lang('Jā, naktsmītnes ir piemērota ģimenēm. "Black" mājā ir atsevišķa bērnistaba, un tajā ērti var uzturēties divi pieaugušie un līdz diviem bērniem.')
                        </p>
                    </div>
                </div>

                {{-- Question 4 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-four">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-four">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Kādi ir reģistrēšanās un izrakstīšanās laiki?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-four"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-four">
                        <div class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            <p class="mb-2">
                                @lang('Reģistrēšanās laiks: no plkst. 15:00 - 18:00')</p>
                            {{-- prettier-ignore --}}
                            <p>@lang('Izrakstīšanās laiks: līdz plkst. 8:00 - 11:00')</p>
                        </div>
                    </div>
                </div>

                {{-- Question 5 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-five">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-five">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Vai ir pieejams bezvadu internets?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-five"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-five">
                        <p class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            @lang('Jā, visās telpās ir pieejams bezmaksas Wi-Fi.')
                        </p>
                    </div>
                </div>

                {{-- Question 6 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-six">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-six">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Vai brīvdienu mājās ir pieejama virtuve?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-six"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-six">
                        <p class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            @lang('Jā, visās brīvdienu mājās ir pilnībā aprīkota virtuve ar plīts virsmu, ledusskapi, trauku mazgājamo mašīnu, kafijas aparātu un visu nepieciešamo maltīšu pagatavošanai.')
                        </p>
                    </div>
                </div>

                {{-- Question 7 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-seven">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-seven">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Vai atļauti ir mājdzīvnieki?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-seven"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-seven">
                        <p class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            @lang('Ar mājdzīvniekiem pašlaik atpūta mūsu dizaina mājās nav atļauta.')
                        </p>
                    </div>
                </div>

                {{-- Question 8 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-eight">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-eight">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Kā notiek rezervācija?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-eight"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-eight">
                        <p class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            @lang('Rezervāciju iespējams ir veikt tiešsaistē caur rezervēšanas platformu booking.com')
                        </p>
                    </div>
                </div>

                {{-- Question 9 --}}
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-nine">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-nine">
                        <h3 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            {{-- prettier-ignore --}}
                            @lang('Vai sauna un džakūzī ir iekļauts cenā?')
                        </h3>
                        <x-accordion-arrows/>
                    </button>
                    <div id="hs-basic-with-arrow-collapse-nine"
                         class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                         role="region" aria-labelledby="hs-basic-with-arrow-heading-nine">
                        <p class="text-gray-700 pb-4">
                            {{-- prettier-ignore --}}
                            @lang('Nē, sauna un džakuzi nav iekļauti naktsmītnes cenā – tie ir pieejami par papildu samaksu, iepriekš vienojoties par rezervāciju.')
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

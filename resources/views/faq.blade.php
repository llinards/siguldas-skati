<x-app-layout :title="__('Biežāk uzdotie jautājumi')">
    <div class="bg-ss">
        <div class="bg-ss container mx-auto px-4">
            <div class="relative mt-36 mb-3 inline-block">
                <h1 class="text-h-mob lg:text-h-md leading-none border-b-2">
                    @lang('Biežāk uzdotie jautājumi')
                </h1>
            </div>
            <p class="text-ss-gray mb-12 text-sm leading-none">
                @lang('Šeit ir atbildes uz biežāk uzdotajiem jautājumiem par mūsu dizaina mājām un saunām.')
            </p>

            <div class="hs-accordion-group container mx-auto space-y-6" data-hs-accordion-always-open="">
                <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-one">
                    <button
                        class="hs-accordion-toggle relative flex w-full cursor-pointer items-center justify-between border-b-2 pt-3"
                        aria-expanded="true" aria-controls="hs-basic-with-arrow-collapse-one">
                        <h2 class="text-h-sm-mob lg:text-h-mob text-left leading-none">
                            @lang('Ērtības un
                            aprīkojums')
                        </h2>
                        <x-accordion-arrows />
                    </button>
                    <div id="hs-basic-with-arrow-collapse-one"
                        class="hs-accordion-content mt-3 hidden w-full overflow-hidden transition-[height] duration-300"
                        role="region" aria-labelledby="hs-basic-with-arrow-heading-one">
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat nobis dolor accusantium
                            reiciendis. Aperiam nulla, deserunt pariatur minus sint iusto, mollitia optio modi id fuga
                            eaque molestiae quia voluptatibus itaque. Voluptatibus, illum. Quibusdam perspiciatis,
                            aliquid ipsa sed itaque nesciunt alias ex adipisci quis quisquam autem numquam facilis
                            quaerat deserunt non suscipit, commodi laborum eius sunt? Eius deleniti minus praesentium
                            voluptatibus? Veritatis, rerum! Cumque amet nam ipsa id excepturi accusantium itaque culpa
                            totam exercitationem ea quod consequuntur accusamus impedit obcaecati inventore, ullam est
                            veritatis perspiciatis debitis pariatur quis sit doloribus quibusdam?š
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
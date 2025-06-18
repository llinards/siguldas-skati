<x-app-layout :title="__('Biežāk uzdotie jautājumi')">
    <div class="bg-ss">
        <div class="container mx-auto px-4 bg-ss">
            <div class="relative inline-block mb-3 mt-36">
                <h1 class="text-h-mob lg:text-h-md leading-none">@lang('Biežāk uzdotie jautājumi')</h1>
                <span class="absolute left-0 bottom-0 w-2/3 h-0.5 bg-ss-dark"></span>
            </div>
            <p class="text-sm text-ss-gray mb-12 leading-none">
                @lang('Šeit ir atbildes uz biežāk uzdotajiem jautājumiem par mūsu dizaina mājām un saunām.')
            </p>

        <div class="hs-accordion-group space-y-6 container max-w-6xl mx-auto" data-hs-accordion-always-open="">
            <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-one">
                <button class="hs-accordion-toggle border-b-2 pt-3 relative flex items-center w-full justify-between cursor-pointer"
                    aria-expanded="true" aria-controls="hs-basic-with-arrow-collapse-one">

                    <h2 class="text-left leading-none text-h-sm-mob lg:text-h-mob">@lang('Ērtības un
                        aprīkojums')</h2>
                    <x-accordion-arrows />

                </button>
                <div id="hs-basic-with-arrow-collapse-one" class="mt-3 hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300"
                    role="region" aria-labelledby="hs-basic-with-arrow-heading-one">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat nobis dolor accusantium reiciendis. Aperiam nulla, deserunt pariatur minus sint iusto, mollitia optio modi id fuga eaque molestiae quia voluptatibus itaque.
                    Voluptatibus, illum. Quibusdam perspiciatis, aliquid ipsa sed itaque nesciunt alias ex adipisci quis quisquam autem numquam facilis quaerat deserunt non suscipit, commodi laborum eius sunt? Eius deleniti minus praesentium voluptatibus?
                    Veritatis, rerum! Cumque amet nam ipsa id excepturi accusantium itaque culpa totam exercitationem ea quod consequuntur accusamus impedit obcaecati inventore, ullam est veritatis perspiciatis debitis pariatur quis sit doloribus quibusdam?š
                    </p>
                    </div>
            </div>

            <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-two">
                <button class="hs-accordion-toggle border-b-2 pt-3 relative flex items-center w-full justify-between cursor-pointer"
                    aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-two">
                    <h2 class="text-left leading-none text-h-sm-mob lg:text-h-mob">@lang('Cenas un papildu
                        informācija')</h2>
                    <x-accordion-arrows />
                </button>
                <div id="hs-basic-with-arrow-collapse-two"
                    class="mt-3 hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region"
                    aria-labelledby="hs-basic-with-arrow-heading-two">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur ipsa suscipit alias tempora mollitia distinctio aliquid necessitatibus aliquam voluptas nulla dicta placeat repellat, accusantium voluptates molestiae consequatur eum provident! Rem.
                    Modi aliquid sequi consectetur velit placeat optio eligendi voluptate illo doloremque ut aut quo sed, adipisci est earum harum praesentium quis fugit excepturi quos et necessitatibus! Illo totam harum provident!</p>
                </div>
            </div>

            <div class="hs-accordion w-full" id="hs-basic-with-arrow-heading-three">
                <button class="hs-accordion-toggle border-b-2 pt-3 relative flex items-center w-full justify-between cursor-pointer"
                    aria-expanded="false" aria-controls="hs-basic-with-arrow-collapse-three">
                    <h2 class="text-left leading-none text-h-sm-mob lg:text-h-mob">@lang('Lietas, ko ņemt vērā')</h2>
                    <x-accordion-arrows />
                </button>
                <div id="hs-basic-with-arrow-collapse-three"
                    class="mt-3 hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region"
                    aria-labelledby="hs-basic-with-arrow-heading-three">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem labore laudantium veritatis non id beatae ipsum, commodi cupiditate, incidunt nam blanditiis consectetur sequi molestiae delectus qui, fuga sed? Neque, quis?
                    Quas illo nesciunt error iure nulla officiis vero voluptatum reprehenderit quidem. Unde, quas fugit cum temporibus eius illo esse quia vitae rem dolore sit voluptatem accusamus iure a neque minus.
                    Vel quidem inventore incidunt, earum vero dolore nemo distinctio beatae optio, eum reprehenderit. Ullam, quis harum dignissimos quisquam non dicta. Atque unde molestias error saepe sapiente ipsum maiores quidem facilis.
                    Impedit delectus magnam corrupti laudantium similique necessitatibus inventore, perferendis sunt voluptatum? Nulla, vel enim, dolorum corrupti ea, necessitatibus cupiditate labore ratione nobis magni minima numquam? Voluptas expedita praesentium vero doloremque.</p>
                </div>
            </div>
        </div>


        </div>
    </div>
</x-app-layout>
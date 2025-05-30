<x-layouts.app>
    <div class="px-2 relative h-screen sm:h-220 w-full bg-center bg-cover z-0 flex justify-center before:absolute before:inset-0 before:bg-black/30"
        style="background-image: url('{{ asset('images/siguldas-skati-houses.jpg') }}')">

        <h1
            class="text-h-mob sm:text-7xl md:text-8xl xl:text-h font-heading uppercase text-center text-white z-10 absolute top-32 sm:top-1/2 sm:-translate-y-1/2 leading-12 sm:leading-20 px-4">
            @lang('Modernas
            brīvdienu
            dizaina
            mājas tavai
            atpūtai!')</h1>

        <x-btn-primary href="#" class="absolute bottom-16 z-10">
            @lang('Uzzināt vairāk')
        </x-btn-primary>
    </div>
</x-layouts.app>
<x-app-layout :title="__('Sākums')">
    <div
        class="px-2 relative h-dvh bg-center bg-cover will-change-transform z-0 flex justify-center before:absolute before:inset-0 before:bg-black/30"
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
    <div class="h-120 bg-ss">

    </div>
</x-app-layout>

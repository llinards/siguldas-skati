<div class="bg-cover bg-center bg-no-repeat h-173 lg:h-271 home-banner"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('{{ $bannerImage ?? '' }}'"
    alt="{{ $bannerImageAlt ?? '' }}">
    <div class="container mx-auto h-full flex flex-col items-center justify-center text-center px-4">
        <h2
            class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-h-banner max-w-7xl font-heading uppercase text-white leading-12 sm:leading-16 md:leading-24 xl:leading-28">
            {{ $bannerText ?? '' }}
        </h2>
        <h2
            class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-h-banner max-w-7xl font-heading uppercase text-white leading-12 sm:leading-16 md:leading-24 xl:leading-28 mt-6 sm:mt-8 md:mt-10 lg:mt-16">
            {{ $secondaryBannerText ?? '' }}
        </h2>
    </div>
</div>
<div class="home-banner h-173 bg-cover bg-center bg-no-repeat lg:h-271"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('{{ $bannerBackgroundImage ?? '' }}')"
    alt="{{ $bannerBackgroundImageAlt ?? '' }}">
    <div class="container mx-auto flex h-full flex-col items-center justify-center px-4 text-center">

        @if(!empty($bannerImage))
        <img class="mb-12 max-h-60" src="{{ $bannerImage ?? '' }}" alt="{{ $bannerImageAlt ?? '' }}">
        @endif

        @if(!empty($bannerText))
        <h2
            class="xl:text-h-banner font-heading max-w-7xl text-4xl leading-12 text-white uppercase sm:text-5xl sm:leading-16 md:text-6xl md:leading-24 lg:text-7xl xl:leading-28">
            {{ $bannerText }}
        </h2>
        @endif

        @if(!empty($secondaryBannerText))
        <h2
            class="xl:text-h-banner font-heading mt-6 max-w-7xl text-4xl leading-12 text-white uppercase sm:mt-8 sm:text-5xl sm:leading-16 md:mt-10 md:text-6xl md:leading-24 lg:mt-16 lg:text-7xl xl:leading-28">
            {{ $secondaryBannerText }}
        </h2>
        @endif

    </div>
</div>
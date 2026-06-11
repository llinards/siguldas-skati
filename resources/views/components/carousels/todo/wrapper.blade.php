@props(['activities'])

<div class="bg-ss">
    <div class="carousel-section container mx-auto grid grid-cols-1">
        <x-carousels.nav class="xl:hidden">
            <x-slot name="prev">todoPrev</x-slot>
            <x-slot name="next">todoNext</x-slot>
        </x-carousels.nav>
        <div id="todoCarousel" class="f-carousel">
            <div class="f-carousel__viewport">
                @foreach ($activities as $activity)
                    <x-carousels.todo.card>
                        <x-slot name="todoTitle">{{ $activity->title }}</x-slot>
                        <x-slot name="todoImage">{{ $activity->image ? Storage::url($activity->image) : '' }}</x-slot>
                        <x-slot name="modalId">activity-{{ $activity->id }}</x-slot>
                    </x-carousels.todo.card>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script type="module">
    const carousel = new Carousel(document.getElementById('todoCarousel'), {
        Navigation: false,
        infinite: false,
        center: true,
        transition: 'fade',
        Dots: false,
        Autoplay: false,
    });

    document.getElementById('todoPrev').addEventListener('click', () => carousel.slidePrev());
    document.getElementById('todoNext').addEventListener('click', () => carousel.slideNext());

    const prevBtn = document.getElementById('todoPrev');
    const nextBtn = document.getElementById('todoNext');

    function updateToDoCarouselNav() {
        prevBtn.disabled = carousel.page === 0;
        nextBtn.disabled = carousel.page === carousel.pages.length - 1;
    }

    updateToDoCarouselNav();

    carousel.on('change', updateToDoCarouselNav);
</script>
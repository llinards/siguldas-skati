<div class="bg-ss">
    <div class="container mx-auto carousel-section grid grid-cols-1">
        <x-carousels.nav class="xl:hidden">
            <x-slot name="prev">todoPrev</x-slot>
            <x-slot name="next">todoNext</x-slot>
        </x-carousels.nav>
        <div id="todoCarousel" class="f-carousel">
            <div class="f-carousel__viewport">
                <x-carousels.todo.card>
                    <x-slot name="todoTitle">@lang('Dabas takas')</x-slot>
                    <x-slot name="todoImage">{{ asset('images/siguldas-skati-todo-1.jpg') }}</x-slot>
                    <x-slot name="todoLink">#</x-slot>
                </x-carousels.todo.card>
                <x-carousels.todo.card>
                    <x-slot name="todoTitle">@lang('Aktīvā atpūta')</x-slot>
                    <x-slot name="todoImage">{{ asset('images/siguldas-skati-todo-2.jpg') }}</x-slot>
                    <x-slot name="todoLink">#</x-slot>
                </x-carousels.todo.card>
                <x-carousels.todo.card>
                    <x-slot name="todoTitle">@lang('Garšu pieredze')</x-slot>
                    <x-slot name="todoImage">{{ asset('images/siguldas-skati-todo-3.jpg') }}</x-slot>
                    <x-slot name="todoLink">#</x-slot>
                </x-carousels.todo.card>
                <x-carousels.todo.card>
                    <x-slot name="todoTitle">@lang('Kultūra un vēsture')</x-slot>
                    <x-slot name="todoImage">{{ asset('images/siguldas-skati-todo-4.jpg') }}</x-slot>
                    <x-slot name="todoLink">#</x-slot>
                </x-carousels.todo.card>
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
    Dots: false
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
<div class="hidden lg:grid grid-cols-2 2xl:grid-cols-5 gap-4 mb-6">
    @if(!$product->images->isEmpty())
        @php $firstImage = $product->images->first() @endphp
        <div class="2xl:col-span-3 overflow-hidden rounded-3xl h-full">
            <a data-fancybox="galleryDesktop" href="{{ Storage::url($firstImage->filename) }}"
               class="block h-full">
                <img
                    class="rounded-3xl object-cover w-full h-148 transition-transform duration-300 ease-in-out hover:scale-105"
                    alt="" src="{{ Storage::url($firstImage->filename) }}">
            </a>
        </div>
        <div class="2xl:col-span-2 grid grid-cols-2 grid-rows-2 gap-4 h-full">
            @foreach($product->images->skip(1) as $image)
                <div class="overflow-hidden rounded-3xl h-full">
                    <a data-fancybox="galleryDesktop" href="{{ Storage::url($image->filename) }}"
                       class="block h-full">
                        <img
                            class="rounded-3xl object-cover w-full h-72 transition-transform duration-300 ease-in-out hover:scale-105"
                            alt="" src="{{ Storage::url($image->filename) }}">
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script type="module">
    Fancybox.bind('[data-fancybox="galleryDesktop"]', {
        compact: false,
        idle: false,

        animated: false,
        showClass: false,
        hideClass: false,

        dragToClose: false,
        contentClick: false,

        Images: {},

        Toolbar: {
            display: {
                left: [],
                middle: ['infobar'],
                right: ['close'],
            },
        },

        Thumbs: {
            type: 'classic',
        },
    });

</script>

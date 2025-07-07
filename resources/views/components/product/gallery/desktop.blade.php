<div class="mb-6 hidden grid-cols-2 gap-4 lg:grid 2xl:grid-cols-5">
    @if (! $product->images->isEmpty())
        @php
            $firstImage = $product->images->first();
            $otherImages = $product->images->skip(1);
        @endphp

        <div class="h-full overflow-hidden rounded-3xl 2xl:col-span-3">
            <a data-fancybox="galleryDesktop" href="{{ Storage::url($firstImage->filename) }}" class="block h-full">
                <img
                    class="h-148 w-full rounded-3xl object-cover transition-transform duration-300 ease-in-out hover:scale-105"
                    alt=""
                    src="{{ Storage::url($firstImage->filename) }}"
                />
            </a>
        </div>

        <div class="grid h-full grid-cols-2 grid-rows-2 gap-4 2xl:col-span-2">
            @foreach ($otherImages as $index => $image)
                <div class="{{ $index < 5 ? '' : 'hidden' }} h-full overflow-hidden rounded-3xl">
                    <a data-fancybox="galleryDesktop" href="{{ Storage::url($image->filename) }}" class="block h-full">
                        <img
                            class="h-72 w-full rounded-3xl object-cover transition-transform duration-300 ease-in-out hover:scale-105"
                            alt=""
                            src="{{ Storage::url($image->filename) }}"
                        />
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

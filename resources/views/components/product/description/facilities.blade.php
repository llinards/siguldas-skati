@if (! $product->features->isEmpty())
<ul class="xs:grid mb-3 grid-cols-2 grid-rows-5 space-y-3">
    @foreach ($product->features->take(10) as $feature)
    <li class="flex items-center gap-x-4">
        <img src="{{ Storage::url($feature->icon_image) }}" alt="{{ $feature->title }}" class="h-8 w-8" />
        {{ $feature->title }}
    </li>
    @endforeach
</ul>

{{-- <x-btn-primary aria-haspopup="dialog" aria-expanded="false" aria-controls="modal" data-hs-overlay="#modal"
    class="modalBtnOpen">
    @lang('Rādīt visas papildērtības')
    ({{ count($product->features) }})
</x-btn-primary> --}}

<div x-data="{}">
    <x-btn-primary aria-haspopup="dialog" aria-expanded="false" aria-controls="modal" class="modalBtnOpen"
        x-on:click="$dispatch('open-modal', 'example-modal')">
        @lang('Rādīt visas papildērtības')
        ({{ count($product->features) }})
    </x-btn-primary>
</div>

@endif
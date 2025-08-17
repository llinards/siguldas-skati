@if (! $product->features->isEmpty())
    <ul class="xs:grid mb-3 grid-cols-2 grid-rows-5 space-y-3">
        @foreach ($product->features as $feature)
            @if($feature->is_active)
                <li class="flex items-center gap-x-4">
                    <img src="{{ Storage::url($feature->icon_image) }}" alt="{{ $feature->title }}" class="h-8 w-8"/>
                    {{ $feature->title }}
                </li>
            @endif
        @endforeach
    </ul>
@endif
@if($product->show_all_features)
    <x-btn-primary aria-haspopup="dialog" aria-expanded="false" aria-controls="modal" data-hs-overlay="#product-modal">
        @lang('Rādīt visas papildērtības')
    </x-btn-primary>
@endif

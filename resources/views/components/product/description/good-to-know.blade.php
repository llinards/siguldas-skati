<div class="grid-cols-2 sm:grid xl:grid-cols-3">
    @php
        $hasHouseRules = $product->rules->contains(function($rule) {
            return $rule->section === 'house' && $rule->is_active;
        });
        $hasSafetyRules = $product->rules->contains(function($rule) {
            return $rule->section === 'safety' && $rule->is_active;
        });
        $hasProhibitedRules = $product->rules->contains(function($rule) {
            return $rule->section === 'prohibited' && $rule->is_active;
        });
    @endphp

    @if($hasHouseRules)
        <div class="mb-6">
            <ul class="space-y-3">
                <li>
                    <h3>@lang('Mājokļa noteikumi')</h3>
                </li>
                @foreach ($product->rules as $rule)
                    @if(($rule->section === 'house') && ($rule->is_active) )
                        <li class="flex items-center gap-x-4">
                            <img src="{{ Storage::url($rule->icon_image) }}" alt="{{ $rule->title }}"
                                 class="h-8 w-8"/>
                            {{ $rule->title }}
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
    @if($hasProhibitedRules)
        <div class="mb-6">
            <ul class="space-y-3">
                <li>
                    <h3>@lang('Aizliegts')</h3>
                </li>
                @foreach ($product->rules as $rule)
                    @if(($rule->section === 'prohibited') && ($rule->is_active) )
                        <li class="flex items-center gap-x-4">
                            <img src="{{ Storage::url($rule->icon_image) }}" alt="{{ $rule->title }}"
                                 class="h-8 w-8"/>
                            {{ $rule->title }}
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
    @if($hasSafetyRules)
        <div class="mb-6">
            <ul class="space-y-3">
                <li>
                    <h3>@lang('Drošība un īpašums')</h3>
                </li>
                @foreach ($product->rules as $rule)
                    @if(($rule->section === 'safety') && ($rule->is_active) )
                        <li class="flex items-center gap-x-4">
                            <img src="{{ Storage::url($rule->icon_image) }}" alt="{{ $rule->title }}"
                                 class="h-8 w-8"/>
                            {{ $rule->title }}
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
    <div class="xl:mb-6">
        <ul>
            <li>
                <h3>@lang('Atcelšanas politika')</h3>
            </li>
            <li class="text-ss-gray">
                @lang('Atcelšanas politika pieejama Booking platformā <a href="https://www.booking.com/hotel/lv/siguldas-skati-sigulda.lv.html#availability" target="blank" class="underline">šeit</a>.')
            </li>
        </ul>
    </div>
</div>

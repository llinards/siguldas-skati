<form action="{!! $url !!}" {!! $attributes !!}>
    @csrf
    <x-btn-cookies type="submit" class="">
        <span class="{!! $basename !!}__label ">{{ $label }}</span>
    </x-btn-cookies>
</form>

@props(['url', 'variant' => 'primary'])
<a href="{{ $url }}" class="btn{{ $variant === 'secondary' ? ' btn-secondary' : '' }}">{{ $slot }}</a>

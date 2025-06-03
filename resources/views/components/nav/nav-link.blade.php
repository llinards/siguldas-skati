{{-- @props(['active'])

@php
$classes = ($active ?? false)
? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900
focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
: 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500
hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition
duration-150 ease-in-out';
@endphp --}}


<li>
    <a {{ $attributes->merge([ 'class' => 'mx-4 border-1 text-center border-transparent hover:border-b-menu-hover
        hover:text-menu-hover lg:hover:border-b-white lg:hover:text-white transition-all ease-in-out duration-200
        text-btn-md md:text-btn-md lg:text-btn'])}}>
        {{ $slot }}
    </a>
</li>
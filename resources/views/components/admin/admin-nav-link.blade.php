{{--
    @props(['active'])
    
    @php
    $classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900
    focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500
    hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition
    duration-150 ease-in-out';
    @endphp
--}}

<li
    class="hover:border-b-menu-hover hover:text-menu-hover text-btn-md sm:text-btn-sm lg:text-btn mx-4 border-1 border-transparent text-center transition-all duration-200 ease-in-out md:hover:border-b-white md:hover:text-white"
>
    <a {{ $attributes }}>
        {{ $slot }}
    </a>
</li>

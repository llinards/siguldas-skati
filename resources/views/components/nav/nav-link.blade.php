<li>
    @props(['active' => false])
    <a {{$attributes->merge(['class' => ($active ? ' border-b-1' : '' ) . 'mx-4 border-1 text-center border-transparent
        hover:border-b-menu-hover
        hover:text-menu-hover
        lg:hover:border-b-white lg:hover:text-white transition-all ease-in-out duration-200 text-btn-md md:text-btn-md
        lg:text-btn']) }}
        >
        {{ $slot }}
    </a>
</li>
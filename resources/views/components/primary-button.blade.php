<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-2 border uppercase bg-ss-dark rounded-xl text-white transition-all duration-200
text-center hover:bg-white hover:text-black']) }}>
    {{ $slot }}
</button>

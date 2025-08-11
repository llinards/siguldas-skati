<a href="{{ route('home') }}" id="backBtn" aria-label="Previous page" {{ $attributes->merge([
    'class' => 'inline-block px-3 py-2 mb-3 lg:mb-6 uppercase bg-ss-dark rounded-xl text-white text-center
    hover:bg-white
    hover:text-black cursor-pointer hover:border-ss-dark border-1 border-transparent transition-all duration-300'
    ]) }}>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-7">
        <path fill-rule="evenodd"
            d="M9.78 4.22a.75.75 0 0 1 0 1.06L7.06 8l2.72 2.72a.75.75 0 1 1-1.06 1.06L5.47 8.53a.75.75 0 0 1 0-1.06l3.25-3.25a.75.75 0 0 1 1.06 0Z"
            clip-rule="evenodd" />
    </svg>
</a>

<script type="module">
    const backBtn = document.getElementById('backBtn')
    backBtn.addEventListener('click', goBack)
    function goBack() {
        const prev = document.referrer;
        if (prev && prev.startsWith(window.location.origin)) {
            history.back();
            return false;
        }
    }
</script>
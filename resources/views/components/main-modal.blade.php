<div id="{{ $modalId }}"
     class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none items-center justify-center"
     role="dialog" tabindex="-1" aria-labelledby="modal-label">
    <div
        class="hs-overlay-animation-target hs-overlay-open:scale-100 hs-overlay-open:opacity-100 scale-95 opacity-0 ease-in-out transition-all duration-200 flex justify-center items-center p-4 min-h-dvh sm:max-w-lg sm:w-full sm:mx-auto">
        <div
            class="w-full flex flex-col bg-white max-h-[90svh] border border-ss-dark shadow-2xs rounded-xl pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-ss-dark">
                <h3 id="modal-label" class="text-h-sm-mob lg:text-h-mob leading-none mt-2">
                    {{ $modalHeading }}
                </h3>
                <button type="button" aria-label="Close" data-hs-overlay="#{{ $modalId }}">
                    <span type="button"
                          class="z-90 bg-ss-dark hover:bg-white inline-flex size-8 items-center justify-center gap-x-2 rounded-full border border-transparent hover:border-ss-dark transition-all duration-200 cursor-pointer group"
                          aria-label="Close">
                        <span class="sr-only">@lang('Aizvērt')</span>
                        <svg class="size-4 shrink-0 stroke-white group-hover:stroke-ss-dark group-hover:duration-200"
                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="overflow-y-auto p-4">
                {{ $modalContent }}
            </div>
            @if(isset($modalCTA))
                <div class="flex items-center gap-x-2 py-3 px-4 border-t border-ss-dark">
                    <x-btn-primary type="button">
                        {{ $modalCTA }}
                    </x-btn-primary>
                </div>
            @endif
        </div>
    </div>
</div>


<script type="module">
    // Get modal and trigger elements
    const modal = document.getElementById('{{ $modalId }}');

    // Function to disable body scroll
    function disableBodyScroll() {
        const scrollY = window.scrollY;
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%';
    }

    // Function to enable body scroll
    function enableBodyScroll() {
        const scrollY = document.body.style.top;
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        window.scrollTo({
            top: parseInt(scrollY || '0') * -1,
            behavior: 'instant'
        })
    }

    // Listen for Preline UI modal events
    modal.addEventListener('open.hs.overlay', disableBodyScroll);
    modal.addEventListener('close.hs.overlay', enableBodyScroll);
</script>

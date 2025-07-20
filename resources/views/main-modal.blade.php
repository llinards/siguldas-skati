<div id="modal"
    class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
    role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-modal-label">
    <div
        class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center justify-center">
        <div
            class="bg-ss flex max-h-[90vh] flex-col overflow-hidden rounded-xl border shadow-2xs w-xs sm:w-lg md:w-xl xl:w-2xl">
            <div class="border-ss-dark flex items-center justify-between border-b px-4 py-3">
                <h3 id="modal-label" class="text-h-sm-mob lg:text-h-mob leading-none mt-2">
                    {{ $modalHeading }}
                </h3>
                <span id="modalBtnClose" type="button"
                    class="bg-ss-dark hover:bg-white inline-flex size-8 items-center justify-center gap-x-2 rounded-full border border-transparent hover:border-ss-dark transition-all duration-200 cursor-pointer group"
                    aria-label="Close" tabindex="2">
                    <span class="sr-only">Close</span>
                    <svg class="size-4 shrink-0 stroke-white group-hover:stroke-ss-dark group-hover:duration-200"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </span>
            </div>

            <div class="overflow-y-auto p-4">
                {{ $modalContent }}
            </div>

            <div class="border-ss-gray gap-x-2 border-t px-4 py-3">
                <x-btn-primary tabindex="3">@lang('Rezervēt')</x-btn-primary>
            </div>
        </div>
    </div>
</div>

<script type="module">
    // Get modal and trigger elements
        const modal = document.getElementById('modal');
        const modalCloseBtn = document.getElementById('modalBtnClose');

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

        // Also handle manual close button
        modalCloseBtn.addEventListener('click', () => {
        setTimeout(enableBodyScroll, 100);
        });
</script>
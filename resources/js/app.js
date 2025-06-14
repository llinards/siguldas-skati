import './bootstrap';

import Alpine from 'alpinejs';
import 'preline'
import { HSOverlay } from 'preline';
import { Fancybox } from "@fancyapps/ui";

import "@fancyapps/ui/dist/fancybox/fancybox.css";
import "@fancyapps/ui/dist/carousel/carousel.css";
import { Carousel } from "@fancyapps/ui/dist/carousel/carousel.esm.js";


window.Carousel = Carousel;
window.Alpine = Alpine;
window.Fancybox = Fancybox;



function handleMobileMenu() {
    return {
        open: false,
        init() {
            this.$watch('open', value => {
                document.body.style.overflow = value ? 'hidden' : 'auto';
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.open = false;
                    document.body.style.overflow = 'auto';
                }
            });
        }
    }
}

window.Alpine = Alpine;
window.handleMobileMenu = handleMobileMenu;

document.querySelectorAll('[id^="gallery-main-"]').forEach(el => {
    el.addEventListener('click', function (e) {
        e.preventDefault();

        const images = [
            { src: el.getAttribute('href') }
        ];

        const extra = el.getAttribute('data-gallery-extra');
        if (extra) {
            try {
                const extraImages = JSON.parse(extra);
                extraImages.forEach(url => images.push({ src: url }));
            } catch (err) {
                console.error('Invalid gallery extra images:', err);
            }
        }

        Fancybox.show(images, {
            groupAll: false,
        });
    });
});

const accordions = document.getElementsByClassName('hs-accordion');
for (let item of accordions) {
    item.addEventListener('click', function () {
        item.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}
const productModalBtnOpen = document.querySelectorAll('.modalBtnOpen');
const productModalBtnClose = document.getElementById('modalBtnClose');
const productModal = document.getElementById('modal');
const productModalContainer = document.getElementById('modalContainer');

function closeModal() {
    productModalContainer.classList.remove('opacity-100');
    productModalContainer.classList.add('opacity-0');
    productModalContainer.addEventListener('transitionend', function handler() {
        productModal.close();
        productModalContainer.removeEventListener('transitionend', handler);
    });
}

if (productModal) {
    productModalBtnOpen.forEach(btn => {
        btn.addEventListener('click', () => {
            productModal.showModal();
            productModalContainer.classList.add('opacity-100');
            productModalContainer.classList.remove('opacity-0');
        });
    });

    productModalBtnClose.addEventListener('click', () => {
        closeModal();
    });

    // Optional: close when clicking outside
    productModalContainer.addEventListener('click', function (e) {
        if (e.target === productModalContainer) {
            closeModal();
        }
    })
}







Alpine.start();


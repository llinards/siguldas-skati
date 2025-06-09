import './bootstrap';

import Alpine from 'alpinejs';
import 'preline'
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



Alpine.start();

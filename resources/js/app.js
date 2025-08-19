import './bootstrap';
import 'preline';
import {Fancybox} from '@fancyapps/ui';
import '@fancyapps/ui/dist/fancybox/fancybox.css';
import '@fancyapps/ui/dist/carousel/carousel.css';
import {Carousel} from '@fancyapps/ui/dist/carousel/carousel.esm.js';
import {Autoplay} from "@fancyapps/ui/dist/carousel/carousel.autoplay.esm.js";
import "@fancyapps/ui/dist/carousel/carousel.autoplay.css";

// Register Autoplay plugin
Carousel.Plugins.Autoplay = Autoplay;

// Make Carousel available globally with Autoplay plugin
window.Carousel = Carousel;
window.Fancybox = Fancybox;


function handleMobileMenu() {
    return {
        open: false,
        init() {
            this.$watch('open', (value) => {
                document.body.style.overflow = value ? 'hidden' : 'auto';
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1280) {
                    this.open = false;
                    document.body.style.overflow = 'auto';
                }
            });

            this.$nextTick(() => {
                const mobileMenuLinks = this.$el.querySelectorAll('a');
                mobileMenuLinks.forEach(link => {
                    link.addEventListener('click', (e) => {
                        const href = link.getAttribute('href');
                        const isAnchor = href && href.startsWith('#');
                        const isExternal = href && (href.startsWith('http') || link.hasAttribute('target'));

                        if (!isAnchor && !isExternal) {
                            e.preventDefault();
                            this.open = false;

                            setTimeout(() => {
                                window.location.href = href;
                            }, 300);
                        } else {
                            this.open = false;
                        }
                    });
                });
            });
        },
    };
}

window.handleMobileMenu = handleMobileMenu;

document.querySelectorAll('[id^="gallery-main-"]').forEach((el) => {
    el.addEventListener('click', function (e) {
        e.preventDefault();

        const images = [{src: el.getAttribute('href')}];

        const extra = el.getAttribute('data-gallery-extra');
        if (extra) {
            try {
                const extraImages = JSON.parse(extra);
                extraImages.forEach((url) => images.push({src: url}));
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
        item.scrollIntoView({behavior: 'smooth', block: 'start'});
    });
}

function handleHashNavigation() {
    const hash = window.location.hash;
    if (hash) {
        const targetId = hash.substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            setTimeout(() => {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }
    }
}

document.addEventListener('DOMContentLoaded', handleHashNavigation);
window.addEventListener('hashchange', handleHashNavigation);

window.addEventListener('load', () => {
    setTimeout(handleHashNavigation, 200);
});

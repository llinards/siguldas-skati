import './bootstrap';

import Alpine from 'alpinejs';
import 'preline'
import { Fancybox } from "@fancyapps/ui";

import "@fancyapps/ui/dist/fancybox/fancybox.css";

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
window.Fancybox = Fancybox;

Alpine.start();
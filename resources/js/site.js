   import { tns } from 'tiny-slider/src/tiny-slider';
import 'tiny-slider/dist/tiny-slider.css';

window.addEventListener('DOMContentLoaded', () => {
    const sliders = document.querySelectorAll('.js-partners-slider');

    sliders.forEach((slider) => {
        if (slider.children.length <= 1) {
            return;
        }

        tns({
            container: slider,
            items: 2,
            gutter: 12,
            slideBy: 'page',
            autoplay: true,
            autoplayButtonOutput: false,
            controls: false,
            nav: false,
            mouseDrag: true,
            speed: 500,
            loop: true,
            responsive: {
                640: { items: 3, gutter: 16 },
                1024: { items: 5, gutter: 24 },
            },
        });
    });
});

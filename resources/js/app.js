// import './bootstrap';

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();


import './bootstrap';
import '../css/app.css';
import '../css/shop.css';


document.addEventListener("DOMContentLoaded", () => {
    function setupCarousel(name) {
        const track = document.getElementById(`carousel-${name}`);
        const prev = document.querySelector(`.carousel-btn.prev[data-carousel="${name}"]`);
        const next = document.querySelector(`.carousel-btn.next[data-carousel="${name}"]`);

        let position = 0;
        const itemWidth = track.children[0].offsetWidth + 20; // szerokość + gap

        prev.addEventListener("click", () => {
            position = Math.min(position + itemWidth, 0);
            track.style.transform = `translateX(${position}px)`;
        });

        next.addEventListener("click", () => {
            const maxPosition = -(itemWidth * (track.children.length - 3));
            position = Math.max(position - itemWidth, maxPosition);
            track.style.transform = `translateX(${position}px)`;
        });
    }

    setupCarousel("new");
    setupCarousel("popular");
});

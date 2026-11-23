(function () {
    'use strict';

    var deck = document.getElementById('deck');
    if (!deck) return;

    var slides = Array.prototype.slice.call(deck.querySelectorAll('.deck-slide'));
    if (!slides.length) return;

    var counter = document.getElementById('deck-counter');
    var prev = document.getElementById('deck-prev');
    var next = document.getElementById('deck-next');
    var mode = document.getElementById('deck-mode');
    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    var body = document.body;
    var current = 0;
    var presenting = false;

    function pad(value) {
        return value < 10 ? '0' + value : String(value);
    }

    function sync(index) {
        current = Math.max(0, Math.min(slides.length - 1, index));
        counter.textContent = pad(current + 1) + ' / ' + pad(slides.length);
        prev.disabled = current === 0;
        next.disabled = current === slides.length - 1;
        if (presenting) {
            slides.forEach(function (slide, i) {
                slide.classList.toggle('is-current', i === current);
            });
        }
    }

    function go(index) {
        sync(index);
        slides[current].scrollIntoView({
            behavior: reduced.matches ? 'auto' : 'smooth',
            block: 'start'
        });
    }

    function setPresenting(on) {
        presenting = on;
        deck.classList.toggle('is-presenting', on);
        body.classList.toggle('deck-presenting', on);
        mode.setAttribute('aria-pressed', on ? 'true' : 'false');
        mode.textContent = on ? 'Exit' : 'Present';
        sync(current);
        slides[current].scrollIntoView({block: 'start'});
        if (!on) mode.focus({preventScroll: true});
    }

    prev.addEventListener('click', function () { go(current - 1); });
    next.addEventListener('click', function () { go(current + 1); });
    mode.addEventListener('click', function () { setPresenting(!presenting); });

    document.addEventListener('keydown', function (event) {
        var right = event.key === 'ArrowRight';
        var left = event.key === 'ArrowLeft';
        var down = event.key === 'ArrowDown';
        var up = event.key === 'ArrowUp';

        if (event.key === 'Escape' && presenting) {
            setPresenting(false);
            return;
        }
        if (right || (down && presenting)) {
            event.preventDefault();
            go(current + 1);
        } else if (left || (up && presenting)) {
            event.preventDefault();
            go(current - 1);
        }
    });

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            if (presenting) return;
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    sync(slides.indexOf(entry.target));
                }
            });
        }, {rootMargin: '-42% 0px -42% 0px', threshold: 0});

        slides.forEach(function (slide) { observer.observe(slide); });
    }

    sync(0);
})();

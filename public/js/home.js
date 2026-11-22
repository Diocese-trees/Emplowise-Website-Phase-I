/* Emplowise home: count-up stats, stack bars, rotating focus, hero parallax. Loaded only on the home page. */
(function () {
    'use strict';

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // -----------------------------------------------------------------
    // Count-up numbers
    // -----------------------------------------------------------------
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length) {
        const runCount = function (el) {
            const target = parseInt(el.getAttribute('data-count'), 10);
            if (!isFinite(target)) return;
            if (reducedMotion) { el.textContent = String(target); return; }

            const duration = 1100;
            const start = performance.now();
            const step = function (now) {
                const t = Math.min(1, (now - start) / duration);
                const eased = 1 - Math.pow(1 - t, 3);
                el.textContent = String(Math.round(target * eased));
                if (t < 1) window.requestAnimationFrame(step);
            };
            window.requestAnimationFrame(step);
        };

        if ('IntersectionObserver' in window) {
            const countObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    runCount(entry.target);
                    countObserver.unobserve(entry.target);
                });
            }, { threshold: 0.4 });
            counters.forEach(function (el) { countObserver.observe(el); });
        } else {
            counters.forEach(runCount);
        }
    }

    // -----------------------------------------------------------------
    // Hero: animated stack bars, rotating focus, pointer parallax
    // -----------------------------------------------------------------
    const stackBars = document.querySelectorAll('.stack-bar i[data-fill]');
    if (stackBars.length) {
        window.setTimeout(function () {
            stackBars.forEach(function (bar, index) {
                window.setTimeout(function () {
                    bar.style.width = bar.getAttribute('data-fill') + '%';
                }, reducedMotion ? 0 : index * 160);
            });
        }, 260);
    }

    const rotator = document.getElementById('hero-rotator');
    if (rotator && !reducedMotion) {
        const words = ['payroll inputs', 'review cycles', 'policy versions', 'onboarding flows', 'people reports'];
        let wordIndex = 0;
        window.setInterval(function () {
            wordIndex = (wordIndex + 1) % words.length;
            const next = document.createElement('b');
            next.textContent = words[wordIndex];
            rotator.replaceChildren(next);
        }, 3800);
    }

    const heroPanel = document.getElementById('hero-panel');
    if (heroPanel && !reducedMotion && window.matchMedia('(hover: hover) and (min-width: 981px)').matches) {
        const hero = heroPanel.closest('.hero');
        if (hero) {
            hero.addEventListener('pointermove', function (event) {
                const rect = heroPanel.getBoundingClientRect();
                const dx = (event.clientX - (rect.left + rect.width / 2)) / rect.width;
                const dy = (event.clientY - (rect.top + rect.height / 2)) / rect.height;
                heroPanel.style.transform = 'perspective(1100px) rotateY(' + (dx * 5).toFixed(2) + 'deg) rotateX(' + (-dy * 4).toFixed(2) + 'deg) translateY(-3px)';
            });
            hero.addEventListener('pointerleave', function () {
                heroPanel.style.transform = '';
            });
        }
    }
})();

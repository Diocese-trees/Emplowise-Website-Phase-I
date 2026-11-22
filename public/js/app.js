/* Emplowise shared UI: navigation, overlays, clipboard, reveals, reading
   progress. Page-specific behaviour lives in home.js, team.js, faq.js and
   audit.js so a brochure page never ships the wizard's logic. */
(function () {
    'use strict';

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // -----------------------------------------------------------------
    // Toast + clipboard
    // -----------------------------------------------------------------
    let toastTimer = null;

    function toast(message) {
        const el = document.getElementById('toast');
        if (!el) return;
        el.textContent = message;
        el.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () {
            el.classList.remove('is-visible');
        }, 2200);
    }

    function fallbackCopy(text) {
        const area = document.createElement('textarea');
        area.value = text;
        area.setAttribute('readonly', '');
        area.style.position = 'fixed';
        area.style.top = '-1000px';
        document.body.appendChild(area);
        area.select();
        let ok = false;
        try {
            ok = document.execCommand('copy');
        } catch (error) {
            ok = false;
        }
        document.body.removeChild(area);
        return ok;
    }

    function copyText(text) {
        if (!text) return Promise.resolve(false);
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text)
                .then(function () { return true; })
                .catch(function () { return fallbackCopy(text); });
        }
        return Promise.resolve(fallbackCopy(text));
    }

    document.addEventListener('click', function (event) {
        const target = event.target.closest('[data-copy], [data-copy-target]');
        if (!target) return;

        let text = target.getAttribute('data-copy');
        if (!text) {
            const source = document.getElementById(target.getAttribute('data-copy-target'));
            text = source ? (source.textContent || '').trim() : '';
        }

        copyText(text).then(function (ok) {
            toast(ok ? 'Copied to clipboard.' : 'Could not copy. Select the text manually.');
        });
    });

    // Placeholder social profiles stay honest until the accounts go live: the
    // link is rendered with href="#" and this handler explains why it does not
    // navigate instead of sending anyone to a dead profile.
    document.addEventListener('click', function (event) {
        const link = event.target.closest('[data-social-pending]');
        if (!link) return;
        event.preventDefault();
        toast(link.getAttribute('data-social-pending'));
    });

    window.Emplowise = window.Emplowise || {};
    window.Emplowise.toast = toast;
    window.Emplowise.copy = copyText;

    // -----------------------------------------------------------------
    // Overlay scroll lock (drawer, chat sheet)
    // Token based so two overlays never unlock each other. Uses a root
    // overflow toggle, which keeps the current scroll position intact
    // and pairs with scrollbar-gutter: stable so nothing shifts.
    // -----------------------------------------------------------------
    const locks = new Set();

    function lockScroll(token) {
        locks.add(token || 'default');
        document.documentElement.classList.add('is-scroll-locked');
    }

    function unlockScroll(token) {
        locks.delete(token || 'default');
        if (!locks.size) document.documentElement.classList.remove('is-scroll-locked');
    }

    window.Emplowise.lockScroll = lockScroll;
    window.Emplowise.unlockScroll = unlockScroll;

    // -----------------------------------------------------------------
    // Header navigation
    // -----------------------------------------------------------------
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        const closeNav = function (returnFocus) {
            if (!mainNav.classList.contains('is-open')) return;
            mainNav.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
            navToggle.setAttribute('aria-label', 'Open menu');
            unlockScroll('nav');
            if (returnFocus) navToggle.focus({ preventScroll: true });
        };

        navToggle.addEventListener('click', function () {
            const open = mainNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            if (open) {
                lockScroll('nav');
            } else {
                unlockScroll('nav');
            }
        });

        mainNav.addEventListener('click', function (event) {
            if (event.target.closest('a')) closeNav(false);
        });

        document.addEventListener('click', function (event) {
            if (!mainNav.classList.contains('is-open')) return;
            if (event.target.closest('#main-nav') || event.target.closest('#nav-toggle')) return;
            closeNav(false);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape' || !mainNav.classList.contains('is-open')) return;
            closeNav(true);
        });
    }

    // -----------------------------------------------------------------
    // Contact: copy the email address with inline confirmation
    // -----------------------------------------------------------------
    const copyEmail = document.getElementById('copy-email');
    if (copyEmail) {
        const feedback = document.getElementById('copy-feedback');
        copyEmail.addEventListener('click', function () {
            const address = copyEmail.getAttribute('data-email') || '';
            copyText(address).then(function (ok) {
                if (feedback) {
                    feedback.textContent = ok
                        ? copyEmail.getAttribute('data-done') || ''
                        : copyEmail.getAttribute('data-fail') || '';
                    feedback.classList.toggle('is-error', !ok);
                }
            });
        });
    }

    // -----------------------------------------------------------------
    // Reveal on scroll (progressive enhancement: content is visible by
    // default and only hidden once JS has confirmed it can reveal it)
    // -----------------------------------------------------------------
    const revealItems = document.querySelectorAll('.reveal');
    if (revealItems.length) {
        if ('IntersectionObserver' in window && !reducedMotion) {
            document.documentElement.classList.add('js-anim');
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '0px 0px -6% 0px', threshold: 0.05 });
            revealItems.forEach(function (item) { observer.observe(item); });

            // Safety net: never leave content hidden if observation fails.
            window.setTimeout(function () {
                revealItems.forEach(function (item) { item.classList.add('is-visible'); });
            }, 4000);
        } else {
            revealItems.forEach(function (item) { item.classList.add('is-visible'); });
        }
    }

    // -----------------------------------------------------------------
    // Header state, reading progress, back to top
    // -----------------------------------------------------------------
    const header = document.getElementById('site-header');
    const progressBar = document.getElementById('scroll-progress-bar');
    const toTop = document.getElementById('to-top');
    let scrollTicking = false;
    let scrollMax = 0;

    // Cache the scrollable range: reading scrollHeight per frame forces layout.
    function measureScroll() {
        scrollMax = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
    }

    function onScroll() {
        const y = window.scrollY || document.documentElement.scrollTop;

        if (header) header.classList.toggle('is-stuck', y > 12);
        if (progressBar) {
            const ratio = scrollMax > 0 ? Math.min(1, Math.max(0, y / scrollMax)) : 0;
            progressBar.style.transform = 'scaleX(' + ratio.toFixed(4) + ')';
        }
        if (toTop) toTop.classList.toggle('is-visible', y > 600);

        scrollTicking = false;
    }

    window.addEventListener('scroll', function () {
        if (scrollTicking) return;
        scrollTicking = true;
        window.requestAnimationFrame(onScroll);
    }, { passive: true });

    let resizeTicking = false;
    window.addEventListener('resize', function () {
        if (resizeTicking) return;
        resizeTicking = true;
        window.requestAnimationFrame(function () {
            measureScroll();
            onScroll();
            resizeTicking = false;
        });
    }, { passive: true });

    window.addEventListener('load', function () {
        measureScroll();
        onScroll();
    });

    measureScroll();
    onScroll();

    if (toTop) {
        toTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' });
        });
    }

    // -----------------------------------------------------------------
    // Open chat from anywhere
    // -----------------------------------------------------------------
    document.querySelectorAll('[data-open-chat]').forEach(function (button) {
        button.addEventListener('click', function () {
            const launcher = document.getElementById('chat-launcher');
            if (launcher) launcher.click();
        });
    });

})();

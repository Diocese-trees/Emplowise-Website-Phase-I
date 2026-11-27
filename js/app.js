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

        navToggle.addEventListener('click', function (event) {
            const open = mainNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            if (open) {
                lockScroll('nav');
                // Keyboard users open the panel expecting to be in it. A
                // mouse or touch press has detail > 0, so focus stays put
                // there and the drawer does not jump under the finger.
                if (event.detail === 0) {
                    const first = mainNav.querySelector('a[href], button:not([disabled]), summary');
                    if (first) first.focus({ preventScroll: true });
                }
            } else {
                unlockScroll('nav');
            }
        });

        // The drawer is a modal sheet below 1041px: keep Tab inside it while
        // it is open. Above that width the nav is part of the page and Tab
        // moves normally.
        mainNav.addEventListener('keydown', function (event) {
            if (event.key !== 'Tab') return;
            if (!mainNav.classList.contains('is-open')) return;
            if (window.matchMedia('(min-width: 1041px)').matches) return;

            const items = Array.prototype.filter.call(
                mainNav.querySelectorAll('a[href], button:not([disabled]), summary, [tabindex]:not([tabindex="-1"])'),
                function (el) { return el.offsetParent !== null; }
            );
            if (!items.length) return;

            const first = items[0];
            const last = items[items.length - 1];
            const active = document.activeElement;

            if (!event.shiftKey && active === last) {
                event.preventDefault();
                first.focus();
            } else if (event.shiftKey && (active === first || !mainNav.contains(active))) {
                event.preventDefault();
                last.focus();
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

    // -----------------------------------------------------------------
    // Hero support copy: a disclosure on phones, plain body copy above
    // 640px. The markup ships open so a browser without JavaScript still
    // reads the paragraph; here we close it on the first mobile paint and
    // reopen it once the viewport is wide again. A toggle the reader makes
    // is never overridden.
    // -----------------------------------------------------------------
    const heroMore = document.querySelector('[data-hero-more]');
    if (heroMore) {
        const heroQuery = window.matchMedia('(max-width: 640px)');
        let heroTouched = false;

        heroMore.addEventListener('toggle', function () { heroTouched = true; });

        const syncHeroMore = function () {
            if (!heroQuery.matches) {
                heroMore.open = true;
            } else if (!heroTouched) {
                heroMore.open = false;
            }
        };

        syncHeroMore();

        const onHeroQuery = function () {
            // A resize is not a reader choice, so allow the trim again.
            heroTouched = false;
            syncHeroMore();
        };

        if (heroQuery.addEventListener) heroQuery.addEventListener('change', onHeroQuery);
        else heroQuery.addListener(onHeroQuery);
    }

    // -----------------------------------------------------------------
    // Thumb-reach dock
    // Appears on phones once the hero is behind the reader, never on the
    // audit page (the markup omits it there), and steps aside while the
    // consent card or the chat sheet is open. Hidden on wider viewports.
    // -----------------------------------------------------------------
    const dock = document.getElementById('dock');
    if (dock) {
        // The bar is an enhancement: without this class it stays out of the
        // layout entirely, so a no-JS phone never gets off-screen links in
        // the tab order. It is switched on before the first paint of this
        // deferred script.
        document.documentElement.classList.add('has-js-dock');
        const dockQuery = window.matchMedia('(max-width: 640px)');
        let footerInView = false;

        const setDock = function (visible) {
            dock.classList.toggle('is-visible', visible);
            document.body.classList.toggle('dock-visible', visible);
            if (visible) {
                dock.removeAttribute('inert');
                dock.removeAttribute('aria-hidden');
            } else {
                dock.setAttribute('inert', '');
                dock.setAttribute('aria-hidden', 'true');
            }
        };

        const updateDock = function () {
            if (!dockQuery.matches) {
                setDock(false);
                return;
            }
            const blocked = document.body.classList.contains('has-consent')
                || document.body.classList.contains('chat-open')
                || footerInView;
            const threshold = Math.max(240, window.innerHeight * 0.6);
            const y = window.scrollY || document.documentElement.scrollTop || 0;
            setDock(y > threshold && !blocked);
        };

        // The footer already carries the same actions, so the bar stands
        // down as soon as it appears; that also keeps the legal links clear
        // instead of sitting under a fixed bar.
        const footer = document.querySelector('.site-footer');
        if (footer && 'IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                footerInView = entries[0].isIntersecting;
                updateDock();
            }, { rootMargin: '0px 0px -45% 0px' }).observe(footer);
        }

        window.addEventListener('scroll', updateDock, { passive: true });
        window.addEventListener('resize', updateDock, { passive: true });
        if (dockQuery.addEventListener) dockQuery.addEventListener('change', updateDock);
        else dockQuery.addListener(updateDock);

        // Consent and chat toggle classes on <body>, not scroll events, so
        // watch that attribute to stand the dock down the moment they open.
        if ('MutationObserver' in window) {
            new MutationObserver(updateDock).observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        updateDock();
    }

    // -----------------------------------------------------------------
    // Chat launcher: tuck it away while scrolling down, bring it back the
    // moment the reader scrolls up, so it never sits on the thumb path.
    // -----------------------------------------------------------------
    const chatLauncher = document.getElementById('chat-launcher');
    if (chatLauncher) {
        let lastY = window.scrollY || 0;
        let launcherTicking = false;

        const updateLauncher = function () {
            const y = window.scrollY || document.documentElement.scrollTop || 0;
            const delta = y - lastY;

            if (Math.abs(delta) > 6) {
                const hidden = chatLauncher.getAttribute('aria-expanded') !== 'true';
                if (hidden) chatLauncher.classList.toggle('is-tucked', delta > 0 && y > 240);
                lastY = y;
            }
            launcherTicking = false;
        };

        window.addEventListener('scroll', function () {
            if (launcherTicking) return;
            launcherTicking = true;
            window.requestAnimationFrame(updateLauncher);
        }, { passive: true });
    }

})();

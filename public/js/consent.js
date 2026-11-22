/* Emplowise cookie choices: one preference cookie, no tracking by default.
   Essential always on; analytics and marketing are opt-in and only recorded —
   no third-party tag is loaded by this site today. */
(function () {
    'use strict';

    var STORAGE_KEY = 'emplowise:consent';
    var COOKIE_NAME = 'emplowise_consent';
    var COOKIE_AGE = 15552000; // 180 days

    var banner = document.getElementById('consent');
    if (!banner) return;

    var prefs = document.getElementById('consent-prefs');
    var manage = banner.querySelector('[data-consent-manage]');
    var analytics = banner.querySelector('[data-consent-analytics]');
    var marketing = banner.querySelector('[data-consent-marketing]');
    var hideTimer = null;

    function readStorage() {
        try {
            var raw = window.localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (error) {
            return null;
        }
    }

    function readCookie() {
        var match = document.cookie.match(new RegExp('(?:^|; )' + COOKIE_NAME + '=([^;]*)'));
        if (!match) return null;
        try {
            return JSON.parse(decodeURIComponent(match[1]));
        } catch (error) {
            return null;
        }
    }

    function remember(value) {
        var saved = {
            essential: true,
            analytics: !!value.analytics,
            marketing: !!value.marketing,
            updated: new Date().toISOString()
        };

        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(saved));
        } catch (error) {
            /* Private mode: the cookie below still carries the choice. */
        }

        try {
            document.cookie = COOKIE_NAME + '=' + encodeURIComponent(JSON.stringify(saved))
                + '; path=/; max-age=' + COOKIE_AGE + '; SameSite=Lax';
        } catch (error) {
            /* No cookie jar available; the page keeps working. */
        }

        document.documentElement.setAttribute('data-consent-state', saved.analytics ? 'analytics' : 'essential');
        document.dispatchEvent(new CustomEvent('emplowise:consent', { detail: saved }));

        return saved;
    }

    function sync(value) {
        if (analytics) analytics.checked = !!(value && value.analytics);
        if (marketing) marketing.checked = !!(value && value.marketing);
        document.documentElement.setAttribute(
            'data-consent-state',
            value && value.analytics ? 'analytics' : 'essential'
        );
    }

    function syncExpanded() {
        banner.classList.toggle('is-expanded', !!prefs && !prefs.hidden);
    }

    function open(expand, focus) {
        window.clearTimeout(hideTimer);
        banner.hidden = false;
        banner.classList.remove('is-closing');

        if (expand && prefs) {
            prefs.hidden = false;
            if (manage) manage.setAttribute('aria-expanded', 'true');
        }

        syncExpanded();

        if (focus) {
            var first = banner.querySelector('[data-consent-all]');
            if (first) first.focus({ preventScroll: true });
        }
    }

    function close() {
        banner.classList.add('is-closing');
        hideTimer = window.setTimeout(function () {
            banner.hidden = true;
            banner.classList.remove('is-closing');
        }, 240);
    }

    function save(value) {
        remember(value);
        sync(value);
        close();

        if (window.Emplowise && window.Emplowise.toast) {
            window.Emplowise.toast(banner.getAttribute('data-saved') || 'Preferences saved.');
        }
    }

    banner.addEventListener('click', function (event) {
        var button = event.target.closest('button');
        if (!button) return;

        if (button.hasAttribute('data-consent-all')) {
            save({ analytics: true, marketing: true });
            return;
        }

        if (button.hasAttribute('data-consent-essential')) {
            save({ analytics: false, marketing: false });
            return;
        }

        if (button.hasAttribute('data-consent-manage')) {
            if (!prefs) return;
            var willOpen = prefs.hidden;
            prefs.hidden = !willOpen;
            button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            syncExpanded();
            if (willOpen && analytics) analytics.focus({ preventScroll: true });
            return;
        }

        if (button.hasAttribute('data-consent-save')) {
            save({
                analytics: !!(analytics && analytics.checked),
                marketing: !!(marketing && marketing.checked)
            });
        }
    });

    document.addEventListener('click', function (event) {
        var opener = event.target.closest('[data-consent-open]');
        if (!opener) return;
        event.preventDefault();
        open(true, true);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape' || banner.hidden) return;
        close();
    });

    var stored = readStorage() || readCookie();
    sync(stored);

    function afterIntro() {
        if (stored) return;
        window.setTimeout(function () {
            if (banner.hidden) open(false, false);
        }, 700);
    }

    afterIntro();

    window.Emplowise = window.Emplowise || {};
    window.Emplowise.consent = {
        get: function () { return readStorage() || readCookie(); },
        open: function () { open(true, true); }
    };
})();

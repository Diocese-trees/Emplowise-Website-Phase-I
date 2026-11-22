/* Theme bootstrap. Runs synchronously in <head> so the correct palette is on
   the root element before the first paint (deferring it would flash light).
   Choice order: stored preference, then the OS setting. Toggles are wired by
   event delegation so the header and footer buttons stay in sync. */
(function () {
    'use strict';

    var KEY = 'emplowise:theme';
    var root = document.documentElement;
    var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

    function stored() {
        try {
            var value = window.localStorage.getItem(KEY);
            return value === 'dark' || value === 'light' ? value : null;
        } catch (error) {
            return null;
        }
    }

    function preferred() {
        return media && media.matches ? 'dark' : 'light';
    }

    function apply(theme, persist) {
        if (theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
        } else {
            root.removeAttribute('data-theme');
        }

        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', theme === 'dark' ? '#000000' : '#081B3E');

        sync(theme);

        if (persist) {
            try {
                window.localStorage.setItem(KEY, theme);
            } catch (error) {
                /* Private mode: the choice still applies for this page view. */
            }
        }
    }

    function sync(theme) {
        var dark = theme === 'dark';

        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            button.setAttribute('aria-pressed', dark ? 'true' : 'false');
            button.setAttribute(
                'aria-label',
                button.getAttribute(dark ? 'data-label-light' : 'data-label-dark') || ''
            );

            var text = button.getAttribute(dark ? 'data-text-light' : 'data-text-dark');
            if (text) {
                var label = button.querySelector('[data-theme-text]');
                if (label) label.textContent = text;
            }
        });
    }

    function current() {
        return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    }

    // Mark the document so the toggle only exists once it can work.
    root.classList.add('js-theme');
    apply(stored() || preferred(), false);

    // In <head> the toggles are not parsed yet, so label them once they are.
    var syncAll = function () { sync(current()); };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncAll, { once: true });
    } else {
        syncAll();
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-theme-toggle]');
        if (!button) return;
        event.preventDefault();
        apply(current() === 'dark' ? 'light' : 'dark', true);
    });

    // Follow the OS setting only while the visitor has not chosen explicitly.
    if (media) {
        var onChange = function () {
            if (!stored()) apply(preferred(), false);
        };
        if (media.addEventListener) media.addEventListener('change', onChange);
        else if (media.addListener) media.addListener(onChange);
    }

    window.Emplowise = window.Emplowise || {};
    window.Emplowise.theme = {
        get: current,
        set: function (theme) { apply(theme, true); }
    };
})();

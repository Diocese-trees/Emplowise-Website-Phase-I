/* Toggle clicks, labels and persistence only. The palette itself is applied
   before first paint by the inline bootstrap in <head> (see
   App\Support\ThemeBootstrap), so nothing here can cause a light flash. */
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

    function current() {
        return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    }

    function paintMeta(theme) {
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', theme === 'dark' ? '#050F26' : '#081B3E');
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

    function apply(theme, persist) {
        if (theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
        } else {
            root.removeAttribute('data-theme');
        }

        paintMeta(theme);
        sync(theme);

        if (persist) {
            try {
                window.localStorage.setItem(KEY, theme);
            } catch (error) {
                /* Private mode: the choice still applies for this page view. */
            }
        }
    }

    // Label the buttons and reconcile the meta tag with the bootstrap choice.
    apply(current(), false);

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-theme-toggle]');
        if (!button) return;
        event.preventDefault();
        apply(current() === 'dark' ? 'light' : 'dark', true);
    });

    // Follow the OS setting only while the visitor has not chosen explicitly.
    if (media) {
        var onChange = function () {
            if (!stored()) apply(media.matches ? 'dark' : 'light', false);
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

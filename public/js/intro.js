/* Emplowise intro: "Emplo" and "wise" meet at the centre, then the site opens.
   Loaded synchronously before the page paints so the first frame is covered.
   Kept deliberately small: transform/opacity only, once per browser session. */
(function () {
    'use strict';

    var KEY = 'emplowise:intro';
    var root = document.documentElement;
    var seen = false;

    try {
        seen = window.sessionStorage.getItem(KEY) === 'done';
    } catch (error) {
        seen = false;
    }

    // A 1.5s brand moment is a luxury on a 2G connection or with data saver
    // on: skip straight to the site instead of spending the budget on it.
    var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    var slow = !!conn && (conn.saveData === true || /(^|-)2g$/.test(conn.effectiveType || ''));

    // ?intro=1 replays the sequence on demand: useful for demos, and for
    // checking the timing after the once-per-session flag has been set.
    var force = /(?:^|[?&])intro=1(?:&|$)/.test(window.location.search);

    if ((seen && !force) || (slow && !force)) return;

    root.classList.add('intro-pending');

    var finished = false;

    function finish() {
        if (finished) return;
        finished = true;

        var el = document.getElementById('intro');
        if (el) {
            el.hidden = true;
            el.classList.remove('is-leaving');
        }

        root.classList.remove('intro-pending');

        try {
            window.sessionStorage.setItem(KEY, 'done');
        } catch (error) {
            /* Storage can be unavailable in private mode; the intro still ends. */
        }

        document.dispatchEvent(new CustomEvent('emplowise:intro-done'));
    }

    function play() {
        var el = document.getElementById('intro');
        if (!el) {
            finish();
            return;
        }

        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var hold = reduced ? 240 : 1650;

        window.setTimeout(function () {
            el.classList.add('is-leaving');
            window.setTimeout(finish, reduced ? 110 : 700);
        }, hold);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', play, { once: true });
    } else {
        play();
    }

    // Safety net: never leave the site covered if an animation event stalls.
    window.setTimeout(finish, 4200);
})();

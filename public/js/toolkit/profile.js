/* Emplowise Student Toolkit: profile optimiser.
 *
 * Browser port of the local, deterministic path of
 * App\Services\StudentToolkit::optimizeProfile() (headlines, summary, bullets and
 * fixes). The model branch is intentionally not ported: this file never touches
 * the network, so the Student Hub profile tool works on a static build.
 *
 * Loaded with <script src="/js/toolkit/profile.js" defer> before student.js.
 */
(function () {
    'use strict';

    /* PCRE \s without the /u modifier: tab, LF, VT, FF, CR and space only. */
    var WS = '[\\t\\n\\v\\f\\r ]';
    var WS_GLOBAL = new RegExp(WS + '+', 'g');
    /* PHP trim() default characters: " \t\n\r\0\x0B" (no form feed). */
    var TRIM_ALL = /^[ \t\n\r\x00\x0B]+|[ \t\n\r\x00\x0B]+$/g;
    var TRIM_END = /[ \t\n\r\x00\x0B]+$/;
    /* PCRE \R without /u: CRLF, LF, VT, FF or CR, matched atomically. */
    var NEWLINE = /\r\n|[\n\v\f\r]/;
    var BULLET_MARKER = new RegExp('^' + WS + '*[-•*·]' + WS + '*');
    var ROLE_SEPARATOR = /[|·•,–—-]/;

    var WEAK_PHRASES = [
        'passionate about', 'hardworking', 'aspiring', 'team player',
        'go getter', 'results driven', 'detail oriented'
    ];

    /* deriveRole(): ordered cut-off phrases, first hit wins. */
    var ROLE_CUTOFFS = [
        'passionate about', 'aspiring', 'hardworking', 'looking for',
        'seeking', 'interested in', 'enthusiast'
    ];

    /* detectSkills(): ordered keyword map, first match wins. */
    var KNOWN_SKILLS = [
        'python', 'javascript', 'typescript', 'react', 'laravel', 'php', 'sql', 'excel',
        'figma', 'canva', 'seo', 'copywriting', 'photoshop', 'tableau', 'power bi',
        'communication', 'research', 'project management', 'stakeholder management',
        'payroll', 'recruitment', 'onboarding', 'data analysis', 'customer service',
        'sales', 'accounting', 'bookkeeping', 'supply chain', 'logistics'
    ];

    /* rewriteBullet(): ordered verb map, first match wins. */
    var BULLET_VERBS = [
        { pattern: new RegExp('^responsible for' + WS + '+', 'i'), value: 'Owned ' },
        { pattern: new RegExp('^worked on' + WS + '+', 'i'), value: 'Delivered ' },
        { pattern: new RegExp('^helped (to' + WS + '+)?', 'i'), value: 'Drove ' },
        { pattern: new RegExp('^assisted (with' + WS + '+)?', 'i'), value: 'Supported ' },
        { pattern: new RegExp('^participated in' + WS + '+', 'i'), value: 'Contributed to ' },
        { pattern: new RegExp('^was involved in' + WS + '+', 'i'), value: 'Contributed to ' },
        { pattern: new RegExp('^duties included' + WS + '+', 'i'), value: 'Ran ' },
        { pattern: new RegExp('^tasked with' + WS + '+', 'i'), value: 'Led ' }
    ];

    /* ---- PHP / PCRE primitives ------------------------------------ */

    /* mb_substr() counts code points, so surrogate pairs stay one character. */
    function characters(value) {
        var out = [];
        var i = 0;
        while (i < value.length) {
            var code = value.charCodeAt(i);
            if (code >= 0xD800 && code <= 0xDBFF && i + 1 < value.length) {
                out.push(value.slice(i, i + 2));
                i += 2;
                continue;
            }
            out.push(value.charAt(i));
            i += 1;
        }
        return out;
    }

    function trim(value) {
        return value.replace(TRIM_ALL, '');
    }

    function trimEnd(value) {
        return value.replace(TRIM_END, '');
    }

    /* preg_replace('/\s+/', ' ', $value) */
    function collapse(value) {
        return value.replace(WS_GLOBAL, ' ');
    }

    /* Str::limit($value, $limit, $end) for narrow-width text. */
    function limit(value, length, end) {
        var chars = characters(value);
        if (chars.length <= length) {
            return value;
        }
        return trimEnd(chars.slice(0, length).join('')) + (end === undefined ? '...' : end);
    }

    /* Str::ucfirst(): uppercases the first character, leaves the rest alone. */
    function strUcfirst(value) {
        var chars = characters(value);
        if (chars.length === 0) {
            return value;
        }
        chars[0] = chars[0].toUpperCase();
        return chars.join('');
    }

    /* PHP ucfirst(): ASCII bytes only, used inside rewriteBullet(). */
    function asciiUcfirst(value) {
        var first = value.charAt(0);
        if (first >= 'a' && first <= 'z') {
            return first.toUpperCase() + value.slice(1);
        }
        return value;
    }

    /* Str::lower() */
    function lower(value) {
        return value.toLowerCase();
    }

    /* Str::title(): first letter of each word up, everything else down. */
    function title(value) {
        return value.toLowerCase().replace(/(^|[^a-z0-9])([a-z])/g, function (all, before, letter) {
            return before + letter.toUpperCase();
        });
    }

    /* preg_quote($phrase, '/') */
    function quoteMeta(value) {
        return value.replace(/[.\\\+*\?[\]^$(){}=!<>|:\-\/#\x00]/g, '\\$&');
    }

    /* str_contains() */
    function contains(haystack, needle) {
        return haystack.indexOf(needle) !== -1;
    }

    function containsAny(haystack, needles) {
        for (var i = 0; i < needles.length; i++) {
            if (needles[i] !== '' && contains(haystack, needles[i])) {
                return true;
            }
        }
        return false;
    }

    /* array_filter() with no callback: drops values PHP treats as false. */
    function filterTruthy(values) {
        var out = [];
        for (var i = 0; i < values.length; i++) {
            if (values[i] !== '' && values[i] !== '0') {
                out.push(values[i]);
            }
        }
        return out;
    }

    /* array_values(array_unique()) keeping first occurrence order. */
    function unique(values) {
        var out = [];
        for (var i = 0; i < values.length; i++) {
            if (out.indexOf(values[i]) === -1) {
                out.push(values[i]);
            }
        }
        return out;
    }

    /* preg_split('/(?<=[.!?])\s+/', $value) without a lookbehind regex. */
    function splitSentences(value) {
        var out = [];
        var current = '';
        var i = 0;
        var runs = new RegExp(WS);
        while (i < value.length) {
            if (runs.test(value.charAt(i)) && i > 0 && /[.!?]/.test(value.charAt(i - 1))) {
                while (i < value.length && runs.test(value.charAt(i))) {
                    i += 1;
                }
                out.push(current);
                current = '';
                continue;
            }
            current += value.charAt(i);
            i += 1;
        }
        out.push(current);
        return out;
    }

    /* ---- Local engines -------------------------------------------- */

    function deriveRole(headline) {
        var value = trim(headline);

        if (value === '') {
            return 'Early career professional';
        }

        var parts = value.split(ROLE_SEPARATOR);
        var role = trim(parts.length > 0 ? parts[0] : value);
        var haystack = lower(role);

        for (var i = 0; i < ROLE_CUTOFFS.length; i++) {
            var position = haystack.indexOf(ROLE_CUTOFFS[i]);
            if (position !== -1) {
                role = trim(role.slice(0, position));
                break;
            }
        }

        role = trim(collapse(role));

        return role === '' ? 'Early career professional' : strUcfirst(limit(role, 60, ''));
    }

    function detectSkills(text) {
        var found = [];

        for (var i = 0; i < KNOWN_SKILLS.length; i++) {
            if (contains(text, KNOWN_SKILLS[i])) {
                found.push(title(KNOWN_SKILLS[i]));
            }
        }

        return unique(found);
    }

    function cleanSummary(summary, role, skills) {
        var value = trim(collapse(summary));

        for (var i = 0; i < WEAK_PHRASES.length; i++) {
            value = value.replace(
                new RegExp('\\b' + quoteMeta(WEAK_PHRASES[i]) + '\\b', 'gi'),
                ''
            );
        }

        value = trim(collapse(value));

        if (value === '') {
            var skillLine = skills.length === 0
                ? 'structured problem solving'
                : skills.slice(0, 3).join(', ');

            return 'Early career ' + lower(role) + ' with hands on work in ' + skillLine +
                '. I like clear briefs, measurable outcomes, and documentation other people can follow.' +
                ' Currently looking for a role where I can own delivery end to end.';
        }

        return limit(splitSentences(value).slice(0, 3).join(' '), 480);
    }

    function rewriteBullet(line) {
        var value = trim(line.replace(BULLET_MARKER, ''));

        if (value === '') {
            return '';
        }

        for (var i = 0; i < BULLET_VERBS.length; i++) {
            if (BULLET_VERBS[i].pattern.test(value)) {
                value = value.replace(BULLET_VERBS[i].pattern, BULLET_VERBS[i].value);
                break;
            }
        }

        value = asciiUcfirst(value);

        if (!/\b[0-9]/.test(value)) {
            value = value.replace(/\.+$/, '') + ' [add one number]';
        }

        return limit(value, 190);
    }

    function localProfile(headline, summary, lines, targetRole) {
        var role = trim(targetRole) !== ''
            ? strUcfirst(limit(trim(targetRole), 60, ''))
            : deriveRole(headline);

        var skills = detectSkills(lower(headline + ' ' + summary + ' ' + lines.join(' ')));
        var skillPhrase = skills.length === 0
            ? 'structured delivery'
            : skills.slice(0, 3).join(', ');

        var headlines = [
            limit(role + ' | ' + skillPhrase, 118, ''),
            limit(role + ' building ' + (skills.length === 0 ? 'measurable outcomes' : skills[0] + ' systems'), 118, ''),
            limit(role + ' · ' + skillPhrase + ' · open to ' + (skills.length === 0 ? 'early career' : 'new roles'), 118, '')
        ];

        var bullets = [];
        var fixes = [];
        var rewritten;

        for (var i = 0; i < lines.length; i++) {
            rewritten = rewriteBullet(lines[i]);

            if (rewritten !== '') {
                bullets.push(rewritten);
            }
        }

        if (containsAny(lower(summary), ['passionate', 'hardworking', 'aspiring', 'team player'])) {
            fixes.push('Weak words removed. Replace claims like passionate with one proof point.');
        }

        if (!/\b[0-9]/.test(summary + lines.join(' '))) {
            fixes.push('Add one number somewhere. Without a metric every claim reads the same.');
        }

        if (bullets.length > 6) {
            fixes.push('Keep six bullets or fewer. Recruiters skim, they do not read.');
        }

        if (fixes.length === 0) {
            fixes.push('Solid base. Now mirror three exact keywords from the job post you are targeting.');
        }

        return {
            headlines: filterTruthy(headlines),
            summary: cleanSummary(summary, role, skills),
            bullets: bullets.slice(0, 10),
            fixes: fixes.slice(0, 3)
        };
    }

    function optimizeProfile(headline, summary, bullets, targetRole) {
        var lines = [];
        var raw = bullets.split(NEWLINE);

        for (var i = 0; i < raw.length; i++) {
            if (trim(raw[i]) !== '' && trim(raw[i]) !== '0') {
                lines.push(trim(raw[i]));
            }
        }

        var local = localProfile(headline, summary, lines, targetRole === undefined ? '' : targetRole);

        return {
            headlines: local.headlines,
            summary: local.summary,
            bullets: local.bullets,
            fixes: local.fixes,
            source: 'local'
        };
    }

    window.EmplowiseToolkit = window.EmplowiseToolkit || {};

    window.EmplowiseToolkit.profile = {
        optimize: optimizeProfile
    };
})();

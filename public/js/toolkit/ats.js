/*
 * Emplowise Student Hub: the local ATS scoring engine.
 *
 * A byte-for-byte port of the deterministic path in
 * app/Services/StudentToolkit.php (atsScore and its helpers). No backend, no
 * model, no DOM: the same resume and job description must always produce the
 * same result in the browser as PHP produces on the server. The committed
 * fixtures in tools/parity/fixtures.json are the specification; run
 * node tools/parity/check-ats.mjs to prove parity.
 *
 * Loaded with <script src="/js/toolkit/ats.js" defer> before student.js.
 */
(function () {
    'use strict';

    window.EmplowiseToolkit = window.EmplowiseToolkit || {};

    // ------------------------------------------------------------------
    // PHP runtime equivalents
    // ------------------------------------------------------------------

    // PCRE without the /u flag works on bytes, and PHP's \s is the ASCII set:
    // space, \t, \n, \v, \f, \r. JS \s also matches NBSP and friends, so every
    // whitespace test below is written out explicitly.
    function isSpace(c) {
        return c === ' ' || c === '\t' || c === '\n' || c === '\u000B' || c === '\f' || c === '\r';
    }

    function isAlpha(c) {
        return (c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z');
    }

    function isDigit(c) {
        return c >= '0' && c <= '9';
    }

    // PCRE \w and \b word characters in byte mode: ASCII alphanumerics plus _.
    function isWord(c) {
        return isAlpha(c) || isDigit(c) || c === '_';
    }

    // strlen() is byte length, so a UTF-8 helper is needed wherever PHP uses it.
    function byteLength(str) {
        var total = 0;
        for (var i = 0; i < str.length; i++) {
            var code = str.charCodeAt(i);
            if (code < 0x80) {
                total += 1;
            } else if (code < 0x800) {
                total += 2;
            } else if (code >= 0xD800 && code < 0xDC00 && i + 1 < str.length
                && str.charCodeAt(i + 1) >= 0xDC00 && str.charCodeAt(i + 1) < 0xE000) {
                total += 4;
                i++;
            } else {
                total += 3;
            }
        }
        return total;
    }

    // PHP trim() with no charlist strips " \t\n\r\0\x0B" and nothing else.
    function trim(str) {
        var start = 0;
        var end = str.length;
        while (start < end && isTrimChar(str.charAt(start))) start++;
        while (end > start && isTrimChar(str.charAt(end - 1))) end--;
        return str.slice(start, end);
    }

    function rtrim(str) {
        var end = str.length;
        while (end > 0 && isTrimChar(str.charAt(end - 1))) end--;
        return str.slice(0, end);
    }

    function isTrimChar(c) {
        return c === ' ' || c === '\t' || c === '\n' || c === '\r' || c === '\u0000' || c === '\u000B';
    }

    function trimChars(str, chars) {
        var start = 0;
        var end = str.length;
        while (start < end && chars.indexOf(str.charAt(start)) !== -1) start++;
        while (end > start && chars.indexOf(str.charAt(end - 1)) !== -1) end--;
        return str.slice(start, end);
    }

    // Str::lower() is mb_strtolower(); toLowerCase() matches it for the scripts
    // a resume or posting can contain.
    function lower(str) {
        return str.toLowerCase();
    }

    function strContains(haystack, needle) {
        return needle !== '' && haystack.indexOf(needle) !== -1;
    }

    function inArray(needle, haystack) {
        return haystack.indexOf(needle) !== -1;
    }

    // substr_count(): non-overlapping occurrences.
    function substrCount(haystack, needle) {
        if (needle === '') return 0;
        var count = 0;
        var at = haystack.indexOf(needle);
        while (at !== -1) {
            count++;
            at = haystack.indexOf(needle, at + needle.length);
        }
        return count;
    }

    // php_intpow10(): the exact powers PHP keeps in a lookup table, 1e-8..1e22.
    var POW10 = [1e-8, 1e-7, 1e-6, 1e-5, 1e-4, 1e-3, 1e-2, 1e-1, 1e0, 1e1,
        1e2, 1e3, 1e4, 1e5, 1e6, 1e7, 1e8, 1e9, 1e10, 1e11, 1e12, 1e13, 1e14,
        1e15, 1e16, 1e17, 1e18, 1e19, 1e20, 1e21, 1e22];

    // php_intlog10abs(): floor(log10(fabs(value))).
    function intLog10Abs(value) {
        value = Math.abs(value);
        if (value < 1e-8 || value > 1e22) return Math.floor(Math.log(value) / Math.LN10);
        for (var power = 22; power >= -8; power--) {
            if (value >= POW10[power + 8]) return power;
        }
        return -8;
    }

    // php_round_helper() in PHP_ROUND_HALF_UP mode.
    function roundHelper(value) {
        return value >= 0 ? Math.floor(value + 0.5) : Math.ceil(value - 0.5);
    }

    // round($value) with the default places of 0. PHP first pre-rounds to the
    // 14 significant digits floating point guarantees, so a product that lands
    // a hair below .5, such as 55 * (6 / 11) * 1.15 = 34.49999999999999, still
    // rounds up to 35. Math.round() would give 34.
    function phpRound(value) {
        if (!isFinite(value) || value === 0) return value;

        var precisionPlaces = 14 - intLog10Abs(value);

        if (precisionPlaces > 0 && precisionPlaces - 15 < 0) {
            var scale = POW10[precisionPlaces + 8];
            var prerounded = roundHelper(value * scale);
            return roundHelper(prerounded / scale);
        }

        // Beyond the precision FP guarantees, rounding would only shuffle noise.
        if (Math.abs(value) >= 1e15) return value;
        return roundHelper(value);
    }

    // mb_strwidth(): the East Asian Wide and Fullwidth code points, dumped from
    // PHP so the table is the same one mbstring uses. Everything else is 1.
    var WIDE_RANGES_SOURCE =
        '1100-115F,231A-231B,2329-232A,23E9-23EC,23F0-23F0,23F3-23F3,' +
        '25FD-25FE,2614-2615,2648-2653,267F-267F,2693-2693,26A1-26A1,' +
        '26AA-26AB,26BD-26BE,26C4-26C5,26CE-26CE,26D4-26D4,26EA-26EA,' +
        '26F2-26F3,26F5-26F5,26FA-26FA,26FD-26FD,2705-2705,270A-270B,' +
        '2728-2728,274C-274C,274E-274E,2753-2755,2757-2757,2795-2797,' +
        '27B0-27B0,27BF-27BF,2B1B-2B1C,2B50-2B50,2B55-2B55,2E80-2E99,' +
        '2E9B-2EF3,2F00-2FD5,2FF0-2FFB,3000-303E,3041-3096,3099-30FF,' +
        '3105-312F,3131-318E,3190-31E3,31F0-321E,3220-3247,3250-4DBF,' +
        '4E00-A48C,A490-A4C6,A960-A97C,AC00-D7A3,F900-FAFF,FE10-FE19,' +
        'FE30-FE52,FE54-FE66,FE68-FE6B,FF01-FF60,FFE0-FFE6,16FE0-16FE4,' +
        '16FF0-16FF1,17000-187F7,18800-18CD5,18D00-18D08,1AFF0-1AFF3,' +
        '1AFF5-1AFFB,1AFFD-1AFFE,1B000-1B122,1B150-1B152,1B164-1B167,' +
        '1B170-1B2FB,1F004-1F004,1F0CF-1F0CF,1F18E-1F18E,1F191-1F19A,' +
        '1F200-1F202,1F210-1F23B,1F240-1F248,1F250-1F251,1F260-1F265,' +
        '1F300-1F320,1F32D-1F335,1F337-1F37C,1F37E-1F393,1F3A0-1F3CA,' +
        '1F3CF-1F3D3,1F3E0-1F3F0,1F3F4-1F3F4,1F3F8-1F43E,1F440-1F440,' +
        '1F442-1F4FC,1F4FF-1F53D,1F54B-1F54E,1F550-1F567,1F57A-1F57A,' +
        '1F595-1F596,1F5A4-1F5A4,1F5FB-1F64F,1F680-1F6C5,1F6CC-1F6CC,' +
        '1F6D0-1F6D2,1F6D5-1F6D7,1F6DD-1F6DF,1F6EB-1F6EC,1F6F4-1F6FC,' +
        '1F7E0-1F7EB,1F7F0-1F7F0,1F90C-1F93A,1F93C-1F945,1F947-1F9FF,' +
        '1FA70-1FA74,1FA78-1FA7C,1FA80-1FA86,1FA90-1FAAC,1FAB0-1FABA,' +
        '1FAC0-1FAC5,1FAD0-1FAD9,1FAE0-1FAE7,1FAF0-1FAF6,20000-2FFFD,' +
        '30000-3FFFD,';

    var WIDE_RANGES = [];
    (function () {
        var parts = WIDE_RANGES_SOURCE.split(',');
        for (var i = 0; i < parts.length; i++) {
            if (parts[i] === '') continue;
            var edge = parts[i].split('-');
            WIDE_RANGES.push(parseInt(edge[0], 16), parseInt(edge[1], 16));
        }
    })();

    function charWidth(code) {
        var low = 0;
        var high = WIDE_RANGES.length / 2 - 1;
        while (low <= high) {
            var mid = (low + high) >> 1;
            if (code < WIDE_RANGES[mid * 2]) high = mid - 1;
            else if (code > WIDE_RANGES[mid * 2 + 1]) low = mid + 1;
            else return 2;
        }
        return 1;
    }

    function eachCodePoint(str, visit) {
        for (var i = 0; i < str.length; i++) {
            var code = str.charCodeAt(i);
            if (code >= 0xD800 && code < 0xDC00 && i + 1 < str.length
                && str.charCodeAt(i + 1) >= 0xDC00 && str.charCodeAt(i + 1) < 0xE000) {
                code = 0x10000 + ((code - 0xD800) << 10) + (str.charCodeAt(i + 1) - 0xDC00);
                i++;
            }
            visit(code, i);
        }
    }

    function strWidth(str) {
        var width = 0;
        eachCodePoint(str, function (code) { width += charWidth(code); });
        return width;
    }

    // mb_strimwidth($value, 0, $limit, '', 'UTF-8'): keep code points while the
    // running width fits.
    function strImWidth(str, limit) {
        var width = 0;
        var out = '';
        var i = 0;
        while (i < str.length) {
            var code = str.charCodeAt(i);
            var size = 1;
            if (code >= 0xD800 && code < 0xDC00 && i + 1 < str.length
                && str.charCodeAt(i + 1) >= 0xDC00 && str.charCodeAt(i + 1) < 0xE000) {
                code = 0x10000 + ((code - 0xD800) << 10) + (str.charCodeAt(i + 1) - 0xDC00);
                size = 2;
            }
            var next = width + charWidth(code);
            if (next > limit) break;
            out += str.substr(i, size);
            width = next;
            i += size;
        }
        return out;
    }

    // Str::limit($value, $limit, $end) without word preservation.
    function strLimit(value, limit, end) {
        if (strWidth(value) <= limit) return value;
        return rtrim(strImWidth(value, limit)) + end;
    }

    // preg_split('/\R/', $subject) without the /u flag: PCRE looks at BYTES, so
    // the separators are CR, LF, \v, \f, the NEL byte 0x85, and CRLF as a pair.
    // 0x85 also occurs as a continuation byte inside multi-byte characters, so
    // PHP can split a character in half. Working on the UTF-8 bytes reproduces
    // that; an orphaned part of a character is dropped here, and every caller
    // feeds the pieces to normalise(), where those bytes become a space anyway.
    function splitBreaks(subject) {
        var bytes = toUtf8(subject);
        var parts = [];
        var start = 0;
        var i = 0;

        while (i < bytes.length) {
            var byte = bytes[i];
            var size = 0;

            if (byte === 0x0D) size = bytes[i + 1] === 0x0A ? 2 : 1;
            else if (byte === 0x0A || byte === 0x0B || byte === 0x0C || byte === 0x85) size = 1;

            if (size === 0) {
                i++;
                continue;
            }

            parts.push(fromUtf8(bytes.slice(start, i)));
            i += size;
            start = i;
        }

        parts.push(fromUtf8(bytes.slice(start)));
        return parts;
    }

    function toUtf8(str) {
        var bytes = [];
        for (var i = 0; i < str.length; i++) {
            var code = str.charCodeAt(i);

            if (code < 0x80) {
                bytes.push(code);
            } else if (code < 0x800) {
                bytes.push(0xC0 | (code >> 6), 0x80 | (code & 0x3F));
            } else if (code >= 0xD800 && code < 0xDC00 && i + 1 < str.length
                && str.charCodeAt(i + 1) >= 0xDC00 && str.charCodeAt(i + 1) < 0xE000) {
                var pair = 0x10000 + ((code - 0xD800) << 10) + (str.charCodeAt(i + 1) - 0xDC00);
                bytes.push(0xF0 | (pair >> 18), 0x80 | ((pair >> 12) & 0x3F), 0x80 | ((pair >> 6) & 0x3F), 0x80 | (pair & 0x3F));
                i++;
            } else {
                bytes.push(0xE0 | (code >> 12), 0x80 | ((code >> 6) & 0x3F), 0x80 | (code & 0x3F));
            }
        }
        return bytes;
    }

    function fromUtf8(bytes) {
        var out = '';
        var i = 0;

        while (i < bytes.length) {
            var byte = bytes[i];
            var need = 0;
            var code = 0;

            if (byte < 0x80) { out += String.fromCharCode(byte); i++; continue; }
            if (byte >= 0xC2 && byte <= 0xDF) { need = 1; code = byte & 0x1F; }
            else if (byte >= 0xE0 && byte <= 0xEF) { need = 2; code = byte & 0x0F; }
            else if (byte >= 0xF0 && byte <= 0xF4) { need = 3; code = byte & 0x07; }
            else { i++; continue; } // orphaned continuation byte

            if (i + need > bytes.length) break; // the character was cut off

            var valid = true;
            for (var n = 1; n <= need; n++) {
                if (bytes[i + n] < 0x80 || bytes[i + n] > 0xBF) valid = false;
                code = (code << 6) | (bytes[i + n] & 0x3F);
            }
            if (!valid) { i++; continue; }

            if (code < 0x10000) {
                out += String.fromCharCode(code);
            } else {
                out += String.fromCharCode(0xD800 + ((code - 0x10000) >> 10), 0xDC00 + ((code - 0x10000) & 0x3FF));
            }
            i += need + 1;
        }

        return out;
    }

    // str_word_count(), which is not what it looks like: a word is a run of
    // locale letters, and an apostrophe or hyphen counts as part of that run
    // even when no letter is next to it. A leading ' or - and a trailing - are
    // cut off first, so a bare "-" between digits is itself a word. Non-ASCII
    // bytes are never letters in the C locale, so they break a run here too.
    function wordCount(str) {
        var head = 0;
        var tail = str.length;

        if (str.charAt(0) === '\'' || str.charAt(0) === '-') head = 1;
        if (str.charAt(tail - 1) === '-') tail--;

        var words = 0;
        var p = head;
        while (p < tail) {
            var start = p;
            while (p < tail && isWordLetter(str.charAt(p))) p++;
            if (p > start) words++;
            p++;
        }

        return words;
    }

    function isWordLetter(c) {
        return isAlpha(c) || c === '\'' || c === '-';
    }

    // \b at an offset: exactly one side is a word character.
    function isBoundary(str, at) {
        var before = at > 0 ? isWord(str.charAt(at - 1)) : false;
        var after = at < str.length ? isWord(str.charAt(at)) : false;
        return before !== after;
    }

    // Case-insensitive compare where only ASCII folds, like PCRE in byte mode.
    function equalsIgnoreCase(str, from, needle) {
        if (from + needle.length > str.length) return false;
        for (var i = 0; i < needle.length; i++) {
            var a = str.charCodeAt(from + i);
            var b = needle.charCodeAt(i);
            if (a >= 0x41 && a <= 0x5A) a += 32;
            if (b >= 0x41 && b <= 0x5A) b += 32;
            if (a !== b) return false;
        }
        return true;
    }

    // preg_match('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', $resume)
    //
    // Backtracking makes any dot inside the domain a candidate, so the pattern
    // holds when some dot in the run after @ is followed by two letters.
    function hasEmail(resume) {
        var at = resume.indexOf('@');
        while (at !== -1) {
            var start = at;
            while (start > 0 && isLocalChar(resume.charAt(start - 1))) start--;
            var end = at + 1;
            while (end < resume.length && isDomainChar(resume.charAt(end))) end++;

            if (at - start >= 1 && end - at >= 4) {
                for (var dot = at + 1; dot < end; dot++) {
                    if (resume.charAt(dot) !== '.') continue;
                    if (dot === at + 1) continue; // [a-z0-9.-]+ needs one character first
                    if (isAsciiLetter(resume.charAt(dot + 1)) && isAsciiLetter(resume.charAt(dot + 2))) return true;
                }
            }

            at = resume.indexOf('@', at + 1);
        }
        return false;
    }

    function isLocalChar(c) {
        return isAlpha(c) || isDigit(c) || c === '.' || c === '_' || c === '%' || c === '+' || c === '-';
    }

    function isDomainChar(c) {
        return isAlpha(c) || isDigit(c) || c === '.' || c === '-';
    }

    // preg_match('/(\+?\d[\d\s\-().]{7,})|linkedin\.com/i', $resume)
    function hasPhoneOrLinkedin(resume) {
        if (equalsIgnoreCaseSearch(resume, 'linkedin.com')) return true;

        for (var i = 0; i < resume.length; i++) {
            var at = i;
            if (resume.charAt(at) === '+') at++;
            if (at >= resume.length || !isDigit(resume.charAt(at))) continue;

            var run = 0;
            var j = at + 1;
            while (j < resume.length && isPhoneChar(resume.charAt(j))) { j++; run++; }
            if (run >= 7) return true;
        }

        return false;
    }

    function isPhoneChar(c) {
        return isDigit(c) || isSpace(c) || c === '-' || c === '(' || c === ')' || c === '.';
    }

    function equalsIgnoreCaseSearch(str, needle) {
        for (var i = 0; i + needle.length <= str.length; i++) {
            if (equalsIgnoreCase(str, i, needle)) return true;
        }
        return false;
    }

    // preg_match('/\b(photo|date of birth|marital status|religion|cnic|national id)\b/i', $resume)
    var NOISE_PHRASES = ['photo', 'date of birth', 'marital status', 'religion', 'cnic', 'national id'];

    function hasNoise(resume) {
        for (var i = 0; i < resume.length; i++) {
            for (var p = 0; p < NOISE_PHRASES.length; p++) {
                var phrase = NOISE_PHRASES[p];
                if (equalsIgnoreCase(resume, i, phrase)
                    && isBoundary(resume, i)
                    && isBoundary(resume, i + phrase.length)) return true;
            }
        }
        return false;
    }

    // preg_match_all('/^\s*[-•*·]\s+/m', $resume)
    //
    // Two PHP quirks are reproduced here. The delimiter convention is LF, so ^
    // only matches at offset 0 or right after a newline; and the pattern has no
    // /u flag, so the bullet character class is a set of bytes: a multi-byte
    // bullet can never be followed by a whitespace byte, so only the ASCII '-'
    // and '*' bullets are counted.
    function countBulletLines(resume) {
        var starts = [0];
        for (var n = 0; n < resume.length; n++) {
            if (resume.charAt(n) === '\n') starts.push(n + 1);
        }
        var matches = 0;
        var consumedTo = 0;
        for (var s = 0; s < starts.length; s++) {
            var at = starts[s];
            if (at < consumedTo) continue;
            var i = at;
            while (i < resume.length && isSpace(resume.charAt(i))) i++;
            var bullet = i < resume.length ? resume.charAt(i) : '';
            if (bullet !== '-' && bullet !== '*') continue;
            i++;
            var spaces = 0;
            while (i < resume.length && isSpace(resume.charAt(i))) { i++; spaces++; }
            if (spaces >= 1) {
                matches++;
                consumedTo = i;
            }
        }
        return matches;
    }

    // preg_match_all('/^.*\|.*\|.*$/m', $resume): one match per LF-delimited
    // segment holding at least two pipes.
    function countTableLines(resume) {
        var segments = resume.split('\n');
        var matches = 0;
        for (var i = 0; i < segments.length; i++) {
            if (substrCount(segments[i], '|') >= 2) matches++;
        }
        return matches;
    }

    // preg_match('/\b\d+(\.\d+)?%?\b/', $resume): true when the pattern can
    // match anywhere, with the same backtracking options PCRE tries.
    function hasNumber(resume) {
        for (var i = 0; i < resume.length; i++) {
            if (!isDigit(resume.charAt(i)) || !isBoundary(resume, i)) continue;

            var run = i;
            while (run < resume.length && isDigit(resume.charAt(run))) run++;

            for (var end = i + 1; end <= run; end++) {
                if (numberTail(resume, end)) return true;
                if (resume.charAt(end) !== '.' || !isDigit(resume.charAt(end + 1))) continue;

                var fraction = end + 2;
                while (fraction < resume.length && isDigit(resume.charAt(fraction))) fraction++;
                for (var tail = end + 2; tail <= fraction; tail++) {
                    if (numberTail(resume, tail)) return true;
                }
            }
        }
        return false;
    }

    function numberTail(resume, at) {
        if (isBoundary(resume, at)) return true; // the optional % is skipped
        return resume.charAt(at) === '%' && isBoundary(resume, at + 1);
    }

    // arsort() on an associative array: descending by value, and stable so ties
    // keep insertion order (PHP 8 sorts are stable).
    function Tally() {
        this.keys = [];
        this.counts = [];
    }

    Tally.prototype.add = function (key, amount) {
        var at = this.keys.indexOf(key);
        if (at === -1) {
            this.keys.push(key);
            this.counts.push(amount);
            return;
        }
        this.counts[at] += amount;
    };

    Tally.prototype.sorted = function () {
        var counts = this.counts;
        var order = [];
        for (var i = 0; i < this.keys.length; i++) order.push(i);
        order.sort(function (a, b) { return counts[b] - counts[a] || a - b; });
        var sorted = [];
        for (var s = 0; s < order.length; s++) {
            sorted.push({ key: this.keys[order[s]], freq: counts[order[s]] });
        }
        return sorted;
    };

    // array_unique(): first occurrence wins, then array_values reindexes.
    function unique(values) {
        var seen = {};
        var out = [];
        for (var i = 0; i < values.length; i++) {
            var token = '\u0001' + values[i];
            if (seen[token]) continue;
            seen[token] = true;
            out.push(values[i]);
        }
        return out;
    }

    function slice(values, offset, length) {
        if (length === undefined) return values.slice(offset);
        return values.slice(offset, offset + length);
    }

    function implodeGlue(glue, values) {
        var out = '';
        for (var i = 0; i < values.length; i++) out += (i ? glue : '') + values[i];
        return out;
    }

    // ------------------------------------------------------------------
    // Ported engine
    // ------------------------------------------------------------------

    // normalise(): lowercase, punctuation to spaces, collapse whitespace.
    //
    // PHP runs /[^a-z0-9+#.]+/ then /\s+/, both in byte mode with no /u flag.
    // A run of disallowed bytes becomes one space, and a multi-byte character
    // is entirely disallowed, so walking code points gives the same text. After
    // that first pass only single spaces remain, so the /\s+/ pass cannot
    // change anything.
    function normalise(text) {
        return trim(collapseRuns(lower(text), isNormalChar));
    }

    function isNormalChar(c) {
        return (c >= 'a' && c <= 'z') || (c >= '0' && c <= '9') || c === '+' || c === '#' || c === '.';
    }

    // The inverse of a bracketed class: every run of characters that fail the
    // test collapses to a single replacement space.
    function collapseRuns(value, keep) {
        var out = '';
        var i = 0;
        while (i < value.length) {
            if (keep(value.charAt(i))) {
                out += value.charAt(i);
                i++;
                continue;
            }
            while (i < value.length && !keep(value.charAt(i))) i++;
            out += ' ';
        }
        return out;
    }

    // stopWords()
    var STOP_WORDS = ('a about above after again against all am an and any are as at be because been before being below between both but by can could did do does doing down during each few for from further had has have having he her here hers herself him himself his how i if in into is it its itself just me more most my myself no nor not of off on once only or other our ours ourselves out over own same she should so some such than that the their theirs them themselves then there these they this those through to too under until up very was we were what when where which while who whom why will with would you your yours yourself yourselves also across using use used work working role team teams company job jobs candidate candidates ability able strong good great excellent required requirements responsibilities responsible experience years year including etc plus preferred must may well within per new help support ensure based like looking join us our we you will who what where when why how online apply position opportunity benefits salary month time full part')
        .split(' ')
        .concat(['remote', 'hybrid', 'office', 'manager', 'management', 'lead', 'senior', 'junior', 'entry', 'level', 'intern', 'internship', 'graduate', 'degree', 'bachelor', 'master', 'bachelors', 'masters', 'university', 'college', 'school', 'knowledge', 'understanding', 'familiarity', 'proficiency', 'plus', 'bonus', 'nice', 'must', 'have', 'has', 'with', 'and', 'need', 'needs', 'needed']);
    // The second half of the PHP list runs through array_filter(), which drops
    // falsy strings; there are none here, so the merge above is equivalent.

    function stopWords() {
        return STOP_WORDS;
    }

    // Generic words that must not become bigram keywords on their own.
    var GENERIC_WORDS = ['skill', 'skills', 'experience', 'knowledge', 'work', 'works', 'working', 'role', 'team', 'teams', 'environment', 'ability', 'requirements', 'responsibilities'];

    // extractKeywords()
    function extractKeywords(jobDescription) {
        var text = normalise(jobDescription);
        var raw = text.split(' ');
        var tokens = [];
        for (var i = 0; i < raw.length; i++) {
            var token = trimChars(raw[i], '.');
            if (token !== '') tokens.push(token);
        }

        var stop = stopWords();
        var unigrams = new Tally();
        var bigrams = new Tally();
        var count = tokens.length;

        for (var t = 0; t < count; t++) {
            var current = tokens[t];

            if (byteLength(current) < 3 || inArray(current, stop) || isAllDigits(current)) continue;

            unigrams.add(current, 1);

            if (t + 1 < count) {
                var next = tokens[t + 1];
                if (byteLength(next) >= 3 && !inArray(next, stop) && !inArray(next, GENERIC_WORDS) && !isAllDigits(next)) {
                    bigrams.add(current + ' ' + next, 1);
                }
            }
        }

        var sortedUnigrams = unigrams.sorted();
        var sortedBigrams = bigrams.sorted();

        var selected = [];

        var intro = ' ' + normalise(implodeGlue(' ', slice(splitBreaks(jobDescription), 0, 2))) + ' ';

        for (var b = 0; b < sortedBigrams.length && selected.length < 3; b++) {
            var pair = sortedBigrams[b].key;
            if (strContains(intro, ' ' + pair + ' ') && !inArray(pair, selected)) selected.push(pair);
        }

        for (var f = 0; f < sortedBigrams.length; f++) {
            if (sortedBigrams[f].freq >= 2 && selected.length < 8) selected.push(sortedBigrams[f].key);
        }

        for (var u = 0; u < sortedUnigrams.length; u++) {
            if (selected.length >= 16) break;

            var word = sortedUnigrams[u].key;
            var covered = false;

            for (var s = 0; s < selected.length; s++) {
                if (selected[s].indexOf(word) !== -1) {
                    covered = true;
                    break;
                }
            }

            if (!covered) selected.push(word);
        }

        if (selected.length === 0) {
            var keys = [];
            for (var k = 0; k < Math.min(10, unigrams.keys.length); k++) keys.push(unigrams.keys[k]);
            selected = keys;
        }

        return unique(selected);
    }

    function isAllDigits(token) {
        if (token === '') return false;
        for (var i = 0; i < token.length; i++) {
            if (!isDigit(token.charAt(i))) return false;
        }
        return true;
    }

    // keywordVariants()
    var VARIANT_GROUPS = [
        ['javascript', 'js'],
        ['typescript', 'ts'],
        ['power bi', 'powerbi', 'power-bi'],
        ['machine learning', 'ml'],
        ['artificial intelligence', 'ai'],
        ['communication', 'communicate', 'communicating', 'communicative'],
        ['analysis', 'analyse', 'analyze', 'analytical', 'analytics'],
        ['management', 'managing', 'manage', 'managerial'],
        ['development', 'developing', 'develop'],
        ['customer service', 'customer support', 'client support'],
        ['data cleaning', 'data cleansing', 'clean data'],
        ['human resources', 'hr'],
        ['quality assurance', 'qa'],
        ['search engine optimization', 'seo'],
        ['user experience', 'ux'],
        ['user interface', 'ui'],
        ['e-commerce', 'ecommerce'],
        ['onboarding', 'on boarding']
    ];

    function keywordVariants(keyword) {
        for (var g = 0; g < VARIANT_GROUPS.length; g++) {
            if (inArray(keyword, VARIANT_GROUPS[g])) {
                var out = [];
                for (var i = 0; i < VARIANT_GROUPS[g].length; i++) {
                    if (VARIANT_GROUPS[g][i] !== keyword) out.push(VARIANT_GROUPS[g][i]);
                }
                return out;
            }
        }
        return [];
    }

    // keywordMatch(): null means no match.
    function keywordMatch(keyword, paddedResume, resumeStems) {
        if (strContains(paddedResume, ' ' + keyword + ' ')) return 1.0;

        var variants = keywordVariants(keyword);
        for (var i = 0; i < variants.length; i++) {
            if (strContains(paddedResume, ' ' + variants[i] + ' ')) return 0.85;
        }

        if (keyword.indexOf(' ') === -1) {
            var stemmed = stem(keyword);
            if (stemmed !== '' && resumeStems['\u0001' + stemmed] === true) return 0.7;
        }

        return null;
    }

    // stem()
    var SUFFIXES = ['ing', 'ed', 'es', 's'];

    function stem(word) {
        var value = lower(word);
        for (var i = 0; i < SUFFIXES.length; i++) {
            var suffix = SUFFIXES[i];
            if (byteLength(value) > byteLength(suffix) + 3 && endsWith(value, suffix)) {
                return value.slice(0, value.length - suffix.length);
            }
        }
        return value;
    }

    function endsWith(value, suffix) {
        return suffix !== '' && value.slice(value.length - suffix.length) === suffix;
    }

    // detectJobTitle()
    function detectJobTitle(jobDescription) {
        var lines = splitBreaks(trim(jobDescription));
        var first = trim(lines.length ? lines[0] : '');

        if (first !== '' && wordCount(first) <= 8 && !endsWithPunctuation(first)) {
            return strLimit(first, 60, '');
        }

        var found = jobTitleInside(jobDescription);
        if (found !== null) return trim(strLimit(found, 60, ''));

        return first === '' ? '' : strLimit(first, 60, '');
    }

    function endsWithPunctuation(value) {
        var last = value.charAt(value.length - 1);
        return last === '.' || last === '!' || last === '?';
    }

    // preg_match('/(?:hiring|seeking|looking for|role|position)\s*(?:an?|:)?\s*([a-z][a-z0-9 ,\/-]{2,50})/i')
    //
    // Written as an explicit backtrack so the greedy quantifiers and the
    // optional article are tried in the same order PCRE tries them.
    var TITLE_LEADS = ['hiring', 'seeking', 'looking for', 'role', 'position'];

    function jobTitleInside(text) {
        for (var i = 0; i < text.length; i++) {
            for (var l = 0; l < TITLE_LEADS.length; l++) {
                if (!equalsIgnoreCase(text, i, TITLE_LEADS[l])) continue;
                var captured = matchAfterLead(text, i + TITLE_LEADS[l].length);
                if (captured !== null) return captured;
            }
        }
        return null;
    }

    function matchAfterLead(text, from) {
        var max = spaceRunEnd(text, from);
        for (var at = max; at >= from; at--) {
            var captured = matchArticleAndTitle(text, at);
            if (captured !== null) return captured;
        }
        return null;
    }

    function matchArticleAndTitle(text, at) {
        var options = [];
        var head = text.charAt(at);
        var next = text.charAt(at + 1);

        if (foldAsciiCode(head) === 'a') {
            if (foldAsciiCode(next) === 'n') options.push(at + 2); // an
            options.push(at + 1);                                  // a
        }
        if (head === ':') options.push(at + 1);
        options.push(at); // the group is optional

        for (var o = 0; o < options.length; o++) {
            var max = spaceRunEnd(text, options[o]);
            for (var b = max; b >= options[o]; b--) {
                var captured = matchTitleCapture(text, b);
                if (captured !== null) return captured;
            }
        }

        return null;
    }

    function matchTitleCapture(text, at) {
        var head = text.charAt(at);
        if (!isAsciiLetter(head)) return null;

        var limit = Math.min(text.length, at + 51);
        var end = at + 1;
        while (end < limit && isTitleChar(text.charAt(end))) end++;

        // {2,50} counts characters after the first one, and nothing follows the
        // group, so the longest run that is long enough is the match.
        if (end - at < 3) return null;
        return text.slice(at, end);
    }

    function spaceRunEnd(value, at) {
        var i = at;
        while (i < value.length && isSpace(value.charAt(i))) i++;
        return i;
    }

    function foldAsciiCode(c) {
        var code = c.charCodeAt(0);
        return String.fromCharCode(code >= 0x41 && code <= 0x5A ? code + 32 : code);
    }

    function isAsciiLetter(c) {
        return (c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z');
    }

    function isTitleChar(c) {
        return isAsciiLetter(c) || isDigit(c) || c === ' ' || c === ',' || c === '/' || c === '-';
    }

    // titleAppears()
    var TITLE_STOPS = ['and', 'the', 'for', 'with', 'job'];

    function titleAppears(title, lowerResume) {
        var clean = '';
        var value = lower(title);
        for (var i = 0; i < value.length; i++) {
            var c = value.charAt(i);
            clean += (isAsciiLetter(c) || isDigit(c) || c === ' ') ? c : ' ';
        }

        var parts = trim(clean).split(' ');
        var tokens = [];
        for (var p = 0; p < parts.length; p++) {
            var token = parts[p];
            if (byteLength(token) >= 3 && !inArray(token, TITLE_STOPS)) tokens.push(token);
        }

        if (tokens.length === 0) return true;

        var found = 0;
        for (var t = 0; t < tokens.length; t++) {
            if (lowerResume.indexOf(tokens[t]) !== -1) found++;
        }

        return found / tokens.length >= 0.7;
    }

    // structureChecks()
    function structureChecks(resume, title) {
        var target = title === undefined ? '' : title;
        var value = lower(resume);

        return [
            { label: 'Contact email', ok: hasEmail(resume), weight: 5, hint: 'Add a plain email address in the header, not inside an image.' },
            { label: 'Phone or LinkedIn', ok: hasPhoneOrLinkedin(resume), weight: 4, hint: 'Add a phone number or a LinkedIn URL in plain text.' },
            { label: 'Experience section', ok: containsAny(value, ['experience', 'employment', 'work history']), weight: 5, hint: 'Use the standard heading Experience.' },
            { label: 'Education section', ok: containsAny(value, ['education', 'qualification', 'academic']), weight: 3, hint: 'Add an Education section with degree and institution.' },
            { label: 'Skills section', ok: containsAny(value, ['skills', 'tools', 'technologies']), weight: 4, hint: 'Add a Skills block with the exact tool names from the posting.' },
            { label: 'Summary or objective', ok: containsAny(value, ['summary', 'profile', 'objective', 'about']), weight: 4, hint: 'Open with a three line summary aimed at this role.' },
            { label: 'Target title present', ok: target === '' || titleAppears(target, value), weight: 0, hint: 'Put the exact job title from the posting in your header or summary.' }
        ];
    }

    function containsAny(value, needles) {
        for (var i = 0; i < needles.length; i++) {
            if (value.indexOf(needles[i]) !== -1) return true;
        }
        return false;
    }

    // formatChecks()
    function formatChecks(resume) {
        var words = wordCount(resume);
        var bulletLines = countBulletLines(resume);
        var tableLines = countTableLines(resume);
        var noise = hasNoise(resume);

        return [
            { label: 'Readable length', ok: words >= 220 && words <= 900, weight: 8, hint: 'Aim for 220 to 900 words, one to two pages.' },
            { label: 'Bullet points used', ok: bulletLines >= 3, weight: 4, hint: 'Use simple dash bullets, at least three.' },
            { label: 'No table layout', ok: tableLines < 2, weight: 4, hint: 'Remove table and column layouts. Parsers read them out of order.' },
            { label: 'No personal noise', ok: !noise, weight: 4, hint: 'Remove photo, date of birth, marital status, religion, and ID numbers.' }
        ];
    }

    // atsFixes()
    function atsFixes(missing, structure, format, resume) {
        var fixes = [];

        if (missing.length !== 0) {
            fixes.push('Add these keywords where they are true: ' + implodeGlue(', ', slice(missing, 0, 6)) + '.');
        }

        var checks = structure.concat(format);
        for (var i = 0; i < checks.length; i++) {
            if (!checks[i].ok) fixes.push(checks[i].hint);
        }

        if (!hasNumber(resume)) {
            fixes.push('Add at least one number per role. Volume, users, marks, time saved, error rate.');
        }

        if (fixes.length === 0) {
            fixes.push('Score is healthy. Tailor the summary to this posting and re run before applying.');
        }

        return slice(unique(fixes), 0, 6);
    }

    // atsVerdict()
    function atsVerdict(score) {
        if (score >= 80) return 'Strong match';
        if (score >= 65) return 'Promising, needs tailoring';
        if (score >= 45) return 'Partial match';
        return 'Needs a rewrite';
    }

    // atsScore(): the local result, in the same key order as PHP.
    function atsScore(resume, jobDescription) {
        var resumeText = trim(String(resume === undefined || resume === null ? '' : resume));
        var description = trim(String(jobDescription === undefined || jobDescription === null ? '' : jobDescription));

        var keywords = extractKeywords(description);
        var title = detectJobTitle(description);
        var normalisedResume = normalise(resumeText);
        var paddedResume = ' ' + normalisedResume + ' ';

        var resumeStems = {};
        var tokens = normalisedResume.split(' ');
        for (var i = 0; i < tokens.length; i++) {
            // array_filter() drops falsy strings, so both '' and '0' go.
            if (tokens[i] === '' || tokens[i] === '0') continue;
            resumeStems['\u0001' + stem(tokens[i])] = true;
        }

        var matched = [];
        var missing = [];
        var weighted = 0.0;
        var repeated = [];

        for (var k = 0; k < keywords.length; k++) {
            var keyword = keywords[k];
            var match = keywordMatch(keyword, paddedResume, resumeStems);

            if (match === null) {
                missing.push(keyword);
                continue;
            }

            matched.push(keyword);
            weighted += match;

            if (substrCount(paddedResume, ' ' + keyword + ' ') > 6) repeated.push(keyword);
        }

        var keywordCount = Math.max(1, keywords.length);
        var coverage = weighted / keywordCount;
        var keywordScore = phpRound(55 * Math.min(1.0, coverage * 1.15));

        var structure = structureChecks(resumeText, title);
        var structureScore = 0;
        for (var s = 0; s < structure.length; s++) if (structure[s].ok) structureScore += structure[s].weight;

        var format = formatChecks(resumeText);
        var formatScore = 0;
        for (var f = 0; f < format.length; f++) if (format[f].ok) formatScore += format[f].weight;

        var total = Math.max(3, Math.min(98, keywordScore + structureScore + formatScore));

        var fixes = atsFixes(missing, structure, format, resumeText);

        if (repeated.length !== 0) {
            fixes.unshift('Keyword repeated too often: ' + implodeGlue(', ', slice(repeated, 0, 3)) + '. Keep each term to once or twice.');
            fixes = slice(unique(fixes), 0, 6);
        }

        return {
            score: total,
            verdict: atsVerdict(total),
            breakdown: [
                { label: 'Keyword coverage', score: keywordScore, max: 55, detail: matched.length + ' of ' + keywords.length + ' priority keywords found' },
                { label: 'Resume structure', score: structureScore, max: 25, detail: 'Standard sections a parser can label' },
                { label: 'Formatting hygiene', score: formatScore, max: 20, detail: 'Length, bullets, and parser safe layout' }
            ],
            title: title,
            title_match: title === '' || titleAppears(title, lower(resumeText)),
            matched: slice(matched, 0, 18),
            missing: slice(missing, 0, 18),
            structure: structure,
            format: format,
            fixes: fixes,
            source: 'local'
        };
    }

    window.EmplowiseToolkit.ats = {
        score: function (resume, jobDescription) {
            return atsScore(resume === undefined ? '' : resume, jobDescription === undefined ? '' : jobDescription);
        }
    };
})();

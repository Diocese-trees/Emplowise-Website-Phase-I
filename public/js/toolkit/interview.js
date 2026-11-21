/* Emplowise Student Hub: browser port of the deterministic interview engine in app/Services/StudentToolkit.php. */
(function () {
    'use strict';

    window.EmplowiseToolkit = window.EmplowiseToolkit || {};

    // -----------------------------------------------------------------
    // PHP primitives, matched byte for byte
    // -----------------------------------------------------------------

    // UTF-8 bytes of a string. PHP strings are byte buffers, so strlen,
    // isalpha and the word scanner below all work on these.
    function utf8Bytes(str) {
        var bytes = [];
        var i = 0;
        var n = str.length;

        while (i < n) {
            var cp = str.charCodeAt(i);

            if (cp >= 0xD800 && cp <= 0xDBFF && i + 1 < n) {
                var low = str.charCodeAt(i + 1);

                if (low >= 0xDC00 && low <= 0xDFFF) {
                    cp = 0x10000 + ((cp - 0xD800) << 10) + (low - 0xDC00);
                    i += 1;
                }
            }

            if (cp < 0x80) {
                bytes.push(cp);
            } else if (cp < 0x800) {
                bytes.push(0xC0 | (cp >> 6), 0x80 | (cp & 0x3F));
            } else if (cp < 0x10000) {
                bytes.push(0xE0 | (cp >> 12), 0x80 | ((cp >> 6) & 0x3F), 0x80 | (cp & 0x3F));
            } else {
                bytes.push(0xF0 | (cp >> 18), 0x80 | ((cp >> 12) & 0x3F), 0x80 | ((cp >> 6) & 0x3F), 0x80 | (cp & 0x3F));
            }

            i += 1;
        }

        return bytes;
    }

    // strlen(): byte length, not .length.
    function byteLength(str) {
        return utf8Bytes(str).length;
    }

    // PHP's default trim charlist is " \t\n\r\0\x0B". Form feed and non-breaking
    // space are deliberately kept, unlike String.prototype.trim().
    var TRIM_CHARS = ' \t\n\r\x0B\0';

    function isTrimByte(byte) {
        return TRIM_CHARS.indexOf(String.fromCharCode(byte)) !== -1;
    }

    function phpTrim(str) {
        return phpRtrim(phpLtrim(str));
    }

    function phpLtrim(str) {
        var i = 0;

        while (i < str.length && str.charCodeAt(i) < 0x80 && isTrimByte(str.charCodeAt(i))) {
            i += 1;
        }

        return str.slice(i);
    }

    function phpRtrim(str) {
        var i = str.length;

        while (i > 0 && str.charCodeAt(i - 1) < 0x80 && isTrimByte(str.charCodeAt(i - 1))) {
            i -= 1;
        }

        return str.slice(0, i);
    }

    // Str::lower() is mb_strtolower(..., 'UTF-8'): full Unicode simple case
    // mapping. JS toLowerCase() applies conditional special casing (Greek final
    // sigma), so the two can differ for that one script; every other supported
    // script maps identically.
    function strLower(str) {
        return str.toLowerCase();
    }

    // Str::contains(): case-sensitive substring test against any needle.
    function strContains(haystack, needles) {
        for (var i = 0; i < needles.length; i++) {
            if (haystack.indexOf(needles[i]) !== -1) {
                return true;
            }
        }

        return false;
    }

    // substr_count(): non-overlapping occurrences of an ASCII needle. Continuation
    // bytes are never < 0x80, so a byte scan and a code-unit scan agree.
    function substrCount(haystack, needle) {
        var count = 0;
        var at = haystack.indexOf(needle);

        while (at !== -1) {
            count += 1;
            at = haystack.indexOf(needle, at + needle.length);
        }

        return count;
    }

    // str_word_count() in the C locale: a word is a run of ASCII letters that may
    // also contain ' and -, the very first byte may not be ' or -, and the very
    // last byte may not be -.
    function isAlphaByte(byte) {
        return (byte >= 0x41 && byte <= 0x5A) || (byte >= 0x61 && byte <= 0x7A);
    }

    function strWordCount(str) {
        var bytes = utf8Bytes(str);
        var p = 0;
        var e = bytes.length;

        if (e === 0) {
            return 0;
        }

        if (bytes[0] === 0x27 || bytes[0] === 0x2D) {
            p += 1;
        }

        if (bytes[e - 1] === 0x2D) {
            e -= 1;
        }

        var count = 0;

        while (p < e) {
            var start = p;

            while (p < e && (isAlphaByte(bytes[p]) || bytes[p] === 0x27 || bytes[p] === 0x2D)) {
                p += 1;
            }

            if (p > start) {
                count += 1;
            }

            p += 1;
        }

        return count;
    }

    // preg_match_all() returns the count of non-overlapping whole matches.
    // The patterns below are ASCII-only, so PCRE's byte engine and the JS engine
    // see the same word boundaries (bytes >= 0x80 are non-word in both).
    function matchCount(pattern, subject) {
        var count = 0;
        var match;

        pattern.lastIndex = 0;
        match = pattern.exec(subject);

        while (match !== null) {
            count += 1;

            if (match[0] === '') {
                pattern.lastIndex += 1;
            }

            match = pattern.exec(subject);
        }

        return count;
    }

    // mb_strwidth(): PHP gives every code point display width 1 except the
    // East Asian Wide and Fullwidth ranges below, which are 2. There are no
    // zero-width code points in its table, not even combining marks or NUL.
    // Captured from the same PHP 8.3 build that generated the fixtures.
    var WIDE_RANGES = [
        0x1100, 0x115F, 0x231A, 0x231B, 0x2329, 0x232A, 0x23E9, 0x23EC,
        0x23F0, 0x23F0, 0x23F3, 0x23F3, 0x25FD, 0x25FE, 0x2614, 0x2615,
        0x2648, 0x2653, 0x267F, 0x267F, 0x2693, 0x2693, 0x26A1, 0x26A1,
        0x26AA, 0x26AB, 0x26BD, 0x26BE, 0x26C4, 0x26C5, 0x26CE, 0x26CE,
        0x26D4, 0x26D4, 0x26EA, 0x26EA, 0x26F2, 0x26F3, 0x26F5, 0x26F5,
        0x26FA, 0x26FA, 0x26FD, 0x26FD, 0x2705, 0x2705, 0x270A, 0x270B,
        0x2728, 0x2728, 0x274C, 0x274C, 0x274E, 0x274E, 0x2753, 0x2755,
        0x2757, 0x2757, 0x2795, 0x2797, 0x27B0, 0x27B0, 0x27BF, 0x27BF,
        0x2B1B, 0x2B1C, 0x2B50, 0x2B50, 0x2B55, 0x2B55, 0x2E80, 0x2E99,
        0x2E9B, 0x2EF3, 0x2F00, 0x2FD5, 0x2FF0, 0x2FFB, 0x3000, 0x303E,
        0x3041, 0x3096, 0x3099, 0x30FF, 0x3105, 0x312F, 0x3131, 0x318E,
        0x3190, 0x31E3, 0x31F0, 0x321E, 0x3220, 0x3247, 0x3250, 0x4DBF,
        0x4E00, 0xA48C, 0xA490, 0xA4C6, 0xA960, 0xA97C, 0xAC00, 0xD7A3,
        0xF900, 0xFAFF, 0xFE10, 0xFE19, 0xFE30, 0xFE52, 0xFE54, 0xFE66,
        0xFE68, 0xFE6B, 0xFF01, 0xFF60, 0xFFE0, 0xFFE6, 0x16FE0, 0x16FE4,
        0x16FF0, 0x16FF1, 0x17000, 0x187F7, 0x18800, 0x18CD5, 0x18D00, 0x18D08,
        0x1AFF0, 0x1AFF3, 0x1AFF5, 0x1AFFB, 0x1AFFD, 0x1AFFE, 0x1B000, 0x1B122,
        0x1B150, 0x1B152, 0x1B164, 0x1B167, 0x1B170, 0x1B2FB, 0x1F004, 0x1F004,
        0x1F0CF, 0x1F0CF, 0x1F18E, 0x1F18E, 0x1F191, 0x1F19A, 0x1F200, 0x1F202,
        0x1F210, 0x1F23B, 0x1F240, 0x1F248, 0x1F250, 0x1F251, 0x1F260, 0x1F265,
        0x1F300, 0x1F320, 0x1F32D, 0x1F335, 0x1F337, 0x1F37C, 0x1F37E, 0x1F393,
        0x1F3A0, 0x1F3CA, 0x1F3CF, 0x1F3D3, 0x1F3E0, 0x1F3F0, 0x1F3F4, 0x1F3F4,
        0x1F3F8, 0x1F43E, 0x1F440, 0x1F440, 0x1F442, 0x1F4FC, 0x1F4FF, 0x1F53D,
        0x1F54B, 0x1F54E, 0x1F550, 0x1F567, 0x1F57A, 0x1F57A, 0x1F595, 0x1F596,
        0x1F5A4, 0x1F5A4, 0x1F5FB, 0x1F64F, 0x1F680, 0x1F6C5, 0x1F6CC, 0x1F6CC,
        0x1F6D0, 0x1F6D2, 0x1F6D5, 0x1F6D7, 0x1F6DD, 0x1F6DF, 0x1F6EB, 0x1F6EC,
        0x1F6F4, 0x1F6FC, 0x1F7E0, 0x1F7EB, 0x1F7F0, 0x1F7F0, 0x1F90C, 0x1F93A,
        0x1F93C, 0x1F945, 0x1F947, 0x1F9FF, 0x1FA70, 0x1FA74, 0x1FA78, 0x1FA7C,
        0x1FA80, 0x1FA86, 0x1FA90, 0x1FAAC, 0x1FAB0, 0x1FABA, 0x1FAC0, 0x1FAC5,
        0x1FAD0, 0x1FAD9, 0x1FAE0, 0x1FAE7, 0x1FAF0, 0x1FAF6, 0x20000, 0x2FFFD,
        0x30000, 0x3FFFD
    ];

    function charWidth(cp) {
        var lo = 0;
        var hi = WIDE_RANGES.length / 2 - 1;

        while (lo <= hi) {
            var mid = (lo + hi) >> 1;
            var from = WIDE_RANGES[mid * 2];
            var to = WIDE_RANGES[mid * 2 + 1];

            if (cp < from) {
                hi = mid - 1;
            } else if (cp > to) {
                lo = mid + 1;
            } else {
                return 2;
            }
        }

        return 1;
    }

    function codePoints(str) {
        var out = [];
        var i = 0;

        while (i < str.length) {
            var cp = str.charCodeAt(i);

            if (cp >= 0xD800 && cp <= 0xDBFF && i + 1 < str.length) {
                var low = str.charCodeAt(i + 1);

                if (low >= 0xDC00 && low <= 0xDFFF) {
                    out.push(0x10000 + ((cp - 0xD800) << 10) + (low - 0xDC00));
                    i += 2;
                    continue;
                }
            }

            out.push(cp);
            i += 1;
        }

        return out;
    }

    function codePointToString(cp) {
        if (cp < 0x10000) {
            return String.fromCharCode(cp);
        }

        var high = 0xD800 + ((cp - 0x10000) >> 10);
        var low = 0xDC00 + ((cp - 0x10000) & 0x3FF);

        return String.fromCharCode(high, low);
    }

    // Str::limit($value, $limit, $end): return the value while its display width
    // fits, otherwise take the widest prefix inside the limit, rtrim it, append $end.
    function strLimit(value, limit, end) {
        var chars = codePoints(value);
        var total = 0;
        var i;

        for (i = 0; i < chars.length; i++) {
            total += charWidth(chars[i]);
        }

        if (total <= limit) {
            return value;
        }

        var width = 0;
        var kept = '';

        for (i = 0; i < chars.length; i++) {
            if (width + charWidth(chars[i]) > limit) {
                break;
            }

            width += charWidth(chars[i]);
            kept += codePointToString(chars[i]);
        }

        return phpRtrim(kept) + (typeof end === 'undefined' ? '...' : end);
    }

    // -----------------------------------------------------------------
    // Local engines
    // -----------------------------------------------------------------

    function roleFamily(role) {
        var lower = strLower(role);

        if (strContains(lower, ['software', 'engineer', 'developer', 'frontend', 'backend', 'full stack'])) {
            return 'engineering';
        }

        if (strContains(lower, ['data', 'analytic', 'machine learning', 'ai '])) {
            return 'data';
        }

        if (strContains(lower, ['product', 'project', 'program'])) {
            return 'product';
        }

        if (strContains(lower, ['marketing', 'content', 'brand', 'social'])) {
            return 'marketing';
        }

        if (strContains(lower, ['sales', 'business development', 'account'])) {
            return 'sales';
        }

        if (strContains(lower, ['finance', 'account', 'audit'])) {
            return 'finance';
        }

        if (strContains(lower, ['hr', 'people', 'talent', 'recruit'])) {
            return 'hr';
        }

        if (strContains(lower, ['operations', 'supply', 'logistics'])) {
            return 'operations';
        }

        return 'general';
    }

    function localQuestions(role, level) {
        var family = roleFamily(role);

        var banks = {
            engineering: [
                'Walk me through a project you built end to end. What broke, and how did you fix it?',
                'How do you decide when code is good enough to ship?',
                'Tell me about a bug that took you far longer than expected. What was the root cause?'
            ],
            data: [
                'Describe a dataset you cleaned. What was messy, and what did you change?',
                'How would you explain a metric that dropped 20 percent to a non technical manager?',
                'Tell me about an analysis that changed a decision. What was the decision?'
            ],
            product: [
                'Pick a product you use daily. What would you cut first, and why?',
                'How do you decide what goes into a first release?',
                'Tell me about a time you had to say no to a stakeholder.'
            ],
            marketing: [
                'Walk me through a campaign or content piece you ran. What did the numbers say?',
                'How would you find an audience for a product with no budget?',
                'Tell me about a piece of feedback that changed how you write.'
            ],
            sales: [
                'Sell me something you actually use. Keep it under 60 seconds.',
                'How do you handle a prospect who goes quiet after the first call?',
                'Tell me about a time you lost a deal. What did you change afterwards?'
            ],
            finance: [
                'Walk me through a spreadsheet or model you built. What was it for?',
                'How do you check your own work before it goes to a manager?',
                'Explain a financial concept you know well to someone outside finance.'
            ],
            hr: [
                'A manager wants to fire someone without documentation. What do you do?',
                'How would you run a first week onboarding for ten new hires?',
                'Tell me about a time you handled a confidential situation carefully.'
            ],
            operations: [
                'Describe a process you improved. What was the before and after?',
                'A supplier misses a delivery the day before launch. Walk me through your response.',
                'How do you keep track of ten moving tasks without dropping one?'
            ],
            general: [
                'Tell me about a project you are proud of. What was your specific contribution?',
                'Describe a time you received difficult feedback. What did you do with it?',
                'Why this role, and why now?'
            ]
        };

        var questions = banks[family].slice();

        if (level === 'Internship') {
            questions[2] = 'What do you want to learn in your first three months, and how would you measure it?';
        }

        if (level === 'Career switch') {
            questions[2] = 'You are changing fields. What from your previous work transfers directly to this role?';
        }

        return questions;
    }

    function modelAnswerTemplate(role, question) {
        return 'In my most recent ' + role + ' project, the situation was [one line of context]. I was asked to [specific task]. I [action one], then [action two], and finally [action three]. As a result, [outcome with one number if you have it]. The part I would repeat is [lesson].\n\nFor "' + strLimit(question, 90) + '", keep it to 90 seconds and finish on the result, not the setup.';
    }

    function verdict(score) {
        if (score >= 85) {
            return 'Hire signal';
        }

        if (score >= 70) {
            return 'Close, tighten the evidence';
        }

        if (score >= 55) {
            return 'Workable, needs structure';
        }

        return 'Rebuild the answer';
    }

    function localFeedback(role, level, question, answer) {
        var text = phpTrim(answer);
        var lower = strLower(text);
        var words = strWordCount(text);

        if (words < 8) {
            return {
                score: 18,
                verdict: 'Too thin to score',
                breakdown: [
                    { label: 'Structure', score: 6, max: 35, detail: 'No complete STAR sequence found' },
                    { label: 'Evidence', score: 3, max: 25, detail: 'No detail or numbers to weigh' },
                    { label: 'Clarity', score: 5, max: 25, detail: 'Too short to judge flow' },
                    { label: 'Ownership', score: 4, max: 15, detail: 'Your part is not visible yet' }
                ],
                strengths: ['You started the answer instead of stalling.'],
                fixes: [
                    'Write at least four sentences so an interviewer can follow your reasoning.',
                    'Use STAR: one line of situation, one of task, three of action, one of result.',
                    'Name the tools, people, or numbers involved. Specifics make it credible.'
                ],
                model_answer: modelAnswerTemplate(role, question),
                next_probe: 'Give one concrete example: where were you, what were you asked to do, and what did you do?'
            };
        }

        var hasSituation = strContains(lower, ['when i', 'at my', 'during', 'last year', 'in my', 'at university', 'on a project', 'we were']);
        var hasTask = strContains(lower, ['my task', 'i was asked', 'i had to', 'my job', 'the goal', 'responsible', 'needed to']);
        var hasAction = strContains(lower, ['i built', 'i led', 'i wrote', 'i organised', 'i organized', 'i analysed', 'i analyzed', 'i designed', 'i ran', 'i fixed', 'i created', 'i coordinated', 'i tested', 'i presented', 'first', 'then', 'so i']);
        var hasResult = strContains(lower, ['result', 'outcome', 'as a result', 'which led', 'improved', 'reduced', 'increased', 'delivered', 'learned', 'so the']);

        var numbers = matchCount(/\b\d+(\.\d+)?%?\b/g, text);
        var filler = matchCount(/\b(um|uh|like|basically|honestly|kind of|sort of|stuff)\b/g, lower);
        var firstPerson = substrCount(lower, ' i ');

        var structure = (hasSituation ? 9 : 3) + (hasTask ? 8 : 2) + (hasAction ? 10 : 3) + (hasResult ? 8 : 2);
        var evidence = Math.min(25, (numbers > 0 ? 15 : 4) + (byteLength(text) > 240 ? 10 : 5));
        var clarity = words >= 70 && words <= 230 ? 25 : (words < 70 ? 14 : 17);
        clarity -= Math.min(8, filler * 2);
        clarity = Math.max(4, clarity);
        var ownership = Math.min(15, 5 + firstPerson * 2);

        var score = Math.max(20, Math.min(96, structure + evidence + clarity + ownership));

        var missingParts = [];

        if (!hasSituation) {
            missingParts.push('situation');
        }

        if (!hasTask) {
            missingParts.push('task');
        }

        if (!hasAction) {
            missingParts.push('action');
        }

        if (!hasResult) {
            missingParts.push('result');
        }

        var nextProbe;

        if (!hasResult) {
            nextProbe = 'What changed after you acted? Name one result, even a small one.';
        } else if (!hasAction) {
            nextProbe = 'What three actions did you take, in order? Say what you did, not what the team did.';
        } else if (!hasTask) {
            nextProbe = 'What were you specifically asked to do, and what was the deadline or constraint?';
        } else if (!hasSituation) {
            nextProbe = 'Where and when did this happen? Give one line of context before the task.';
        } else if (numbers === 0) {
            nextProbe = 'What number proves the result? Users, hours saved, marks, headcount, or error rate.';
        } else {
            nextProbe = 'Record a 30 second version. Keep the result last and cut the setup.';
        }

        var breakdown = [
            {
                label: 'Structure',
                score: structure,
                max: 35,
                detail: missingParts.length === 0 ? 'Situation, task, action, and result all present' : 'Missing ' + missingParts.join(', ')
            },
            {
                label: 'Evidence',
                score: evidence,
                max: 25,
                detail: numbers > 0 ? numbers + ' number' + (numbers === 1 ? '' : 's') + ' used' : 'No numbers found'
            },
            {
                label: 'Clarity',
                score: clarity,
                max: 25,
                detail: words + ' words' + (filler > 0 ? ', ' + filler + ' filler word' + (filler === 1 ? '' : 's') : '')
            },
            {
                label: 'Ownership',
                score: ownership,
                max: 15,
                detail: firstPerson === 0 ? 'No first-person reference' : firstPerson + ' first-person reference' + (firstPerson === 1 ? '' : 's')
            }
        ];

        var strengths = [];
        var fixes = [];

        if (hasSituation) {
            strengths.push('You set a concrete scene before explaining the work. Interviewers can follow you.');
        } else {
            fixes.push('Open with one line of context. Where were you, what was the project, and when.');
        }

        if (hasAction) {
            strengths.push('Your actions are visible. You described what you did, not just what the team did.');
        } else {
            fixes.push('Name three specific actions you took. Verbs like built, tested, rewrote, and presented beat "worked on".');
        }

        if (hasResult) {
            strengths.push('You closed the loop with an outcome. That is what separates an answer from an anecdote.');
        } else {
            fixes.push('End with the result. If you have no metric, state what changed for the team or the user.');
        }

        if (numbers > 0) {
            strengths.push('You used numbers. Quantified answers are easier to trust.');
        } else {
            fixes.push('Add one number, even small. Users, days saved, error rate, marks, or headcount.');
        }

        if (words > 260) {
            fixes.push('Tighten to 90 seconds. Cut setup and keep the decision, the action, and the result.');
        }

        if (filler > 2) {
            fixes.push('Cut filler words. Say the point, then the evidence.');
        }

        if (firstPerson === 0) {
            fixes.push('Say "I" at least twice so your contribution is clear inside the team story.');
        }

        if (strengths.length === 0) {
            strengths.push('You answered directly without dodging the question.');
        }

        if (fixes.length === 0) {
            fixes.push('Strong answer. Now practise a 30 second version for follow ups.');
        }

        return {
            score: score,
            verdict: verdict(score),
            breakdown: breakdown,
            strengths: strengths.slice(0, 3),
            fixes: fixes.slice(0, 3),
            model_answer: modelAnswerTemplate(role, question),
            next_probe: nextProbe
        };
    }

    // -----------------------------------------------------------------
    // Public surface
    // -----------------------------------------------------------------

    window.EmplowiseToolkit.interview = {
        questions: function (role, level) {
            return { questions: localQuestions(role, level), source: 'local' };
        },

        feedback: function (role, level, question, answer) {
            var local = localFeedback(role, level, question, answer);
            var out = {};
            var key;

            for (key in local) {
                if (Object.prototype.hasOwnProperty.call(local, key)) {
                    out[key] = local[key];
                }
            }

            out.source = 'local';

            return out;
        }
    };
})();

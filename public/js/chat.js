/* Emplowise assistant: grounded chat widget with graceful local fallback. */
(function () {
    'use strict';

    const root = document.getElementById('chat-root');
    const panel = document.getElementById('chat-panel');
    const launcher = document.getElementById('chat-launcher');
    const closeBtn = document.getElementById('chat-close');
    const log = document.getElementById('chat-log');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send');
    const sendLabel = sendBtn && sendBtn.textContent.trim() ? sendBtn.textContent.trim() : 'Send';
    const quick = document.getElementById('chat-quick');
    const suggestionsEl = document.getElementById('chat-suggestions');
    const resetBtn = document.getElementById('chat-reset');

    if (!root || !panel || !launcher || !log || !form || !input) return;

    const endpoint = root.getAttribute('data-endpoint');
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

    // Local knowledge base mirrors the server-side rules. It keeps the widget
    // fully usable on static hosts, in the offline demo, or if the API is down.
    const KB = {
        pricing: 'Starter is PKR 40,000 a month for up to 20 employees.\nBusiness is PKR 75,000 a month for up to 50.\nEnterprise is custom above 50.\nExact inclusions are confirmed in writing after the free HR audit, so the audit scopes them.',
        audit: 'The Free HR Audit is the front door.\nFill the form at /free-hr-audit and pick a morning, afternoon, or evening slot.\nWe map payroll, performance, and policy gaps before quoting anything.\nNo cost, no obligation.',
        student: 'EmploStart is free and no account is needed.\nInside: mock interviews, an ATS score out of 100, resume builder, profile optimiser, and 14 prompts.\nOpen /emplostart and pick a tool.',
        tools: 'The ATS tool scores your resume against a job description out of 100.\nThe resume builder and profile optimiser rewrite weak bullets.\nThe prompt library holds 14 ready prompts.\nAll free at /emplostart, and nothing you paste is stored.',
        payroll: 'Payroll, leave and attendance, onboarding, and employee records are confirmed services.\nTax, statutory contributions, and disbursement scope are confirmed in writing after the free HR audit.\nSo we confirm those before work starts.',
        performance: 'Performance support, conflict guidance, organisational assessments, and monthly HR reporting are confirmed.\nAssessment cadence is confirmed in writing after the free HR audit.\nThe review cycle stays yours, we run the structure.',
        policy: 'Policies and SOPs, plus legal and compliance support, are confirmed services.\nWe coordinate compliance, we do not promise legal outcomes.\nJurisdiction coverage is confirmed in writing after the free HR audit.',
        services: 'Three pillars hold fourteen confirmed services: Payroll, Performance Management, and Policy Making.\nScope runs from recruitment and onboarding to payroll, compliance, performance, records, reporting, and a dedicated HR rep.\nAll of it is delivered remote.',
        pending: 'Some details are confirmed in writing after the free HR audit: inclusions, recruitment fees, payroll tax scope, legal coverage, rep allocation, cadence, contract terms, and direct contact.\nWe list them as open rather than guessing.\nThe audit pins each one down.',
        contact: 'The Free HR Audit form is the only verified contact channel right now.\nDirect phone and inbox are confirmed in writing after the free HR audit.\nUse /free-hr-audit and we reply from there.',
        careers: 'Open roles are not published yet, so nothing is listed on this site.\nIf you want help getting hired, the EmploStart tools are free at /emplostart.',
        why: 'We sit between informal help and a full enterprise HR department.\nConfirmed structure, published prices, and a named HR representative.\nEvery process you get is documented, not improvised.\nWe do not promise legal outcomes or invented savings.',
        who: 'Emplowise HR Services does remote HR outsourcing for startups and SMEs in Pakistan.\nConsultants backed by specialist support, plus a dedicated HR representative.\nDirect access to your CEO or founder is part of the model.',
        where: 'We are based in Pakistan and deliver fully remote.\nThere is no walk-in office in this build.\nInternational expansion is planned long term, not live yet.',
        when: 'The method runs Assess, Identify, Improve, Review.\nReview means monthly reporting with a quarterly assessment.\nStart dates and contract length are confirmed in writing after the free HR audit.',
        greeting: 'hey. I am the Emplowise assistant.\nAsk about packages, confirmed services, the free audit, or the EmploStart.\nI keep it short and never invent numbers.',
        thanks: 'Anytime.\nIf you want a real scope, the audit at /free-hr-audit is the next step.',
        general: 'Emplowise runs remote HR for growing teams in Pakistan.\nAsk about pricing, services, the free audit, or the EmploStart.\nIf it is not in our verified data, I point you to the free audit.'
    };

    // Scoring mirrors EmplowiseAssistant::detectIntent: all rules are scored,
    // single words match on a word boundary, multi-word phrases score double,
    // a close tie falls back to the general intent, and the previous turn
    // breaks a tie. Keep this list in step with the PHP service.
    const RULES = {
        thanks: ['thanks', 'thank you', 'shukriya', 'bye', 'goodbye'],
        pricing: ['price', 'pricing', 'cost', 'pkr', 'package', 'packages', 'starter', 'business', 'enterprise', 'how much', 'budget', 'charge', 'quote', 'fee', 'per month'],
        pending: ['pending', 'tbd', 'not disclosed', 'not published', 'unclear', 'not confirmed'],
        audit: ['audit', 'free hr', 'free audit', 'book', 'meeting', 'talk to', 'sign up', 'onboard me', 'consultation', 'get started'],
        student: ['student', 'student hub', 'career hub', 'emplostart', 'emplo start', 'internship', 'graduate', 'fresh grad', 'prompt library', 'linkedin'],
        tools: ['resume', 'cv', 'ats', 'interview', 'cover letter'],
        payroll: ['payroll', 'salary', 'tax', 'attendance', 'leave', 'onboarding', 'records', 'document'],
        performance: ['performance', 'review', 'appraisal', 'conflict', 'manager', 'kpi', 'okr'],
        policy: ['policy', 'policies', 'sop', 'compliance', 'legal', 'contract', 'labour', 'labor'],
        services: ['service', 'services', 'what do you do', 'what you do', 'recruit', 'hr support', 'offer'],
        contact: ['contact', 'email', 'phone', 'whatsapp', 'reach you', 'number', 'inbox', 'address', 'office'],
        careers: ['job', 'career', 'hiring me', 'apply', 'vacancy', 'open role'],
        why: ['why', 'different', 'better than', 'vs', 'instead of', 'in-house', 'in house'],
        who: ['who are you', 'who is', 'about you', 'about emplowise', 'what is emplowise', 'your team', 'founder', 'ceo', 'company'],
        where: ['where', 'location', 'based', 'city', 'onsite', 'on-site', 'pakistan', 'remote', 'international'],
        when: ['when', 'timeline', 'how soon', 'start date', 'how long', 'duration', 'notice period']
    };

    const GREETINGS = ['hi', 'hello', 'hey', 'salam', 'assalam', 'yo', 'sup'];

    function matchersFor(phrases) {
        return phrases.map(function (phrase) {
            const escaped = phrase.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return {
                phrase: phrase,
                pattern: phrase.indexOf(' ') === -1
                    ? new RegExp('\\b' + escaped + '\\b')
                    : new RegExp(escaped)
            };
        });
    }

    const COMPILED = {};
    Object.keys(RULES).forEach(function (intent) {
        COMPILED[intent] = matchersFor(RULES[intent]);
    });
    const COMPILED_GREETINGS = matchersFor(GREETINGS);

    function localIntent(text, previous) {
        const lower = String(text || '').trim().toLowerCase();
        const wordCount = lower.split(/\s+/).filter(function (word) { return word.length > 0; }).length;
        const scores = {};
        const order = [];

        const score = function (intent, matchers) {
            let value = 0;
            matchers.forEach(function (matcher) {
                if (matcher.pattern.test(lower)) {
                    value += matcher.phrase.indexOf(' ') === -1 ? 1 : 2;
                }
            });
            if (value > 0) {
                scores[intent] = value;
                order.push(intent);
            }
        };

        Object.keys(COMPILED).forEach(function (intent) { score(intent, COMPILED[intent]); });
        if (wordCount <= 5) score('greeting', COMPILED_GREETINGS);

        const known = Object.prototype.hasOwnProperty.call(RULES, previous || '')
            || (previous === 'greeting' && wordCount <= 5);
        if (previous && known) {
            scores[previous] = (scores[previous] || 0) + (wordCount <= 8 ? 0.9 : 0.4);
            if (order.indexOf(previous) === -1) order.push(previous);
        }

        if (order.length === 0) return 'general';

        const sorted = order.slice().sort(function (a, b) {
            if (scores[b] !== scores[a]) return scores[b] - scores[a];
            return order.indexOf(a) - order.indexOf(b);
        });
        const top = scores[sorted[0]];
        const second = sorted.length > 1 ? scores[sorted[1]] : 0;

        if (second > 0 && top - second < 0.5) {
            if (previous && scores[previous] === top) return previous;
            return 'general';
        }

        return sorted[0];
    }

    function localReply(text, previous) {
        return KB[localIntent(text, previous)] || KB.general;
    }

    // Answer from the bundled knowledge base. Used when there is no endpoint
    // (static host) or the endpoint fails, so the conversation keeps its shape.
    function replyLocally(message) {
        const intent = localIntent(message, previousIntent);
        const reply = clamp(KB[intent] || KB.general);
        previousIntent = intent;
        append('assistant', reply);
        renderSuggestions(localSuggestions(intent));
        history.push({ role: 'user', content: message });
        history.push({ role: 'assistant', content: reply });
        history = history.slice(-8);
    }

    const LOCAL_SUGGESTIONS = {
        pricing: [
            ['What is included', 'What is included in the packages?'],
            ['Free audit', 'How does the free HR audit work?'],
            ['Services', 'Which HR services do you run?']
        ],
        student: [
            ['ATS score', 'How does the ATS score work?'],
            ['Mock interview', 'How does the mock interview tool work?'],
            ['Resume builder', 'Is the resume builder free?']
        ],
        tools: [
            ['EmploStart', 'What is in the EmploStart?'],
            ['Prompt library', 'What is in the prompt library?'],
            ['Profile help', 'Can you help with my LinkedIn profile?']
        ],
        general: [
            ['Pricing', 'What do the packages cost?'],
            ['Services', 'Which HR services do you run?'],
            ['Free audit', 'How does the free HR audit work?']
        ]
    };

    function localSuggestions(intent) {
        return LOCAL_SUGGESTIONS[intent] || LOCAL_SUGGESTIONS.general;
    }

    // Trailing newline-free trim plus a five line ceiling on any reply.
    function clamp(text) {
        const lines = String(text || '').split('\n').map(function (l) { return l.trim(); }).filter(Boolean);
        return lines.slice(0, 5).join('\n');
    }

    // Session storage can throw in private modes and framed contexts; the
    // widget must stay usable when it does.
    function readSession() {
        try {
            return window.sessionStorage.getItem('emplowise_chat_session') || '';
        } catch (error) {
            return '';
        }
    }

    function writeSession(value) {
        try {
            window.sessionStorage.setItem('emplowise_chat_session', value);
        } catch (error) {
            /* not fatal */
        }
    }

    const introNode = log.querySelector('.msg');
    const introText = introNode ? introNode.textContent : '';

    let sessionId = readSession();
    let history = [];
    let previousIntent = '';
    let sending = false;
    let typingEl = null;

    const compact = window.matchMedia('(max-width: 520px)');

    function setOpen(open) {
        panel.hidden = !open;
        launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            if (compact.matches && window.Emplowise && window.Emplowise.lockScroll) {
                window.Emplowise.lockScroll('chat');
            }
            window.setTimeout(function () { input.focus(); }, 60);
            scrollDown();
        } else if (window.Emplowise && window.Emplowise.unlockScroll) {
            window.Emplowise.unlockScroll('chat');
        }
    }

    launcher.addEventListener('click', function () {
        setOpen(panel.hidden);
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            setOpen(false);
            launcher.focus({ preventScroll: true });
        });
    }

    document.addEventListener('keydown', function (event) {
        if (panel.hidden) return;

        if (event.key === 'Escape') {
            setOpen(false);
            launcher.focus({ preventScroll: true });
            return;
        }

        if (event.key !== 'Tab') return;

        // The panel is a modal dialog, so Tab has to stay inside it.
        const items = Array.prototype.filter.call(
            panel.querySelectorAll('button:not([disabled]), a[href], input, textarea, select, [tabindex]:not([tabindex="-1"])'),
            function (el) { return el.offsetParent !== null || el === document.activeElement; }
        );

        if (items.length === 0) return;

        const first = items[0];
        const last = items[items.length - 1];
        const active = document.activeElement;

        if (event.shiftKey) {
            if (active === first || !panel.contains(active)) {
                event.preventDefault();
                last.focus();
            }
            return;
        }

        if (active === last) {
            event.preventDefault();
            first.focus();
        }
    });

    // Click away to dismiss the panel, returning focus to the launcher.
    document.addEventListener('click', function (event) {
        if (panel.hidden) return;
        if (panel.contains(event.target) || launcher.contains(event.target)) return;
        setOpen(false);
        launcher.focus({ preventScroll: true });
    });

    function scrollDown() {
        log.scrollTop = log.scrollHeight;
    }

    function linkify(text, node) {
        const pattern = /(\/free-hr-audit|\/emplostart)/g;
        let last = 0;
        let match = pattern.exec(text);

        while (match !== null) {
            if (match.index > last) {
                node.appendChild(document.createTextNode(text.slice(last, match.index)));
            }
            const link = document.createElement('a');
            link.href = match[1];
            link.textContent = match[1];
            link.className = 'chat-link';
            node.appendChild(link);
            last = match.index + match[1].length;
            match = pattern.exec(text);
        }

        if (last < text.length) {
            node.appendChild(document.createTextNode(text.slice(last)));
        }
    }

    function append(role, text) {
        const bubble = document.createElement('div');
        bubble.className = 'msg' + (role === 'user' ? ' msg--user' : '');
        if (role === 'assistant') {
            linkify(String(text || ''), bubble);
        } else {
            bubble.textContent = text;
        }
        log.appendChild(bubble);
        scrollDown();
        return bubble;
    }

    function renderSuggestions(items) {
        if (!suggestionsEl) return;
        suggestionsEl.innerHTML = '';
        (items || []).slice(0, 4).forEach(function (item) {
            // Server replies send {label, query}; the local table uses pairs.
            const label = Array.isArray(item) ? item[0] : item && item.label;
            const query = Array.isArray(item) ? item[1] : item && item.query;
            if (!label || !query) return;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'quick-chip';
            button.setAttribute('data-q', query);
            button.textContent = label;
            suggestionsEl.appendChild(button);
        });
    }

    function showTyping() {
        if (typingEl) return;
        typingEl = document.createElement('div');
        typingEl.className = 'msg msg--typing';
        typingEl.setAttribute('aria-hidden', 'true');
        for (let i = 0; i < 3; i += 1) {
            typingEl.appendChild(document.createElement('i'));
        }
        log.appendChild(typingEl);
        scrollDown();
    }

    function hideTyping() {
        if (!typingEl) return;
        typingEl.remove();
        typingEl = null;
    }

    function setBusy(busy) {
        sending = busy;
        if (sendBtn) {
            sendBtn.disabled = busy;
            sendBtn.textContent = busy ? '…' : sendLabel;
        }
    }

    function sendMessage(text) {
        const message = (text || '').trim();
        if (sending) return;
        if (message.length < 2) {
            append('assistant', 'Give me a little more than that. Two words minimum.');
            return;
        }
        if (message.length > 800) {
            append('assistant', 'That is over the 800 character limit. Trim it and try again.');
            return;
        }

        append('user', message);
        input.value = '';
        showTyping();
        setBusy(true);

        // A static build has no chat endpoint; skip the request entirely.
        if (!endpoint) {
            hideTyping();
            replyLocally(message);
            setBusy(false);
            input.focus();
            return;
        }

        const payload = {
            message: message,
            session_id: sessionId || null,
            previous_intent: previousIntent || null,
            history: history.slice(-8)
        };

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        })
            .then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (body) {
                    return { ok: response.ok, status: response.status, body: body };
                });
            })
            .then(function (result) {
                hideTyping();
                if (!result.ok) {
                    if (result.status === 429) {
                        append('assistant', 'Slow down a second. Too many messages at once. Try again in a minute.');
                    } else if (result.status === 422) {
                        append('assistant', 'That message did not pass validation. Keep it under 800 characters and try again.');
                    } else if (result.status === 419) {
                        append('assistant', 'Your session expired. Reload the page and send that again.');
                    } else if (result.status >= 500 || result.status === 404 || result.status === 405 || result.status === 501) {
                        replyLocally(message);
                    } else {
                        append('assistant', 'That request did not go through. Try once more.');
                    }
                    return;
                }

                if (result.body.session_id) {
                    sessionId = result.body.session_id;
                    writeSession(sessionId);
                }

                previousIntent = result.body.intent || localIntent(message);
                append('assistant', clamp(result.body.reply || localReply(message)));
                renderSuggestions(result.body.suggestions || localSuggestions(previousIntent));
                history.push({ role: 'user', content: message });
                history.push({ role: 'assistant', content: result.body.reply || '' });
                history = history.slice(-8);
            })
            .catch(function () {
                hideTyping();
                replyLocally(message);
            })
            .finally(function () {
                setBusy(false);
                input.focus();
            });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        sendMessage(input.value);
    });

    if (quick) {
        quick.addEventListener('click', function (event) {
            const chip = event.target.closest('[data-q]');
            if (!chip) return;
            sendMessage(chip.getAttribute('data-q'));
        });
    }

    if (suggestionsEl) {
        suggestionsEl.addEventListener('click', function (event) {
            const chip = event.target.closest('[data-q]');
            if (!chip) return;
            // Sending replaces the chip, which would make the click-away
            // handler below see a detached node and close the panel.
            event.stopPropagation();
            sendMessage(chip.getAttribute('data-q'));
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            log.innerHTML = '';
            if (introText) {
                const intro = document.createElement('div');
                intro.className = 'msg';
                intro.textContent = introText;
                log.appendChild(intro);
            }
            history = [];
            previousIntent = '';
            sessionId = '';
            writeSession('');
            renderSuggestions([]);
            input.value = '';
            input.focus();
            scrollDown();
        });
    }

    // Read-only seam for the routing parity harness. Nothing depends on it.
    window.EmplowiseChat = { intent: localIntent, reply: localReply };
})();

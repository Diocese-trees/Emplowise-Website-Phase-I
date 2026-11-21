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

    if (!root || !panel || !launcher || !log || !form || !input) return;

    const endpoint = root.getAttribute('data-endpoint');
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

    // Local knowledge base mirrors the server-side rules. It keeps the widget
    // fully usable on static hosts, in the offline demo, or if the API is down.
    const KB = {
        pricing: 'Starter is PKR 40,000 a month for up to 20 employees.\nBusiness is PKR 75,000 a month for up to 50.\nEnterprise is custom above 50.\nExact inclusions are Pending Input, so the audit scopes them.',
        legacy: 'The 2025 Replit export listed Foundation at PKR 40,000, Growth at PKR 65,000, and Scale at PKR 100,000.\nThose tiers are not sold on this site.\nCurrent packages are Starter, Business, and Enterprise.',
        audit: 'The Free HR Audit is the front door.\nFill the form at /free-hr-audit and pick a morning, afternoon, or evening slot.\nWe map payroll, performance, and policy gaps before quoting anything.\nNo cost, no obligation.',
        student: 'Student Hub is free and no account is needed.\nInside: mock interviews, an ATS score out of 100, resume builder, profile optimiser, and 14 prompts.\nOpen /student-hub and pick a tool.',
        tools: 'The ATS tool scores your resume against a job description out of 100.\nThe resume builder and profile optimiser rewrite weak bullets.\nThe prompt library holds 14 ready prompts.\nAll free at /student-hub.',
        payroll: 'Payroll, leave and attendance, onboarding, and employee records are confirmed services.\nTax, statutory contributions, and disbursement scope are Pending Input.\nSo we confirm those in writing before work starts.',
        performance: 'Performance support, conflict guidance, organisational assessments, and monthly HR reporting are confirmed.\nAssessment cadence is Pending Input.\nThe review cycle stays yours, we run the structure.',
        policy: 'Policies and SOPs, plus legal and compliance support, are confirmed services.\nWe coordinate compliance, we do not promise legal outcomes.\nJurisdiction coverage is Pending Input.',
        services: 'Confirmed scope: recruitment, onboarding, records, leave and attendance, policies, payroll, compliance support, performance, employee relations, reporting, consultation, assessments, and a dedicated HR rep.\nAll of it is delivered remote.',
        pending: 'Pending Input means not confirmed yet: inclusions, recruitment fees, payroll tax scope, legal coverage, rep allocation, cadence, contract terms, and direct contact.\nWe publish them as open rather than guessing.\nThe audit pins each one down in writing.',
        contact: 'The Free HR Audit form is the only verified contact channel right now.\nDirect phone and inbox are Pending Input.\nUse /free-hr-audit and we reply from there.',
        careers: 'Open roles are Pending Input and nothing is published on this site yet.\nIf you want help getting hired, the Student Hub tools are free at /student-hub.',
        why: 'We sit between informal help and a full enterprise HR department.\nConfirmed structure, published prices, and a named HR representative.\nEvery process you get is documented, not improvised.\nWe do not promise legal outcomes or invented savings.',
        who: 'Emplowise HR Services does remote HR outsourcing for startups and SMEs in Pakistan.\nConsultants backed by specialist support, plus a dedicated HR representative.\nDirect access to your CEO or founder is part of the model.',
        where: 'We are based in Pakistan and deliver fully remote.\nThere is no walk-in office in this build.\nInternational expansion is planned long term, not live yet.',
        when: 'The method runs Assess, Identify, Improve, Review.\nReview means monthly reporting with a quarterly assessment.\nStart dates and contract length are Pending Input.',
        greeting: 'hey. I am the Emplowise assistant.\nAsk about packages, confirmed services, the free audit, or the Student Hub.\nI keep it short and never invent numbers.',
        thanks: 'Anytime.\nIf you want a real scope, the audit at /free-hr-audit is the next step.',
        general: 'Emplowise runs remote HR for growing teams in Pakistan.\nAsk about pricing, services, the free audit, or the Student Hub.\nIf it is not in our verified data, I say Pending Input.'
    };

    const RULES = [
        [/thanks|thank you|shukriya|bye|goodbye/, 'thanks'],
        [/price|cost|pkr|package|starter|business|enterprise|how much|budget|charge|quote|fee/, 'pricing'],
        [/foundation|growth|scale|legacy|replit|old tier/, 'legacy'],
        [/pending input|pending|tbd|not disclosed|unclear|not confirmed/, 'pending'],
        [/audit|free hr|book|meeting|talk to|sign up|onboard me|consultation|get started/, 'audit'],
        [/student|internship|graduate|fresh grad|prompt library|linkedin/, 'student'],
        [/resume|cv|ats|interview|cover letter/, 'tools'],
        [/payroll|salary|tax|attendance|leave|onboarding|records|document/, 'payroll'],
        [/performance|review|appraisal|conflict|manager|kpi|okr/, 'performance'],
        [/policy|policies|sop|compliance|legal|contract|labour|labor/, 'policy'],
        [/service|services|what do you do|what you do|recruit|hr support|offer/, 'services'],
        [/contact|email|phone|whatsapp|reach you|number|inbox|address|office/, 'contact'],
        [/job|career|hiring me|apply|vacancy|open role/, 'careers'],
        [/why|different|better than|instead of|in-house|in house|whey/, 'why'],
        [/who are you|who is|about you|about emplowise|what is emplowise|your team|founder|ceo|company/, 'who'],
        [/where|location|based|city|onsite|on-site|pakistan|international/, 'where'],
        [/when|timeline|how soon|start date|how long|duration|notice period/, 'when'],
        [/\b(hi|hello|hey|salam|assalam|yo|sup)\b/, 'greeting']
    ];

    function localReply(text) {
        const lower = String(text || '').toLowerCase();
        for (let i = 0; i < RULES.length; i += 1) {
            if (RULES[i][0].test(lower)) return KB[RULES[i][1]];
        }
        return KB.general;
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

    let sessionId = readSession();
    let history = [];
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
        if (event.key === 'Escape' && !panel.hidden) {
            setOpen(false);
            launcher.focus({ preventScroll: true });
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

    function append(role, text) {
        const bubble = document.createElement('div');
        bubble.className = 'msg' + (role === 'user' ? ' msg--user' : '');
        bubble.textContent = text;
        log.appendChild(bubble);
        scrollDown();
        return bubble;
    }

    function showTyping() {
        if (typingEl) return;
        typingEl = document.createElement('div');
        typingEl.className = 'msg msg--typing';
        typingEl.setAttribute('aria-label', 'Assistant is typing');
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

        const payload = {
            message: message,
            session_id: sessionId || null,
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
                    } else if (result.status >= 500) {
                        append('assistant', clamp(localReply(message)));
                    } else {
                        append('assistant', 'That request did not go through. Try once more.');
                    }
                    return;
                }

                if (result.body.session_id) {
                    sessionId = result.body.session_id;
                    writeSession(sessionId);
                }

                append('assistant', clamp(result.body.reply || localReply(message)));
                history.push({ role: 'user', content: message });
                history.push({ role: 'assistant', content: result.body.reply || '' });
                history = history.slice(-8);
            })
            .catch(function () {
                hideTyping();
                append('assistant', clamp(localReply(message)));
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
})();

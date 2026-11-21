/* Emplowise Student Hub: five free tools powered by local engines and optional Alibaba Cloud AI. */
(function () {
    'use strict';

    const config = document.getElementById('student-config');
    if (!config) return;

    const endpoints = {
        questions: config.getAttribute('data-interview-questions'),
        feedback: config.getAttribute('data-interview-feedback'),
        ats: config.getAttribute('data-ats'),
        profile: config.getAttribute('data-profile')
    };

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';
    const toast = (window.Emplowise && window.Emplowise.toast) ? window.Emplowise.toast : function () {};
    const copy = (window.Emplowise && window.Emplowise.copy) ? window.Emplowise.copy : function () { return Promise.resolve(false); };
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const RING = 2 * Math.PI * 64;

    function el(id) { return document.getElementById(id); }

    function setStatus(node, message, type) {
        if (!node) return;
        node.classList.remove('is-error', 'is-ok');
        if (type) node.classList.add(type);
        node.textContent = message || '';
    }

    function postJson(url, payload) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        }).then(function (response) {
            return response.json().catch(function () { return {}; }).then(function (body) {
                return { ok: response.ok, status: response.status, body: body };
            });
        });
    }

    function firstError(body) {
        if (!body) return '';
        if (body.errors) {
            const keys = Object.keys(body.errors);
            if (keys.length) {
                const value = body.errors[keys[0]];
                return Array.isArray(value) ? value[0] : String(value);
            }
        }
        // Static previews answer 503 with a human message; surface it verbatim.
        if (typeof body.message === 'string' && body.message.trim()) return body.message.trim();
        return '';
    }

    function setRing(circle, score) {
        if (!circle) return;
        circle.style.strokeDasharray = String(RING);
        circle.style.strokeDashoffset = String(RING * (1 - Math.max(0, Math.min(100, score)) / 100));
    }

    function sourceLabel(source) {
        return source && source.indexOf('model:') === 0 ? 'Alibaba Cloud AI' : 'Local engine';
    }

    function fillList(node, items) {
        if (!node) return;
        node.innerHTML = '';
        (items || []).forEach(function (text) {
            const li = document.createElement('li');
            li.textContent = text;
            node.appendChild(li);
        });
    }

    // -----------------------------------------------------------------
    // Tabs
    // -----------------------------------------------------------------
    const tabs = Array.prototype.slice.call(document.querySelectorAll('[data-hub-tab]'));
    const panels = Array.prototype.slice.call(document.querySelectorAll('[data-hub-panel]'));

    function revealTab(tab) {
        const rail = tab.parentElement;
        if (!rail || rail.scrollWidth <= rail.clientWidth) return;
        const railRect = rail.getBoundingClientRect();
        const tabRect = tab.getBoundingClientRect();
        const left = rail.scrollLeft + (tabRect.left - railRect.left) - (railRect.width - tabRect.width) / 2;
        if (rail.scrollTo) {
            rail.scrollTo({ left: Math.max(0, left), behavior: reducedMotion ? 'auto' : 'smooth' });
        } else {
            rail.scrollLeft = Math.max(0, left);
        }
    }

    function activateTab(id) {
        if (!id) return;
        const target = panels.filter(function (panel) { return panel.getAttribute('data-hub-panel') === id; })[0];
        if (!target) return;
        tabs.forEach(function (tab) {
            const active = tab.getAttribute('data-hub-tab') === id;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
            if (active) revealTab(tab);
        });
        panels.forEach(function (panel) {
            panel.classList.toggle('is-active', panel === target);
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const id = tab.getAttribute('data-hub-tab');
            activateTab(id);
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '#' + id);
            }
            // On narrow screens the rail wraps above the panel, so bring the
            // newly selected panel into view instead of leaving it offscreen.
            if (window.innerWidth <= 760) {
                const panel = document.querySelector('[data-hub-panel="' + id + '"]');
                if (panel) panel.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
            }
        });
    });

    if (tabs.length) {
        const hash = (window.location.hash || '').replace('#', '');
        if (hash && panels.some(function (panel) { return panel.getAttribute('data-hub-panel') === hash; })) {
            activateTab(hash);
            const shell = document.querySelector('.hub-shell');
            if (shell) {
                window.setTimeout(function () {
                    shell.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
                }, 80);
            }
        }
    }

    // -----------------------------------------------------------------
    // 01 Interview simulator
    // -----------------------------------------------------------------
    const interviewStart = el('interview-start');
    if (interviewStart) {
        const roleSelect = el('interview-role');
        const levelSelect = el('interview-level');
        const stage = el('interview-stage');
        const progress = el('interview-progress');
        const questionEl = el('interview-question');
        const answerEl = el('interview-answer');
        const counter = el('interview-counter');
        const submitBtn = el('interview-submit');
        const status = el('interview-status');
        const sourceEl = el('interview-source');
        const feedback = el('interview-feedback');
        const scoreEl = el('interview-score');
        const ringEl = el('interview-ring');
        const verdictEl = el('interview-verdict');
        const strengthsEl = el('interview-strengths');
        const fixesEl = el('interview-fixes');
        const modelEl = el('interview-model');
        const probeEl = el('interview-probe');
        const barsEl = el('interview-bars');
        const nextBtn = el('interview-next');

        const state = { questions: [], index: 0, busy: false, role: '', level: '' };

        function buildProgress() {
            if (!progress) return;
            progress.innerHTML = '';
            state.questions.forEach(function () {
                progress.appendChild(document.createElement('span'));
            });
        }

        function paintProgress() {
            if (!progress) return;
            Array.prototype.slice.call(progress.children).forEach(function (span, index) {
                span.className = index < state.index ? 'is-done' : (index === state.index ? 'is-current' : '');
            });
        }

        function renderQuestion() {
            if (!state.questions.length) return;
            const question = state.questions[state.index];
            if (questionEl) questionEl.textContent = question;
            if (answerEl) answerEl.value = '';
            if (counter) counter.textContent = '0 characters';
            if (feedback) feedback.hidden = true;
            if (submitBtn) submitBtn.disabled = false;
            if (stage) stage.hidden = false;
            paintProgress();
            if (answerEl) answerEl.focus();
        }

        function renderBars(rows) {
            if (!barsEl) return;
            barsEl.innerHTML = '';
            (rows || []).forEach(function (row) {
                const max = Number(row.max) > 0 ? Number(row.max) : 1;
                const pct = Math.round((Number(row.score) || 0) / max * 100);
                const wrap = document.createElement('div');
                wrap.className = 'bar-row';
                wrap.setAttribute('role', 'img');
                wrap.setAttribute('aria-label', row.label + ': ' + row.score + ' of ' + row.max + '. ' + (row.detail || ''));

                const head = document.createElement('div');
                head.className = 'bar-head';
                const label = document.createElement('span');
                label.textContent = row.label;
                const score = document.createElement('span');
                score.textContent = row.score + ' / ' + row.max;
                head.appendChild(label);
                head.appendChild(score);

                const bar = document.createElement('div');
                bar.className = 'bar' + (pct < 50 ? ' is-low' : (pct < 75 ? ' is-mid' : ''));
                const fill = document.createElement('span');
                fill.style.width = pct + '%';
                bar.appendChild(fill);

                const detail = document.createElement('p');
                detail.className = 'small muted bar-detail';
                detail.textContent = row.detail || '';
                wrap.appendChild(head);
                wrap.appendChild(bar);
                wrap.appendChild(detail);
                barsEl.appendChild(wrap);
            });
        }

        function renderFeedback(data) {
            if (feedback) feedback.hidden = false;
            if (scoreEl) scoreEl.textContent = String(data.score);
            setRing(ringEl, data.score);
            if (verdictEl) verdictEl.textContent = data.verdict || '';
            renderBars(data.breakdown);
            fillList(strengthsEl, data.strengths);
            fillList(fixesEl, data.fixes);
            if (modelEl) {
                const span = modelEl.querySelector('span');
                if (span) span.textContent = data.model_answer || '';
            }
            if (probeEl) {
                const span = probeEl.querySelector('span');
                if (span) span.textContent = data.next_probe || '';
                probeEl.hidden = !data.next_probe;
            }
            if (nextBtn) {
                const last = state.index >= state.questions.length - 1;
                nextBtn.textContent = last ? 'Start a new set' : 'Next question';
            }
            if (sourceEl) sourceEl.textContent = sourceLabel(data.source);
            if (feedback) {
                feedback.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'nearest' });
            }
        }

        function startSet() {
            if (state.busy) return;
            state.busy = true;
            state.role = roleSelect ? roleSelect.value : '';
            state.level = levelSelect ? levelSelect.value : '';
            setStatus(status, 'Preparing questions…');
            interviewStart.disabled = true;

            postJson(endpoints.questions, { role: state.role, level: state.level })
                .then(function (result) {
                    if (!result.ok) {
                        setStatus(status, firstError(result.body) || 'Could not prepare questions. Try again.', 'is-error');
                        return;
                    }
                    state.questions = result.body.questions || [];
                    state.index = 0;
                    if (!state.questions.length) {
                        setStatus(status, 'No questions came back. Try a different role.', 'is-error');
                        return;
                    }
                    buildProgress();
                    renderQuestion();
                    setStatus(status, '');
                    if (sourceEl) sourceEl.textContent = sourceLabel(result.body.source);
                })
                .catch(function () {
                    setStatus(status, 'Network error while preparing questions.', 'is-error');
                })
                .finally(function () {
                    state.busy = false;
                    interviewStart.disabled = false;
                });
        }

        interviewStart.addEventListener('click', startSet);

        if (answerEl && counter) {
            answerEl.addEventListener('input', function () {
                counter.textContent = answerEl.value.length + ' characters';
            });
        }

        if (submitBtn) {
            submitBtn.addEventListener('click', function () {
                if (state.busy || !state.questions.length) return;
                const answer = answerEl ? (answerEl.value || '').trim() : '';
                if (answer.length < 10) {
                    setStatus(status, 'Write at least one full sentence before scoring.', 'is-error');
                    if (answerEl) answerEl.focus();
                    return;
                }
                if (answer.length > 5000) {
                    setStatus(status, 'Keep the answer under 5000 characters.', 'is-error');
                    return;
                }

                state.busy = true;
                submitBtn.disabled = true;
                setStatus(status, 'Scoring your answer…');

                postJson(endpoints.feedback, {
                    role: state.role,
                    level: state.level,
                    question: state.questions[state.index],
                    answer: answer
                })
                    .then(function (result) {
                        if (!result.ok) {
                            setStatus(status, firstError(result.body) || 'Could not score that answer.', 'is-error');
                            return;
                        }
                        setStatus(status, '');
                        renderFeedback(result.body);
                    })
                    .catch(function () {
                        setStatus(status, 'Network error while scoring.', 'is-error');
                    })
                    .finally(function () {
                        state.busy = false;
                        submitBtn.disabled = false;
                    });
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (state.index >= state.questions.length - 1) {
                    startSet();
                    return;
                }
                state.index += 1;
                renderQuestion();
            });
        }
    }

    // -----------------------------------------------------------------
    // 02 ATS scoring
    // -----------------------------------------------------------------
    const atsForm = el('ats-form');
    if (atsForm) {
        const resumeEl = el('ats-resume');
        const jdEl = el('ats-jd');
        const status = el('ats-status');
        const submitBtn = el('ats-submit');
        const submitLabel = submitBtn && submitBtn.textContent.trim() ? submitBtn.textContent.trim() : 'Score my resume';
        const resultPanel = el('ats-result');
        let busy = false;

        function renderChecklist(node, checks) {
            if (!node) return;
            node.innerHTML = '';
            (checks || []).forEach(function (check) {
                const li = document.createElement('li');
                li.className = check.ok ? '' : 'is-fail';
                li.textContent = check.ok ? check.label : check.label + ' — ' + check.hint;
                node.appendChild(li);
            });
        }

        function renderChips(node, values, missing) {
            if (!node) return;
            node.innerHTML = '';
            if (!values || !values.length) {
                const empty = document.createElement('span');
                empty.className = 'small muted';
                empty.textContent = missing ? 'No missing keywords detected.' : 'No matched keywords detected.';
                node.appendChild(empty);
                return;
            }
            values.forEach(function (value) {
                const chip = document.createElement('span');
                chip.className = 'chip' + (missing ? ' chip--miss' : ' chip--teal');
                chip.textContent = value;
                node.appendChild(chip);
            });
        }

        function renderAts(data) {
            if (el('ats-score')) el('ats-score').textContent = String(data.score);
            setRing(el('ats-ring'), data.score);
            if (el('ats-verdict')) el('ats-verdict').textContent = data.verdict || '';

            const bars = el('ats-bars');
            if (bars) {
                bars.innerHTML = '';
                (data.breakdown || []).forEach(function (row) {
                    const max = Number(row.max) > 0 ? Number(row.max) : 1;
                    const pct = Math.round((Number(row.score) || 0) / max * 100);
                    const wrap = document.createElement('div');
                    wrap.className = 'bar-row';

                    const head = document.createElement('div');
                    head.className = 'bar-head';
                    const label = document.createElement('span');
                    label.textContent = row.label;
                    const score = document.createElement('span');
                    score.textContent = row.score + ' / ' + row.max;
                    head.appendChild(label);
                    head.appendChild(score);

                    const bar = document.createElement('div');
                    bar.className = 'bar' + (pct < 50 ? ' is-low' : (pct < 75 ? ' is-mid' : ''));
                    const fill = document.createElement('span');
                    fill.style.width = pct + '%';
                    bar.appendChild(fill);

                    const detail = document.createElement('p');
                    detail.className = 'small muted bar-detail';
                    detail.textContent = row.detail;
                    wrap.appendChild(head);
                    wrap.appendChild(bar);
                    wrap.appendChild(detail);
                    bars.appendChild(wrap);
                });
            }

            renderChips(el('ats-matched'), data.matched, false);
            renderChips(el('ats-missing'), data.missing, true);
            renderChecklist(el('ats-structure'), data.structure);
            renderChecklist(el('ats-format'), data.format);
            fillList(el('ats-fixes'), data.fixes);

            const editsBlock = el('ats-edits-block');
            if (editsBlock) {
                if (data.ai_edits && data.ai_edits.length) {
                    editsBlock.hidden = false;
                    fillList(el('ats-edits'), data.ai_edits);
                } else {
                    editsBlock.hidden = true;
                }
            }

            if (resultPanel) {
                resultPanel.hidden = false;
                resultPanel.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
            }
        }

        atsForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (busy) return;
            const resume = resumeEl ? (resumeEl.value || '').trim() : '';
            const jd = jdEl ? (jdEl.value || '').trim() : '';

            if (resume.length < 60) {
                setStatus(status, 'Paste a fuller resume. At least 60 characters.', 'is-error');
                if (resumeEl) resumeEl.focus();
                return;
            }
            if (jd.length < 60) {
                setStatus(status, 'Paste the full job description. At least 60 characters.', 'is-error');
                if (jdEl) jdEl.focus();
                return;
            }

            busy = true;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Scoring…';
            }
            setStatus(status, 'Scoring the match…');

            postJson(endpoints.ats, { resume: resume, job_description: jd })
                .then(function (result) {
                    if (!result.ok) {
                        setStatus(status, firstError(result.body) || 'Could not score this resume.', 'is-error');
                        return;
                    }
                    setStatus(status, '');
                    renderAts(result.body);
                })
                .catch(function () {
                    setStatus(status, 'Network error while scoring.', 'is-error');
                })
                .finally(function () {
                    busy = false;
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = submitLabel;
                    }
                });
        });
    }

    // -----------------------------------------------------------------
    // 03 Resume builder
    // -----------------------------------------------------------------
    const resumeForm = el('resume-form');
    if (resumeForm) {
        const expList = el('exp-list');
        const eduList = el('edu-list');
        const saveStatus = el('resume-save');
        const DRAFT_KEY = 'emplowise_resume_draft_v1';
        let saveTimer = null;

        function expTemplate() {
            const item = document.createElement('div');
            item.className = 'repeat-item';
            item.innerHTML =
                '<button type="button" class="remove-btn" aria-label="Remove this role">&times;</button>' +
                '<div class="repeat-grid">' +
                    '<input type="text" class="exp-role" placeholder="Role, e.g. Data Analyst Intern" maxlength="100">' +
                    '<input type="text" class="exp-company" placeholder="Company" maxlength="100">' +
                '</div>' +
                '<input type="text" class="exp-dates" placeholder="Jun 2025 to Aug 2025" maxlength="60">' +
                '<textarea class="exp-bullets" placeholder="One bullet per line. Start with an action verb." maxlength="1200"></textarea>';
            return item;
        }

        function eduTemplate() {
            const item = document.createElement('div');
            item.className = 'repeat-item';
            item.innerHTML =
                '<button type="button" class="remove-btn" aria-label="Remove this education entry">&times;</button>' +
                '<div class="repeat-grid">' +
                    '<input type="text" class="edu-degree" placeholder="Degree, e.g. BS Computer Science" maxlength="100">' +
                    '<input type="text" class="edu-school" placeholder="Institution" maxlength="100">' +
                '</div>' +
                '<input type="text" class="edu-dates" placeholder="2022 to 2026" maxlength="60">' +
                '<input type="text" class="edu-detail" placeholder="Relevant coursework or honours (optional)" maxlength="160">';
            return item;
        }

        function addExp(row) {
            if (!expList) return;
            const item = expTemplate();
            const data = row || {};
            const fields = { '.exp-role': data.role, '.exp-company': data.company, '.exp-dates': data.dates, '.exp-bullets': data.bullets };
            Object.keys(fields).forEach(function (selector) {
                const input = item.querySelector(selector);
                if (input && fields[selector]) input.value = fields[selector];
            });
            expList.appendChild(item);
            renderResume();
        }

        function addEdu(row) {
            if (!eduList) return;
            const item = eduTemplate();
            const data = row || {};
            const fields = { '.edu-degree': data.degree, '.edu-school': data.school, '.edu-dates': data.dates, '.edu-detail': data.detail };
            Object.keys(fields).forEach(function (selector) {
                const input = item.querySelector(selector);
                if (input && fields[selector]) input.value = fields[selector];
            });
            eduList.appendChild(item);
            renderResume();
        }

        const addExpBtn = el('add-exp');
        const addEduBtn = el('add-edu');
        if (addExpBtn) addExpBtn.addEventListener('click', function () { addExp(); scheduleSave(); });
        if (addEduBtn) addEduBtn.addEventListener('click', function () { addEdu(); scheduleSave(); });

        [expList, eduList].forEach(function (list) {
            if (!list) return;
            list.addEventListener('click', function (event) {
                const remove = event.target.closest('.remove-btn');
                if (!remove) return;
                const item = remove.closest('.repeat-item');
                if (item) item.remove();
                renderResume();
                scheduleSave();
            });
        });

        function value(name) {
            const input = resumeForm.querySelector('[data-rp="' + name + '"]');
            return input ? input.value.trim() : '';
        }

        function contactLine() {
            return [value('email'), value('phone'), value('location'), value('linkedin')]
                .filter(function (part) { return part !== ''; })
                .join(' · ');
        }

        function expData() {
            if (!expList) return [];
            return Array.prototype.slice.call(expList.querySelectorAll('.repeat-item')).map(function (item) {
                const role = item.querySelector('.exp-role');
                const company = item.querySelector('.exp-company');
                const dates = item.querySelector('.exp-dates');
                const bullets = item.querySelector('.exp-bullets');
                return {
                    role: role ? (role.value || '').trim() : '',
                    company: company ? (company.value || '').trim() : '',
                    dates: dates ? (dates.value || '').trim() : '',
                    bullets: bullets ? (bullets.value || '').trim() : ''
                };
            });
        }

        function eduData() {
            if (!eduList) return [];
            return Array.prototype.slice.call(eduList.querySelectorAll('.repeat-item')).map(function (item) {
                const degree = item.querySelector('.edu-degree');
                const school = item.querySelector('.edu-school');
                const dates = item.querySelector('.edu-dates');
                const detail = item.querySelector('.edu-detail');
                return {
                    degree: degree ? (degree.value || '').trim() : '',
                    school: school ? (school.value || '').trim() : '',
                    dates: dates ? (dates.value || '').trim() : '',
                    detail: detail ? (detail.value || '').trim() : ''
                };
            });
        }

        function collectDraft() {
            return {
                fields: {
                    name: value('name'),
                    title: value('title'),
                    email: value('email'),
                    phone: value('phone'),
                    location: value('location'),
                    linkedin: value('linkedin'),
                    summary: value('summary'),
                    skills: value('skills')
                },
                experience: expData(),
                education: eduData()
            };
        }

        function hasContent(draft) {
            if (!draft) return false;
            const fields = draft.fields || {};
            const filled = Object.keys(fields).some(function (key) { return (fields[key] || '').trim() !== ''; });
            const rows = (draft.experience || []).concat(draft.education || []);
            const rowData = rows.some(function (row) {
                return Object.keys(row).some(function (key) { return (row[key] || '').trim() !== ''; });
            });
            return filled || rowData;
        }

        function persist() {
            try {
                window.localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft()));
                if (saveStatus) saveStatus.textContent = 'Saved in this browser.';
            } catch (error) {
                if (saveStatus) saveStatus.textContent = 'Local saving is blocked here. Copy or download before you leave.';
            }
        }

        function scheduleSave() {
            window.clearTimeout(saveTimer);
            saveTimer = window.setTimeout(persist, 400);
        }

        function readDraft() {
            try {
                const raw = window.localStorage.getItem(DRAFT_KEY);
                return raw ? JSON.parse(raw) : null;
            } catch (error) {
                return null;
            }
        }

        function clearRows() {
            if (expList) expList.innerHTML = '';
            if (eduList) eduList.innerHTML = '';
        }

        function applyDraft(draft) {
            if (!draft) return false;

            const fields = draft.fields || {};
            Object.keys(fields).forEach(function (name) {
                const input = resumeForm.querySelector('[data-rp="' + name + '"]');
                if (input) input.value = fields[name] || '';
            });

            clearRows();
            (draft.experience || []).forEach(function (row) { addExp(row); });
            (draft.education || []).forEach(function (row) { addEdu(row); });
            if (!draft.experience || !draft.experience.length) addExp();
            if (!draft.education || !draft.education.length) addEdu();
            renderResume();

            return true;
        }

        const EXAMPLE = {
            fields: {
                name: 'Ayesha Khan',
                title: 'Data Analyst',
                email: 'ayesha@example.com',
                phone: '+92 300 1234567',
                location: 'Karachi, Pakistan',
                linkedin: 'linkedin.com/in/ayesha',
                summary: 'Data analyst with hands on SQL, Excel, and Python work. I clean messy data, document the method, and turn findings into decisions a team can act on. Looking for an analyst role with clear ownership.',
                skills: 'SQL, Python, Excel, Power BI, data cleaning, stakeholder communication'
            },
            experience: [
                {
                    role: 'Data Analyst Intern',
                    company: 'Northline',
                    dates: 'Jun 2025 to Aug 2025',
                    bullets: "Built SQL reports for three teams, cutting manual reporting by 6 hours a week\nCleaned a 40,000 row dataset and wrote the data dictionary\nPresented weekly findings to two product managers"
                }
            ],
            education: [
                {
                    degree: 'BS Computer Science',
                    school: 'IBA Karachi',
                    dates: '2022 to 2026',
                    detail: 'Coursework: databases, statistics, information systems'
                }
            ]
        };

        function renderResume() {
            const name = value('name') || 'Your Name';
            const title = value('title') || 'Target title';
            const contact = contactLine();

            if (el('rp-name')) el('rp-name').textContent = name;
            if (el('rp-title')) el('rp-title').textContent = title;
            if (el('rp-contact')) el('rp-contact').textContent = contact || 'email · phone · location · LinkedIn';

            const summary = value('summary');
            if (el('rp-summary')) {
                el('rp-summary').textContent = summary || 'Add a three line summary.';
                el('rp-summary').classList.toggle('rp-empty', !summary);
            }

            const expBox = el('rp-experience');
            if (expBox) {
                expBox.innerHTML = '';
                const roles = expData().filter(function (row) {
                    return row.role || row.company || row.bullets;
                });
                if (!roles.length) {
                    expBox.innerHTML = '<p class="rp-empty">Add at least one role.</p>';
                } else {
                    roles.forEach(function (row) {
                        const entry = document.createElement('div');
                        entry.className = 'rp-entry';

                        const head = document.createElement('div');
                        head.className = 'rp-entry-head';
                        const left = document.createElement('span');
                        left.textContent = [row.role, row.company].filter(Boolean).join(' — ');
                        const right = document.createElement('span');
                        right.textContent = row.dates;
                        head.appendChild(left);
                        head.appendChild(right);
                        entry.appendChild(head);

                        if (row.bullets) {
                            const p = document.createElement('p');
                            p.textContent = row.bullets.split(/\r?\n/).map(function (line) {
                                return line.trim();
                            }).filter(Boolean).map(function (line) {
                                return line.charAt(0) === '-' ? line : '- ' + line;
                            }).join('\n');
                            entry.appendChild(p);
                        }

                        expBox.appendChild(entry);
                    });
                }
            }

            const eduBox = el('rp-education');
            if (eduBox) {
                eduBox.innerHTML = '';
                const schools = eduData().filter(function (row) {
                    return row.degree || row.school || row.detail;
                });
                if (!schools.length) {
                    eduBox.innerHTML = '<p class="rp-empty">Add your degree or qualification.</p>';
                } else {
                    schools.forEach(function (row) {
                        const entry = document.createElement('div');
                        entry.className = 'rp-entry';

                        const head = document.createElement('div');
                        head.className = 'rp-entry-head';
                        const left = document.createElement('span');
                        left.textContent = [row.degree, row.school].filter(Boolean).join(' — ');
                        const right = document.createElement('span');
                        right.textContent = row.dates;
                        head.appendChild(left);
                        head.appendChild(right);
                        entry.appendChild(head);

                        if (row.detail) {
                            const p = document.createElement('p');
                            p.textContent = row.detail;
                            entry.appendChild(p);
                        }

                        eduBox.appendChild(entry);
                    });
                }
            }

            const skills = value('skills');
            if (el('rp-skills')) {
                el('rp-skills').textContent = skills || 'Add skills.';
                el('rp-skills').classList.toggle('rp-empty', !skills);
            }
        }

        function plainText() {
            const lines = [];
            const name = value('name') || 'Your Name';
            const title = value('title');
            lines.push(name);
            if (title) lines.push(title);
            const contact = contactLine();
            if (contact) lines.push(contact);
            lines.push('');

            if (value('summary')) {
                lines.push('SUMMARY');
                lines.push(value('summary'));
                lines.push('');
            }

            const roles = expData().filter(function (row) { return row.role || row.company || row.bullets; });
            if (roles.length) {
                lines.push('EXPERIENCE');
                roles.forEach(function (row) {
                    lines.push([row.role, row.company].filter(Boolean).join(' — ') + (row.dates ? ' (' + row.dates + ')' : ''));
                    if (row.bullets) {
                        row.bullets.split(/\r?\n/).map(function (line) { return line.trim(); }).filter(Boolean).forEach(function (line) {
                            lines.push(line.charAt(0) === '-' ? line : '- ' + line);
                        });
                    }
                    lines.push('');
                });
            }

            const schools = eduData().filter(function (row) { return row.degree || row.school || row.detail; });
            if (schools.length) {
                lines.push('EDUCATION');
                schools.forEach(function (row) {
                    lines.push([row.degree, row.school].filter(Boolean).join(' — ') + (row.dates ? ' (' + row.dates + ')' : ''));
                    if (row.detail) lines.push(row.detail);
                });
                lines.push('');
            }

            if (value('skills')) {
                lines.push('SKILLS');
                lines.push(value('skills'));
            }

            return lines.join('\n').replace(/\n{3,}/g, '\n\n').trim() + '\n';
        }

        resumeForm.addEventListener('input', function () {
            renderResume();
            scheduleSave();
        });

        const printBtn = el('resume-print');
        if (printBtn) {
            printBtn.addEventListener('click', function () {
                window.print();
            });
        }

        const downloadBtn = el('resume-download');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function () {
                const blob = new Blob([plainText()], { type: 'text/plain;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                const base = (value('name') || 'resume').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'resume';
                link.href = url;
                link.download = base + '-resume.txt';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                toast('Resume text downloaded.');
            });
        }

        const copyBtn = el('resume-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                copy(plainText()).then(function (ok) {
                    toast(ok ? 'Resume text copied.' : 'Could not copy. Try the download instead.');
                });
            });
        }

        const exampleBtn = el('resume-example');
        if (exampleBtn) {
            exampleBtn.addEventListener('click', function () {
                if (hasContent(collectDraft()) && !window.confirm('Replace the current draft with the example?')) return;
                applyDraft(EXAMPLE);
                persist();
                toast('Example loaded. Swap in your own facts.');
            });
        }

        const clearBtn = el('resume-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (!window.confirm('Clear the saved draft and empty the form?')) return;
                try {
                    window.localStorage.removeItem(DRAFT_KEY);
                } catch (error) {
                    /* storage can be blocked; the form still clears */
                }
                resumeForm.querySelectorAll('[data-rp]').forEach(function (input) {
                    input.value = '';
                });
                clearRows();
                addExp();
                addEdu();
                renderResume();
                if (saveStatus) saveStatus.textContent = 'Draft cleared.';
                toast('Draft cleared from this browser.');
            });
        }

        if (applyDraft(readDraft())) {
            if (saveStatus) saveStatus.textContent = 'Draft restored from this browser.';
        } else {
            addExp();
            addEdu();
            renderResume();
        }
    }

    // -----------------------------------------------------------------
    // 04 Profile optimizer
    // -----------------------------------------------------------------
    const profileForm = el('profile-form');
    if (profileForm) {
        const status = el('profile-status');
        const resultPanel = el('profile-result');
        const submitBtn = el('profile-submit');
        const submitLabel = submitBtn && submitBtn.textContent.trim() ? submitBtn.textContent.trim() : 'Rewrite my profile';
        let busy = false;

        function renderProfile(data) {
            const headlines = el('profile-headlines');
            if (headlines) {
                headlines.innerHTML = '';
                (data.headlines || []).forEach(function (text) {
                    const row = document.createElement('div');
                    row.className = 'headline-option';
                    const span = document.createElement('span');
                    span.textContent = text;
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'copy-btn';
                    button.setAttribute('data-copy', text);
                    button.textContent = 'Copy';
                    row.appendChild(span);
                    row.appendChild(button);
                    headlines.appendChild(row);
                });
            }

            if (el('profile-summary-out')) el('profile-summary-out').textContent = data.summary || '';
            if (el('profile-bullets-out')) {
                el('profile-bullets-out').textContent = (data.bullets || []).map(function (line) {
                    return line.charAt(0) === '-' ? line : '- ' + line;
                }).join('\n');
            }
            fillList(el('profile-fixes'), data.fixes);

            setStatus(status, 'Rewritten with ' + sourceLabel(data.source).toLowerCase() + '.', 'is-ok');
            if (resultPanel) {
                resultPanel.hidden = false;
                resultPanel.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
            }
        }

        profileForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (busy) return;
            const headlineEl = el('profile-headline');
            const summaryEl = el('profile-summary');
            const bulletsEl = el('profile-bullets');
            const targetEl = el('profile-target');
            const headline = headlineEl ? headlineEl.value.trim() : '';
            const summary = summaryEl ? summaryEl.value.trim() : '';
            const bullets = bulletsEl ? bulletsEl.value.trim() : '';
            const target = targetEl ? targetEl.value.trim() : '';

            if (!headline && !summary && !bullets) {
                setStatus(status, 'Add a headline, a summary, or at least one bullet.', 'is-error');
                if (headlineEl) headlineEl.focus();
                return;
            }

            busy = true;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Rewriting…';
            }
            setStatus(status, 'Rewriting…');

            postJson(endpoints.profile, { target_role: target, headline: headline, summary: summary, bullets: bullets })
                .then(function (result) {
                    if (!result.ok) {
                        setStatus(status, firstError(result.body) || 'Could not rewrite this profile.', 'is-error');
                        return;
                    }
                    renderProfile(result.body);
                })
                .catch(function () {
                    setStatus(status, 'Network error while rewriting.', 'is-error');
                })
                .finally(function () {
                    busy = false;
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = submitLabel;
                    }
                });
        });
    }

    // -----------------------------------------------------------------
    // 05 Prompt library
    // -----------------------------------------------------------------
    const promptGrid = el('prompt-grid');
    if (promptGrid) {
        const search = el('prompt-search');
        const searchBox = el('prompt-search-box');
        const clearBtn = el('prompt-clear');
        const copyVisibleBtn = el('prompt-copy-visible');
        const filters = el('prompt-filters');
        const empty = el('prompt-empty');
        const cards = Array.prototype.slice.call(promptGrid.querySelectorAll('[data-prompt]'));
        let activeCategory = 'all';

        function applyPrompts() {
            const term = (search ? search.value : '').trim().toLowerCase();
            let visible = 0;

            cards.forEach(function (card) {
                const category = card.getAttribute('data-category') || '';
                const text = card.textContent.toLowerCase();
                const inCategory = activeCategory === 'all' || category === activeCategory;
                const matches = term === '' || text.indexOf(term) !== -1;
                const show = inCategory && matches;
                card.hidden = !show;
                if (show) visible += 1;
            });

            if (empty) empty.classList.toggle('is-visible', visible === 0);
            if (searchBox) searchBox.classList.toggle('has-value', term !== '');
            if (clearBtn) clearBtn.hidden = term === '';
        }

        if (search) {
            search.addEventListener('input', applyPrompts);

            search.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && search.value !== '') {
                    event.preventDefault();
                    search.value = '';
                    applyPrompts();
                }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (search) {
                    search.value = '';
                    search.focus();
                }
                applyPrompts();
            });
        }

        if (copyVisibleBtn) {
            copyVisibleBtn.addEventListener('click', function () {
                const visible = cards.filter(function (card) { return !card.hidden; });
                if (!visible.length) {
                    toast('No prompts to copy. Clear the search first.');
                    return;
                }
                const text = visible.map(function (card) {
                    const title = card.querySelector('h3');
                    const body = card.querySelector('.prompt-text');
                    return (title ? title.textContent.trim() : 'Prompt') + '\n' + (body ? body.textContent.trim() : '');
                }).join('\n\n');
                copy(text).then(function (ok) {
                    toast(ok ? 'Copied ' + visible.length + ' prompts.' : 'Could not copy. Select the prompts manually.');
                });
            });
        }

        if (filters) {
            filters.addEventListener('click', function (event) {
                const button = event.target.closest('[data-prompt-filter]');
                if (!button) return;
                activeCategory = button.getAttribute('data-prompt-filter');
                filters.querySelectorAll('.filter-btn').forEach(function (btn) {
                    btn.classList.toggle('is-active', btn === button);
                });
                applyPrompts();
            });
        }

        promptGrid.addEventListener('click', function (event) {
            const copyBtn = event.target.closest('[data-copy-prompt]');
            if (copyBtn) {
                const card = copyBtn.closest('[data-prompt]');
                const textEl = card ? card.querySelector('.prompt-text') : null;
                const text = textEl ? textEl.textContent.trim() : '';
                copy(text).then(function (ok) {
                    toast(ok ? 'Prompt copied. Fill the brackets and run it.' : 'Could not copy. Select the prompt manually.');
                });
                return;
            }

            const expand = event.target.closest('.prompt-expand');
            if (expand) {
                const card = expand.closest('[data-prompt]');
                if (!card) return;
                const open = card.classList.toggle('is-open');
                expand.textContent = open ? 'Collapse' : 'Expand';
            }
        });
    }
})();

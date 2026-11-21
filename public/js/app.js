/* Emplowise shared UI: navigation, pillars, accordions, reveals, copy, FAQ, audit wizard. */
(function () {
    'use strict';

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // -----------------------------------------------------------------
    // Toast + clipboard
    // -----------------------------------------------------------------
    let toastTimer = null;

    function toast(message) {
        const el = document.getElementById('toast');
        if (!el) return;
        el.textContent = message;
        el.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () {
            el.classList.remove('is-visible');
        }, 2200);
    }

    function fallbackCopy(text) {
        const area = document.createElement('textarea');
        area.value = text;
        area.setAttribute('readonly', '');
        area.style.position = 'fixed';
        area.style.top = '-1000px';
        document.body.appendChild(area);
        area.select();
        let ok = false;
        try {
            ok = document.execCommand('copy');
        } catch (error) {
            ok = false;
        }
        document.body.removeChild(area);
        return ok;
    }

    function copyText(text) {
        if (!text) return Promise.resolve(false);
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text)
                .then(function () { return true; })
                .catch(function () { return fallbackCopy(text); });
        }
        return Promise.resolve(fallbackCopy(text));
    }

    document.addEventListener('click', function (event) {
        const target = event.target.closest('[data-copy], [data-copy-target]');
        if (!target) return;

        let text = target.getAttribute('data-copy');
        if (!text) {
            const source = document.getElementById(target.getAttribute('data-copy-target'));
            text = source ? (source.textContent || '').trim() : '';
        }

        copyText(text).then(function (ok) {
            toast(ok ? 'Copied to clipboard.' : 'Could not copy. Select the text manually.');
        });
    });

    window.Emplowise = window.Emplowise || {};
    window.Emplowise.toast = toast;
    window.Emplowise.copy = copyText;

    // -----------------------------------------------------------------
    // Overlay scroll lock (drawer, chat sheet)
    // Token based so two overlays never unlock each other. Uses a root
    // overflow toggle, which keeps the current scroll position intact
    // and pairs with scrollbar-gutter: stable so nothing shifts.
    // -----------------------------------------------------------------
    const locks = new Set();

    function lockScroll(token) {
        locks.add(token || 'default');
        document.documentElement.classList.add('is-scroll-locked');
    }

    function unlockScroll(token) {
        locks.delete(token || 'default');
        if (!locks.size) document.documentElement.classList.remove('is-scroll-locked');
    }

    window.Emplowise.lockScroll = lockScroll;
    window.Emplowise.unlockScroll = unlockScroll;

    // -----------------------------------------------------------------
    // Header navigation
    // -----------------------------------------------------------------
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        const closeNav = function (returnFocus) {
            if (!mainNav.classList.contains('is-open')) return;
            mainNav.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
            navToggle.setAttribute('aria-label', 'Open menu');
            unlockScroll('nav');
            if (returnFocus) navToggle.focus({ preventScroll: true });
        };

        navToggle.addEventListener('click', function () {
            const open = mainNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            if (open) {
                lockScroll('nav');
            } else {
                unlockScroll('nav');
            }
        });

        mainNav.addEventListener('click', function (event) {
            if (event.target.closest('a')) closeNav(false);
        });

        document.addEventListener('click', function (event) {
            if (!mainNav.classList.contains('is-open')) return;
            if (event.target.closest('#main-nav') || event.target.closest('#nav-toggle')) return;
            closeNav(false);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape' || !mainNav.classList.contains('is-open')) return;
            closeNav(true);
        });
    }

    // -----------------------------------------------------------------
    // Home pillar showcase
    // -----------------------------------------------------------------
    document.querySelectorAll('.pillar-head').forEach(function (head) {
        head.addEventListener('click', function () {
            const pillar = head.closest('.pillar');
            if (!pillar) return;
            const open = pillar.classList.toggle('is-open');
            head.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });

    // -----------------------------------------------------------------
    // Generic accordions (FAQ)
    // -----------------------------------------------------------------
    document.querySelectorAll('.acc-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const item = trigger.closest('.acc-item');
            if (!item) return;
            const open = item.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });

    // -----------------------------------------------------------------
    // Reveal on scroll (progressive enhancement: content is visible by
    // default and only hidden once JS has confirmed it can reveal it)
    // -----------------------------------------------------------------
    const revealItems = document.querySelectorAll('.reveal');
    if (revealItems.length) {
        if ('IntersectionObserver' in window && !reducedMotion) {
            document.documentElement.classList.add('js-anim');
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '0px 0px -6% 0px', threshold: 0.05 });
            revealItems.forEach(function (item) { observer.observe(item); });

            // Safety net: never leave content hidden if observation fails.
            window.setTimeout(function () {
                revealItems.forEach(function (item) { item.classList.add('is-visible'); });
            }, 4000);
        } else {
            revealItems.forEach(function (item) { item.classList.add('is-visible'); });
        }
    }

    // -----------------------------------------------------------------
    // Header state, reading progress, back to top
    // -----------------------------------------------------------------
    const header = document.getElementById('site-header');
    const progressBar = document.getElementById('scroll-progress-bar');
    const toTop = document.getElementById('to-top');
    let scrollTicking = false;
    let scrollMax = 0;

    // Cache the scrollable range: reading scrollHeight per frame forces layout.
    function measureScroll() {
        scrollMax = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
    }

    function onScroll() {
        const y = window.scrollY || document.documentElement.scrollTop;

        if (header) header.classList.toggle('is-stuck', y > 12);
        if (progressBar) {
            const ratio = scrollMax > 0 ? Math.min(1, Math.max(0, y / scrollMax)) : 0;
            progressBar.style.transform = 'scaleX(' + ratio.toFixed(4) + ')';
        }
        if (toTop) toTop.classList.toggle('is-visible', y > 600);

        scrollTicking = false;
    }

    window.addEventListener('scroll', function () {
        if (scrollTicking) return;
        scrollTicking = true;
        window.requestAnimationFrame(onScroll);
    }, { passive: true });

    let resizeTicking = false;
    window.addEventListener('resize', function () {
        if (resizeTicking) return;
        resizeTicking = true;
        window.requestAnimationFrame(function () {
            measureScroll();
            onScroll();
            resizeTicking = false;
        });
    }, { passive: true });

    window.addEventListener('load', function () {
        measureScroll();
        onScroll();
    });

    measureScroll();
    onScroll();

    if (toTop) {
        toTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' });
        });
    }

    // -----------------------------------------------------------------
    // Count-up numbers
    // -----------------------------------------------------------------
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length) {
        const runCount = function (el) {
            const target = parseInt(el.getAttribute('data-count'), 10);
            if (!isFinite(target)) return;
            if (reducedMotion) { el.textContent = String(target); return; }

            const duration = 1100;
            const start = performance.now();
            const step = function (now) {
                const t = Math.min(1, (now - start) / duration);
                const eased = 1 - Math.pow(1 - t, 3);
                el.textContent = String(Math.round(target * eased));
                if (t < 1) window.requestAnimationFrame(step);
            };
            window.requestAnimationFrame(step);
        };

        if ('IntersectionObserver' in window) {
            const countObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    runCount(entry.target);
                    countObserver.unobserve(entry.target);
                });
            }, { threshold: 0.4 });
            counters.forEach(function (el) { countObserver.observe(el); });
        } else {
            counters.forEach(runCount);
        }
    }

    // -----------------------------------------------------------------
    // Hero: animated stack bars, rotating focus, pointer parallax
    // -----------------------------------------------------------------
    const stackBars = document.querySelectorAll('.stack-bar i[data-fill]');
    if (stackBars.length) {
        window.setTimeout(function () {
            stackBars.forEach(function (bar, index) {
                window.setTimeout(function () {
                    bar.style.width = bar.getAttribute('data-fill') + '%';
                }, reducedMotion ? 0 : index * 160);
            });
        }, 260);
    }

    const rotator = document.getElementById('hero-rotator');
    if (rotator && !reducedMotion) {
        const words = ['payroll inputs', 'review cycles', 'policy versions', 'onboarding flows', 'people reports'];
        let wordIndex = 0;
        window.setInterval(function () {
            wordIndex = (wordIndex + 1) % words.length;
            const next = document.createElement('b');
            next.textContent = words[wordIndex];
            rotator.replaceChildren(next);
        }, 2600);
    }

    const heroPanel = document.getElementById('hero-panel');
    if (heroPanel && !reducedMotion && window.matchMedia('(hover: hover) and (min-width: 981px)').matches) {
        const hero = heroPanel.closest('.hero');
        if (hero) {
            hero.addEventListener('pointermove', function (event) {
                const rect = heroPanel.getBoundingClientRect();
                const dx = (event.clientX - (rect.left + rect.width / 2)) / rect.width;
                const dy = (event.clientY - (rect.top + rect.height / 2)) / rect.height;
                heroPanel.style.transform = 'perspective(1100px) rotateY(' + (dx * 5).toFixed(2) + 'deg) rotateX(' + (-dy * 4).toFixed(2) + 'deg) translateY(-3px)';
            });
            hero.addEventListener('pointerleave', function () {
                heroPanel.style.transform = '';
            });
        }
    }

    // -----------------------------------------------------------------
    // Open chat from anywhere
    // -----------------------------------------------------------------
    document.querySelectorAll('[data-open-chat]').forEach(function (button) {
        button.addEventListener('click', function () {
            const launcher = document.getElementById('chat-launcher');
            if (launcher) launcher.click();
        });
    });

    // -----------------------------------------------------------------
    // FAQ search + category filter
    // -----------------------------------------------------------------
    const faqSearch = document.getElementById('faq-search');
    const faqList = document.getElementById('faq-list');
    const faqEmpty = document.getElementById('faq-empty');
    const faqFilters = document.getElementById('faq-filters');
    const faqSearchBox = document.getElementById('faq-search-box');
    const faqClear = document.getElementById('faq-clear');

    if (faqSearch && faqList) {
        const items = Array.prototype.slice.call(faqList.querySelectorAll('.acc-item'));
        const groups = Array.prototype.slice.call(faqList.querySelectorAll('[data-faq-group]'));
        let activeCategory = 'all';
        let applyTimer = null;

        const cache = items.map(function (item) {
            const q = item.querySelector('.acc-q');
            const a = item.querySelector('.acc-a');
            return {
                item: item,
                q: q ? q.textContent : '',
                a: a ? a.textContent : '',
                qEl: q,
                aEl: a,
                category: item.getAttribute('data-cat') || ''
            };
        });

        groupMeta(groups);

        function groupMeta(list) {
            list.forEach(function (group) {
                const count = group.querySelector('.faq-group-head span');
                if (!count) return;
                const total = group.querySelectorAll('.acc-item').length;
                count.setAttribute('data-total', String(total));
            });
        }

        function escapeHtml(value) {
            return value.replace(/[&<>"']/g, function (char) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
            });
        }

        function mark(text, term) {
            if (!term) return escapeHtml(text);
            const safe = escapeHtml(text);
            const tokens = term.split(/\s+/).filter(Boolean).map(function (token) {
                return token.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            });
            if (!tokens.length) return safe;
            try {
                return safe.replace(new RegExp('(' + tokens.join('|') + ')', 'gi'), '<mark>$1</mark>');
            } catch (error) {
                return safe;
            }
        }

        function apply() {
            const term = (faqSearch.value || '').trim().toLowerCase();
            const tokens = term.split(/\s+/).filter(Boolean);
            let totalVisible = 0;

            cache.forEach(function (row) {
                const inCategory = activeCategory === 'all' || row.category === activeCategory;
                const haystack = (row.q + ' ' + row.a).toLowerCase();
                const matches = tokens.every(function (token) { return haystack.indexOf(token) !== -1; });
                const visible = inCategory && matches;

                row.item.hidden = !visible;
                if (visible) totalVisible += 1;

                if (row.qEl) row.qEl.innerHTML = mark(row.q, term);
                if (row.aEl) row.aEl.innerHTML = mark(row.a, term);

                if (!visible) {
                    row.item.classList.remove('is-open');
                    const trigger = row.item.querySelector('.acc-trigger');
                    if (trigger) trigger.setAttribute('aria-expanded', 'false');
                }
            });

            groups.forEach(function (group) {
                const visibleInGroup = group.querySelectorAll('.acc-item:not([hidden])').length;
                group.hidden = visibleInGroup === 0;
                const label = group.querySelector('.faq-group-head span');
                if (label) {
                    const total = label.getAttribute('data-total') || visibleInGroup;
                    label.textContent = visibleInGroup === Number(total)
                        ? total + ' answers'
                        : visibleInGroup + ' of ' + total + ' answers';
                }
            });

            if (faqEmpty) faqEmpty.classList.toggle('is-visible', totalVisible === 0);

            if (faqSearchBox) faqSearchBox.classList.toggle('has-value', term !== '');
            if (faqClear) faqClear.hidden = term === '';
        }

        function scheduleApply() {
            if (applyTimer) window.clearTimeout(applyTimer);
            applyTimer = window.setTimeout(apply, 110);
        }

        faqSearch.addEventListener('input', scheduleApply);

        faqSearch.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && faqSearch.value !== '') {
                event.preventDefault();
                faqSearch.value = '';
                apply();
            }
        });

        if (faqClear) {
            faqClear.addEventListener('click', function () {
                faqSearch.value = '';
                apply();
                faqSearch.focus();
            });
        }

        // Press "/" anywhere that is not an input to jump to the search box.
        document.addEventListener('keydown', function (event) {
            if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) return;
            const tag = (event.target && event.target.tagName) || '';
            if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (event.target && event.target.isContentEditable)) return;
            event.preventDefault();
            faqSearch.focus();
            faqSearch.select();
        });

        if (faqFilters) {
            faqFilters.addEventListener('click', function (event) {
                const button = event.target.closest('[data-faq-filter]');
                if (!button) return;
                activeCategory = button.getAttribute('data-faq-filter');
                faqFilters.querySelectorAll('.filter-btn').forEach(function (btn) {
                    btn.classList.toggle('is-active', btn === button);
                });
                if (applyTimer) window.clearTimeout(applyTimer);
                apply();
            });
        }

        apply();
    }

    // -----------------------------------------------------------------
    // Audit wizard
    // -----------------------------------------------------------------
    const auditForm = document.getElementById('audit-form');
    if (auditForm) {
        initWizard(auditForm);
    }

    function initWizard(form) {
        const steps = Array.prototype.slice.call(form.querySelectorAll('.wizard-step'));
        const backBtn = document.getElementById('wizard-back');
        const nextBtn = document.getElementById('wizard-next');
        const submitBtn = document.getElementById('wizard-submit');
        const submitLabel = submitBtn && submitBtn.textContent.trim() ? submitBtn.textContent.trim() : 'Submit audit request';
        const progress = document.getElementById('wizard-progress');
        const dots = document.getElementById('wizard-dots');
        const label = document.getElementById('wizard-label');
        const counter = document.getElementById('wizard-count');
        const status = document.getElementById('audit-status');
        const reviewList = document.getElementById('review-list');
        const success = document.getElementById('audit-success');
        const submitError = document.getElementById('submit-error');

        const stepNames = ['Contact', 'Operations', 'Schedule', 'Review'];
        let current = 1;
        let sending = false;
        let lastNav = 0;

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        const staticMode = form.getAttribute('data-audit-mode') === 'web3forms';
        const DRAFT_KEY = 'emplowise_audit_draft';
        const DRAFT_FIELDS = ['contact_name', 'company_name', 'email', 'phone', 'headcount_band', 'notes', 'preferred_date', 'preferred_slot'];

        function stepEl(number) {
            return steps[number - 1];
        }

        function fieldWrap(name) {
            return form.querySelector('[data-field="' + name + '"]');
        }

        function fieldEl(name) {
            return form.querySelector('[name="' + name + '"]');
        }

        function fieldValue(name) {
            const input = fieldEl(name);
            return input ? String(input.value || '').trim() : '';
        }

        // Draft persistence: contact and scheduling inputs only. The consent
        // checkbox and the honeypot are deliberately never stored.
        function readDraft() {
            try {
                const raw = window.sessionStorage.getItem(DRAFT_KEY);
                return raw ? JSON.parse(raw) : null;
            } catch (error) {
                return null;
            }
        }

        function writeDraft() {
            try {
                const draft = { fields: {}, pains: [] };
                DRAFT_FIELDS.forEach(function (name) {
                    const input = fieldEl(name);
                    if (input && input.value) draft.fields[name] = input.value;
                });
                form.querySelectorAll('[name="pain_points[]"]:checked').forEach(function (input) {
                    draft.pains.push(input.value);
                });
                window.sessionStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
            } catch (error) {
                /* storage blocked: draft safety is optional, never fatal */
            }
        }

        function clearDraft() {
            try {
                window.sessionStorage.removeItem(DRAFT_KEY);
            } catch (error) {
                /* ignore */
            }
        }

        function restoreDraft() {
            const draft = readDraft();
            if (!draft || (!draft.fields && !draft.pains)) return;

            Object.keys(draft.fields || {}).forEach(function (name) {
                const input = fieldEl(name);
                if (!input) return;
                if (name === 'preferred_date') {
                    const value = draft.fields[name];
                    if (input.min && value < input.min) return;
                    if (input.max && value > input.max) return;
                }
                input.value = draft.fields[name];
            });

            (draft.pains || []).forEach(function (value) {
                const box = form.querySelector('[name="pain_points[]"][value="' + value + '"]');
                if (box) box.checked = true;
            });
        }

        restoreDraft();

        function setError(name, message) {
            const wrap = fieldWrap(name);
            if (!wrap) return;
            wrap.classList.add('has-error');
            const error = wrap.querySelector('.field-error');
            if (error) error.textContent = message;
        }

        function clearErrors(scope) {
            (scope || form).querySelectorAll('.field.has-error').forEach(function (wrap) {
                wrap.classList.remove('has-error');
                const error = wrap.querySelector('.field-error');
                if (error) error.textContent = '';
            });
        }

        function showStep(number, focus) {
            current = Math.min(Math.max(number, 1), steps.length);
            steps.forEach(function (step, index) {
                step.classList.toggle('is-active', index + 1 === current);
            });
            if (progress) progress.style.width = ((current / steps.length) * 100) + '%';
            if (dots) {
                Array.prototype.forEach.call(dots.children, function (dot, index) {
                    dot.classList.toggle('is-done', index + 1 < current);
                    dot.classList.toggle('is-current', index + 1 === current);
                });
            }
            if (label) label.textContent = 'Step ' + current + ' of ' + steps.length + ' · ' + stepNames[current - 1];
            if (counter) counter.textContent = String(current).padStart(2, '0') + ' / ' + String(steps.length).padStart(2, '0');
            if (backBtn) backBtn.hidden = current === 1;
            if (nextBtn) nextBtn.hidden = current === steps.length;
            if (submitBtn) submitBtn.hidden = current !== steps.length;

            if (current === steps.length) buildReview();

            if (focus) {
                const target = stepEl(current);
                if (target) {
                    target.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
                }
            }
        }

        function validateStep(number) {
            const step = stepEl(number);
            if (!step) return true;
            clearErrors(step);
            let firstInvalid = null;
            const fields = step.querySelectorAll('input, select, textarea');

            fields.forEach(function (el) {
                if (el.type === 'hidden' || el.name === 'website') return;
                if (el.type === 'checkbox' || el.type === 'radio') return;

                const value = (el.value || '').trim();
                let message = '';

                if (el.hasAttribute('required') && !value) {
                    message = 'This field is required.';
                } else if (el.type === 'email' && value && !/^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test(value)) {
                    message = 'Use a valid email address, for example you@company.com.';
                } else if (el.name === 'phone' && value && !/^[0-9+\-\s()]{7,40}$/.test(value)) {
                    message = 'Use digits, spaces, +, -, or brackets. At least 7 characters.';
                } else if ((el.name === 'contact_name' || el.name === 'company_name') && value && value.length < 2) {
                    message = 'Use at least 2 characters.';
                } else if (el.name === 'preferred_date' && value) {
                    const today = document.getElementById('audit-date');
                    if (today && today.min && value < today.min) message = 'Pick a date from today onward.';
                    if (today && today.max && value > today.max) message = 'Pick a date within the next 60 days.';
                }

                if (message) {
                    setError(el.name, message);
                    if (!firstInvalid) firstInvalid = el;
                }
            });

            const headcount = step.querySelector('[name="headcount_band"]');
            if (headcount) {
                if (!headcount.value) {
                    setError('headcount_band', 'Select a headcount band.');
                    if (!firstInvalid) firstInvalid = headcount;
                }
            }

            const pains = step.querySelectorAll('[name="pain_points[]"]');
            if (pains.length) {
                const checked = step.querySelectorAll('[name="pain_points[]"]:checked').length;
                if (checked === 0) {
                    setError('pain_points', 'Select at least one pain point.');
                    if (!firstInvalid) firstInvalid = pains[0];
                } else if (checked > 6) {
                    setError('pain_points', 'Select no more than six.');
                }
            }

            const consent = step.querySelector('[name="consent"]');
            if (consent && !consent.checked) {
                setError('consent', 'Please confirm we can contact you about the audit.');
                if (!firstInvalid) firstInvalid = consent;
            }

            if (firstInvalid) {
                firstInvalid.focus({ preventScroll: true });
                firstInvalid.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'center' });
                return false;
            }
            return true;
        }

        function readLabel(select) {
            if (!select || !select.options || select.selectedIndex < 0) return '—';
            return select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '—';
        }

        function addRow(term, value) {
            const row = document.createElement('div');
            const dt = document.createElement('dt');
            const dd = document.createElement('dd');
            dt.textContent = term;
            dd.textContent = value && value.length ? value : '—';
            row.appendChild(dt);
            row.appendChild(dd);
            reviewList.appendChild(row);
        }

        function buildReview() {
            if (!reviewList) return;
            reviewList.innerHTML = '';
            const name = fieldValue('contact_name');
            const company = fieldValue('company_name');
            const email = fieldValue('email');
            const phone = fieldValue('phone');
            const headcount = fieldEl('headcount_band');
            const dateInput = fieldEl('preferred_date');
            const date = dateInput ? dateInput.value : '';
            const slot = fieldEl('preferred_slot');
            const notes = fieldValue('notes');

            const pains = Array.prototype.slice.call(auditForm.querySelectorAll('[name="pain_points[]"]:checked')).map(function (input) {
                const labelEl = input.closest('.check');
                const text = labelEl ? labelEl.querySelector('span') : null;
                return text ? text.textContent.trim() : input.value;
            });

            addRow('Contact', company ? name + ' · ' + company : name);
            addRow('Email', email);
            addRow('Phone', phone);
            addRow('Headcount', readLabel(headcount));
            addRow('Pain points', pains.join(', '));
            if (notes) addRow('Notes', notes.length > 140 ? notes.slice(0, 140) + '…' : notes);
            addRow('Preferred date', date ? new Date(date + 'T00:00:00').toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—');
            addRow('Preferred window', readLabel(slot));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                // Ignore a fast double click so one tap cannot skip a whole step.
                const now = Date.now();
                if (now - lastNav < 250) return;
                lastNav = now;
                if (!validateStep(current)) return;
                showStep(current + 1, true);
            });
        }

        if (backBtn) {
            backBtn.addEventListener('click', function () {
                const now = Date.now();
                if (now - lastNav < 250) return;
                lastNav = now;
                clearErrors(stepEl(current));
                showStep(current - 1, true);
            });
        }

        // Keep the draft warm while the user types; debounce so storage writes
        // stay off the keystroke path.
        let draftTimer = null;
        form.addEventListener('input', function () {
            if (draftTimer) window.clearTimeout(draftTimer);
            draftTimer = window.setTimeout(writeDraft, 400);
        });
        form.addEventListener('change', function () {
            if (draftTimer) window.clearTimeout(draftTimer);
            draftTimer = window.setTimeout(writeDraft, 200);
        });

        auditForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (sending) return;

            if (current < steps.length) {
                if (validateStep(current)) showStep(current + 1, true);
                return;
            }

            for (let index = 1; index < steps.length; index += 1) {
                if (!validateStep(index)) {
                    showStep(index, true);
                    return;
                }
            }

            // A preview build without a Web3Forms key cannot send anywhere, so
            // stop before firing a request that is guaranteed to fail.
            if (staticMode && auditForm.getAttribute('data-audit-nokey') === '1') {
                if (status) {
                    status.classList.add('is-error');
                    status.textContent = 'Booking is not connected in this preview build.';
                }
                return;
            }

            if (status) {
                status.classList.remove('is-error', 'is-ok');
                status.textContent = 'Sending your request…';
            }
            if (submitError) submitError.textContent = '';
            sending = true;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting…';
            }

            const data = new FormData(auditForm);
            const headers = { 'Accept': 'application/json' };

            if (!staticMode) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            fetch(auditForm.getAttribute('data-endpoint') || window.location.pathname.replace(/\/free-hr-audit\/?$/, '/api/audit'), {
                method: 'POST',
                headers: headers,
                body: data,
                credentials: staticMode ? 'omit' : 'same-origin'
            })
                .then(function (response) {
                    return response.json().catch(function () { return {}; }).then(function (body) {
                        return { ok: response.ok, status: response.status, body: body };
                    });
                })
                .then(function (result) {
                    if (result.body && result.body.success === false) {
                        if (status) {
                            status.classList.add('is-error');
                            status.textContent = result.body.message || 'The form service rejected this request.';
                        }
                        return;
                    }

                    if (result.ok) {
                        let reference = result.body.reference;
                        let preferredDate = result.body.preferred_date;
                        let preferredSlot = result.body.preferred_slot;

                        if (staticMode) {
                            const dateField = auditForm.querySelector('[name="preferred_date"]');
                            const slotField = auditForm.querySelector('[name="preferred_slot"]');
                            const stamp = new Date();
                            const ymd = String(stamp.getFullYear()).slice(-2)
                                + String(stamp.getMonth() + 1).padStart(2, '0')
                                + String(stamp.getDate()).padStart(2, '0');
                            const suffix = Math.random().toString(36).slice(2, 6).toUpperCase();
                            reference = reference || ('EMP-' + ymd + '-' + suffix);
                            preferredSlot = preferredSlot || (slotField ? slotField.value : '');
                            if (!preferredDate && dateField && dateField.value) {
                                const parsed = new Date(dateField.value + 'T00:00:00');
                                preferredDate = isNaN(parsed.getTime())
                                    ? dateField.value
                                    : parsed.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                            }
                        }

                        clearDraft();
                        if (document.getElementById('success-ref')) {
                            document.getElementById('success-ref').textContent = reference || 'Reference pending';
                        }
                        if (document.getElementById('success-date')) {
                            document.getElementById('success-date').textContent = preferredDate || 'Pending';
                        }
                        if (document.getElementById('success-slot')) {
                            document.getElementById('success-slot').textContent = preferredSlot || 'Pending';
                        }
                        auditForm.hidden = true;
                        if (success) {
                            success.classList.add('is-visible');
                            success.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
                        }
                        return;
                    }

                    if (result.status === 422 && result.body.errors) {
                        const errors = result.body.errors;
                        let firstStep = 99;
                        Object.keys(errors).forEach(function (key) {
                            const name = key.split('.')[0];
                            const message = Array.isArray(errors[key]) ? errors[key][0] : String(errors[key]);
                            setError(name, message);
                            const wrap = fieldWrap(name);
                            if (wrap) {
                                const step = wrap.closest('.wizard-step');
                                if (step) {
                                    const number = Number(step.getAttribute('data-step'));
                                    if (number && number < firstStep) firstStep = number;
                                }
                            }
                        });
                        if (status) {
                            status.classList.add('is-error');
                            status.textContent = 'Check the highlighted fields and submit again.';
                        }
                        if (firstStep !== 99) {
                            showStep(firstStep, true);
                            const step = stepEl(firstStep);
                            const invalid = step ? step.querySelector('.field.has-error input, .field.has-error select, .field.has-error textarea, .field.has-error [name="consent"]') : null;
                            if (invalid) invalid.focus({ preventScroll: true });
                        }
                        return;
                    }

                    if (result.status === 419) {
                        if (status) {
                            status.classList.add('is-error');
                            status.textContent = 'Your session expired. Your draft is saved — reload the page and submit again.';
                        }
                        return;
                    }

                    if (result.status === 429) {
                        if (status) {
                            status.classList.add('is-error');
                            status.textContent = 'Too many attempts. Wait a minute, then submit again.';
                        }
                        return;
                    }

                    if (status) {
                        status.classList.add('is-error');
                        status.textContent = (result.body && result.body.message) ? result.body.message : 'Something went wrong. Try again.';
                    }
                })
                .catch(function () {
                    if (status) {
                        status.classList.add('is-error');
                        status.textContent = 'Network error. Check your connection and submit again.';
                    }
                })
                .finally(function () {
                    sending = false;
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = submitLabel;
                    }
                });
        });

        auditForm.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') return;
            const el = event.target;
            if (el && (el.tagName === 'TEXTAREA' || el.tagName === 'BUTTON')) return;
            if (current < steps.length) {
                event.preventDefault();
                if (validateStep(current)) showStep(current + 1, true);
            }
        });

        showStep(1, false);
    }
})();

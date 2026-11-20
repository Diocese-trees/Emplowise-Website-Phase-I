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
    // Header navigation
    // -----------------------------------------------------------------
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function () {
            const open = mainNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        });

        mainNav.addEventListener('click', function (event) {
            if (event.target.closest('a')) {
                mainNav.classList.remove('is-open');
                navToggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('click', function (event) {
            if (!mainNav.classList.contains('is-open')) return;
            if (event.target.closest('#main-nav') || event.target.closest('#nav-toggle')) return;
            mainNav.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
            navToggle.setAttribute('aria-label', 'Open menu');
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape' || !mainNav.classList.contains('is-open')) return;
            mainNav.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
            navToggle.setAttribute('aria-label', 'Open menu');
            navToggle.focus();
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

    function onScroll() {
        const y = window.scrollY || document.documentElement.scrollTop;
        const max = document.documentElement.scrollHeight - window.innerHeight;

        if (header) header.classList.toggle('is-stuck', y > 12);
        if (progressBar) progressBar.style.width = (max > 0 ? Math.min(100, (y / max) * 100) : 0) + '%';
        if (toTop) toTop.classList.toggle('is-visible', y > 600);

        scrollTicking = false;
    }

    window.addEventListener('scroll', function () {
        if (scrollTicking) return;
        scrollTicking = true;
        window.requestAnimationFrame(onScroll);
    }, { passive: true });

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

    if (faqSearch && faqList) {
        const items = Array.prototype.slice.call(faqList.querySelectorAll('.acc-item'));
        const groups = Array.prototype.slice.call(faqList.querySelectorAll('[data-faq-group]'));
        let activeCategory = 'all';

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
        }

        faqSearch.addEventListener('input', apply);

        if (faqFilters) {
            faqFilters.addEventListener('click', function (event) {
                const button = event.target.closest('[data-faq-filter]');
                if (!button) return;
                activeCategory = button.getAttribute('data-faq-filter');
                faqFilters.querySelectorAll('.filter-btn').forEach(function (btn) {
                    btn.classList.toggle('is-active', btn === button);
                });
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

        function stepEl(number) {
            return steps[number - 1];
        }

        function fieldWrap(name) {
            return form.querySelector('[data-field="' + name + '"]');
        }

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
            const name = auditForm.querySelector('[name="contact_name"]').value.trim();
            const company = auditForm.querySelector('[name="company_name"]').value.trim();
            const email = auditForm.querySelector('[name="email"]').value.trim();
            const phone = auditForm.querySelector('[name="phone"]').value.trim();
            const headcount = auditForm.querySelector('[name="headcount_band"]');
            const date = auditForm.querySelector('[name="preferred_date"]').value;
            const slot = auditForm.querySelector('[name="preferred_slot"]');
            const notes = auditForm.querySelector('[name="notes"]').value.trim();

            const pains = Array.prototype.slice.call(auditForm.querySelectorAll('[name="pain_points[]"]:checked')).map(function (input) {
                const labelEl = input.closest('.check');
                return labelEl ? labelEl.querySelector('span').textContent.trim() : input.value;
            });

            addRow('Contact', name + (company ? ' · ' + company : ''));
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
                if (!validateStep(current)) return;
                showStep(current + 1, true);
            });
        }

        if (backBtn) {
            backBtn.addEventListener('click', function () {
                clearErrors(stepEl(current));
                showStep(current - 1, true);
            });
        }

        auditForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (sending) return;

            if (current < steps.length) {
                if (validateStep(current)) showStep(current + 1, true);
                return;
            }

            for (let index = 1; index <= 3; index += 1) {
                if (!validateStep(index)) {
                    showStep(index, true);
                    return;
                }
            }

            if (status) {
                status.classList.remove('is-error', 'is-ok');
                status.textContent = 'Sending your request…';
            }
            if (submitError) submitError.textContent = '';
            sending = true;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
            }

            const data = new FormData(auditForm);

            fetch(auditForm.getAttribute('data-endpoint') || window.location.pathname.replace(/\/free-hr-audit\/?$/, '/api/audit'), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: data,
                credentials: 'same-origin'
            })
                .then(function (response) {
                    return response.json().catch(function () { return {}; }).then(function (body) {
                        return { ok: response.ok, status: response.status, body: body };
                    });
                })
                .then(function (result) {
                    if (result.ok) {
                        if (document.getElementById('success-ref')) {
                            document.getElementById('success-ref').textContent = result.body.reference || 'Reference pending';
                        }
                        if (document.getElementById('success-date')) {
                            document.getElementById('success-date').textContent = result.body.preferred_date || 'Pending';
                        }
                        if (document.getElementById('success-slot')) {
                            document.getElementById('success-slot').textContent = result.body.preferred_slot || 'Pending';
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
                        if (firstStep !== 99) showStep(firstStep, true);
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
                        submitBtn.textContent = 'Submit audit request';
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

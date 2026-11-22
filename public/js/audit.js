/* Emplowise free HR audit: multi-step wizard, validation and submit. Loaded only on the audit page. */
(function () {
    'use strict';

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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

        let errorSeq = 0;

        function markField(wrap, invalid) {
            const error = wrap.querySelector('.field-error');

            wrap.querySelectorAll('input, select, textarea').forEach(function (control) {
                if (invalid) {
                    if (!error) return;
                    if (!error.id) error.id = 'audit-error-' + (errorSeq += 1);
                    control.setAttribute('aria-invalid', 'true');
                    const ids = (control.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
                    if (ids.indexOf(error.id) === -1) ids.push(error.id);
                    control.setAttribute('aria-describedby', ids.join(' '));
                    return;
                }

                control.removeAttribute('aria-invalid');
                if (!error || !error.id) return;
                const rest = (control.getAttribute('aria-describedby') || '')
                    .split(/\s+/)
                    .filter(function (id) { return id && id !== error.id; });
                if (rest.length) control.setAttribute('aria-describedby', rest.join(' '));
                else control.removeAttribute('aria-describedby');
            });
        }

        function setError(name, message) {
            const wrap = fieldWrap(name);
            if (!wrap) return;
            wrap.classList.add('has-error');
            const error = wrap.querySelector('.field-error');
            if (error) error.textContent = message;
            markField(wrap, true);
        }

        function clearErrors(scope) {
            (scope || form).querySelectorAll('.field.has-error').forEach(function (wrap) {
                wrap.classList.remove('has-error');
                const error = wrap.querySelector('.field-error');
                if (error) error.textContent = '';
                markField(wrap, false);
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
                    const heading = target.querySelector('h2, h3, legend');
                    if (heading) {
                        // Move focus to the new step so keyboard and screen
                        // reader users do not walk the whole header again.
                        if (!heading.hasAttribute('tabindex')) heading.setAttribute('tabindex', '-1');
                        heading.focus({ preventScroll: true });
                    }
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
                        if (status) {
                            status.classList.remove('is-error');
                            status.textContent = '';
                        }
                        if (success) {
                            success.classList.add('is-visible');
                            success.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
                            const thanks = success.querySelector('h2');
                            if (thanks) {
                                if (!thanks.hasAttribute('tabindex')) thanks.setAttribute('tabindex', '-1');
                                thanks.focus({ preventScroll: true });
                            }
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

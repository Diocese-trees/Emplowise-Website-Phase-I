/* Emplowise FAQ: accordions, live search and category filter. Loaded only on the FAQ page. */
(function () {
    'use strict';

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
})();

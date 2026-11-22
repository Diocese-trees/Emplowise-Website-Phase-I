/* Emplowise team: tabbed profile window. Loaded only on Meet The Team. */
(function () {
    'use strict';

    // -----------------------------------------------------------------
    // Team: tabbed window showcase
    // -----------------------------------------------------------------
    const teamTabs = Array.prototype.slice.call(document.querySelectorAll('[data-team-tab]'));
    const teamPanels = Array.prototype.slice.call(document.querySelectorAll('[data-team-panel]'));

    function activateTeamTab(id) {
        if (!id) return;
        const target = teamPanels.filter(function (panel) {
            return panel.getAttribute('data-team-panel') === id;
        })[0];
        if (!target) return;

        teamTabs.forEach(function (tab) {
            const active = tab.getAttribute('data-team-tab') === id;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
            tab.setAttribute('tabindex', active ? '0' : '-1');
        });
        teamPanels.forEach(function (panel) {
            panel.classList.toggle('is-active', panel === target);
        });
    }

    teamTabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () {
            activateTeamTab(tab.getAttribute('data-team-tab'));
        });

        tab.addEventListener('keydown', function (event) {
            let next;
            switch (event.key) {
                case 'ArrowRight':
                case 'ArrowDown':
                    next = index + 1;
                    break;
                case 'ArrowLeft':
                case 'ArrowUp':
                    next = index - 1;
                    break;
                case 'Home':
                    next = 0;
                    break;
                case 'End':
                    next = teamTabs.length - 1;
                    break;
                default:
                    return;
            }
            event.preventDefault();
            const target = teamTabs[(next + teamTabs.length) % teamTabs.length];
            activateTeamTab(target.getAttribute('data-team-tab'));
            target.focus();
        });
    });
})();

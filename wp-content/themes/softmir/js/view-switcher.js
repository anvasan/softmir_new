/**
 * View Switcher for Software Archive
 * Toggles between grid, list, and table views
 */
(function () {
    const STORAGE_KEY = 'softmir_view_mode';
    const wrapper = document.getElementById('catalog-cards-wrapper');
    const buttons = document.querySelectorAll('.view-switch-btn');

    if (!wrapper || !buttons.length) return;

    // Restore saved view
    const saved = localStorage.getItem(STORAGE_KEY) || 'list';
    setView(saved);

    // Button click handlers
    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var mode = this.getAttribute('data-view');
            setView(mode);
            localStorage.setItem(STORAGE_KEY, mode);
        });
    });

    function setView(mode) {
        // Update buttons
        buttons.forEach(function (b) {
            b.classList.toggle('active', b.getAttribute('data-view') === mode);
        });

        // Update wrapper classes
        wrapper.className = 'catalog-cards-wrapper view-' + mode;

        // Show/hide the correct card set
        var gridCards = wrapper.querySelector('.cards-grid');
        var listCards = wrapper.querySelector('.cards-list');
        var tableCards = wrapper.querySelector('.cards-table');

        if (gridCards) gridCards.style.display = mode === 'grid' ? '' : 'none';
        if (listCards) listCards.style.display = mode === 'list' ? '' : 'none';
        if (tableCards) tableCards.style.display = mode === 'table' ? '' : 'none';
    }
})();

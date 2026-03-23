export function initOrdonnancesIndex(root = document) {
    const searchInput = root.querySelector('#ordSearchInput');
    const statusFilter = root.querySelector('#statusFilter');
    const applyFilters = root.querySelector('#applyFilters');
    const exportBtn = root.querySelector('#exportBtn');

    if (!searchInput || !statusFilter || !applyFilters || applyFilters.dataset.medisysBound === '1') {
        return;
    }

    applyFilters.dataset.medisysBound = '1';

    const getSearchValue = () => (searchInput.value || '').trim();
    let searchTimeout = null;

    const apply = () => {
        const statut = statusFilter.value;
        const search = getSearchValue();
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);

        if (statut) {
            params.set('statut', statut);
        } else {
            params.delete('statut');
        }

        if (search) {
            params.set('search', search);
        } else {
            params.delete('search');
        }

        window.location.href = `${url.pathname}?${params.toString()}`;
    };

    exportBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        window.alert('Fonctionnalite d export a implementer');
    });

    applyFilters.addEventListener('click', apply);
    statusFilter.addEventListener('change', apply);

    searchInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            apply();
        }
    });

    searchInput.addEventListener('input', () => {
        window.clearTimeout(searchTimeout);
        searchTimeout = window.setTimeout(() => {
            const value = getSearchValue();
            if (value.length === 0 || value.length >= 3) {
                apply();
            }
        }, 500);
    });
}
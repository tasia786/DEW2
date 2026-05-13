
function initialize() {
    const tableSelect = document.getElementById('s-table');
    const form = document.getElementById('search-form');
    const clearButton = document.getElementById('btn-clear');

    // alegem prima varianta truthy (sa nu fie null sau goala)
    if (!tableSelect || !form || !clearButton) {
        return;
    }

    tableSelect.addEventListener('change', () => {
        getFilters(tableSelect.value);
        clearResults();
    });

    clearButton.addEventListener('click', () => {
        setTimeout(() => {
            getFilters(tableSelect.value);
            clearResults();
        }, 0);
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        submitSearchForm();
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-result-view]');
        if (!button) return;

        resultViewMode = button.dataset.resultView;
        renderResults(currentResults, currentTableName);
    });
}



function submitSearchForm() {
    const tableSelect = document.getElementById('s-table');
    if (!tableSelect) return;

    const tableName = tableSelect.value;
    if (!tableName) {
        renderError('Selectați o tabelă mai întâi.');
        return;
    }

    const endpoint = tableEndpoints[tableName];
    if (!endpoint) {
        renderError('Nu există endpoint în backend pentru această tabelă.');
        return;
    }

    const params = new URLSearchParams();
    const filters = collectSelectedFilters();

    if (tableName === 'campaigns_projects') {
        params.set('activity', 'project');
    }

    if (tableName === 'prevention_activities') {
        params.set('activity', 'prevention');
    }

    if (tableName.startsWith('crimes_')) {
        const type = tableName.replace('crimes_', '');
        params.set('type', type);
    }

    if (tableName.startsWith('criminal_')) {
        const type = tableName.replace('criminal_', '');
        params.set('type', type);
    }

    Object.entries(filters).forEach(([filterName, value]) => {
        const paramName = queryParamMap[filterName];
        if (!paramName || !value) return;
        params.set(paramName, normalizeFilterValue(filterName, value));
    });

    const url = `${endpoint}?${params.toString()}`;
    fetchResults(url, tableName);
}

function normalizeFilterValue(filterName, value) {
    // Corecție pentru tipul de drog (în special pentru tabela Emergencies)
    if (filterName === 'drug') {
        // Dacă valoarea este "Stimulanti" (fără ț), o transformăm în "Stimulanți" (cu ț)
        if (value === 'Stimulanti') return 'Stimulanți';
        
    }

    // Păstrăm și logica existentă pentru legi
    if (filterName === 'law') {
        return value.replaceAll('Legea nr. ', 'Legea ');
    }

    return value;
}

function collectSelectedFilters() {
    const inputs = Array.from(document.querySelectorAll('#dynamic-filters input[type="checkbox"]'));
    const selected = {};

    inputs.forEach((input) => {
        if (input.checked) {
            if (!selected[input.name]) {
                selected[input.name] = [];
            }
            selected[input.name].push(input.value);
        }
    });

    //separam prin virgula
    const finalFilters = {};
    for (const key in selected) {
        finalFilters[key] = selected[key].join(',');
    }
    return finalFilters;
}

async function fetchResults(url, tableName) {
    const resultsContainer = document.getElementById('results-empty-state');
    if (!resultsContainer) return;

    const response = await fetch(url); //asteapta pana primeste raspuns complet de la server
    if (!response.ok) {
        const errorText = await response.text();
        renderError(errorText || 'Search request failed.');//daca serverul trimite body gol
        return;
    }

    const data = await response.json();
    renderResults(data, tableName);
}


function renderError(message) {
    const resultsContainer = document.getElementById('results-empty-state');
    if (!resultsContainer) return;

    resultsContainer.innerHTML = `<div class="empty-state">${message}</div>`;
}

function clearResults() {
    const resultsContainer = document.getElementById('results-empty-state');
    if (!resultsContainer) return;
    resultsContainer.innerHTML = `<div class="card">Loading..</div>`;
}

document.addEventListener('DOMContentLoaded', initialize);

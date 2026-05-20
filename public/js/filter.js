//construim endpoint ul final care ne da valorile pt un filtru
async function fetchOptionsFromBackend(tableName, filterName) {
    const endpoint = optionsEndpoints[tableName];
    const query = { column: filterName };
    if (!endpoint || !query) {
        return null;
    }

    const url = `${endpoint}?${new URLSearchParams(query).toString()}`;
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text() || 'Nu s-au putut încărca opțiunile pentru acest filtru.');
        }
        const options = await response.json();
        return Array.isArray(options) ? options : null;
    } catch (error) {
        console.warn('Filter options fetch failed:', error, url);
        return null;
    }
}


//cream html ul care afiseaza optiunile pt filtru si il inseram
function renderOptions(groupId, fieldName, options) {

    const container = document.getElementById(groupId);
    container.innerHTML = '';
    options.forEach((option) => {
        const value = String(option);
        //doar pt beneficiary va fi label ul diferit de value
        const label = translateOption(fieldName, value);
        const checkboxId = `${groupId}-${value.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_-]/g, '')}`;

        container.insertAdjacentHTML('beforeend', `
            <div style="display: flex; align-items: center; gap: var(--space-2);">
                <input type="checkbox" id="${checkboxId}" name="${fieldName}" value="${value}" style="cursor: pointer;">
                <label for="${checkboxId}" style="cursor: pointer; font-size: var(--font-size-md); color: var(--color-text-primary);">${label}</label>
            </div>
        `);
    });
}

//imbina functiile fetchOptionsFromBackend si renderOptions
async function populate(tableName, groupId, fieldName) {
    const container = document.getElementById(groupId);
    if (!container) return;


    const backendOptions = await fetchOptionsFromBackend(tableName, fieldName);
    if (backendOptions != null && backendOptions.length > 0) {
        renderOptions(groupId, fieldName, backendOptions);
        return;
    }

    container.innerHTML = `<div class="empty-state">Nu există opțiuni pentru acest filtru.</div>`;
}

//creeaza filtrele pt fiecare tabel si le populeaza cu valorile primite din backend
function getFilters(tableName) {
    const container = document.getElementById('dynamic-filters');
    if (!container) return;

    container.innerHTML = '';

    if (!tableName || !tableFilters[tableName]) {
        return;
    }

    tableFilters[tableName].forEach((filter) => {
        let margin = 'var(--space-4)';
        let fieldHTML = '';

        if (filter.type === 'select') {
            fieldHTML = `
        <div class="form-group" style="margin-bottom: ${margin};">
          <div class="form-label">${filter.label}</div>
          <div id="s-${filter.name}-group" style="display: flex; flex-direction: column; gap: var(--space-2);"></div>
        </div>`;
        }

        container.insertAdjacentHTML('beforeend', fieldHTML);

        populate(tableName, `s-${filter.name}-group`, filter.name);
    });
}
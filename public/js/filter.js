function getOptionsQuery(tableName, filterName) {
    switch (tableName) {
        case 'seizures':
        case 'emergencies':
            return { column: filterName };
        case 'campaigns_projects':
            return { activity: 'project', column: filterName };
        case 'prevention_activities':
            return { activity: 'prevention', column: filterName };
        case 'crimes_general':
            return { type: 'general', column: filterName };
        case 'crimes_sex':
            return { type: 'sex', column: filterName };
        case 'crimes_law':
            return { type: 'law', column: filterName };
        case 'crimes_sentences':
            return { type: 'sentences', column: filterName };
        case 'criminal_groups':
            return { type: 'groups', column: filterName };
        default:
            return null;
    }
}

async function fetchOptionsFromBackend(tableName, filterName) {
    const endpoint = optionsEndpoints[tableName];
    const query = getOptionsQuery(tableName, filterName);
    if (!endpoint || !query) {
        return null;
    }

    const url = `${endpoint}?${new URLSearchParams(query).toString()}`;
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text() || 'Failed to load filter options.');
        }
        const options = await response.json();
        return Array.isArray(options) ? options : null;
    } catch (error) {
        console.warn('Filter options fetch failed:', error, url);
        return null;
    }
}

function renderOptions(groupId, fieldName, options) {
    const container = document.getElementById(groupId);
    if (!container) return;

    container.innerHTML = '';
    options.forEach((option) => {
        const value = String(option);
        const label = translateOption(fieldName, value);
        const checkboxId = `${groupId}-${value.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_-]/g, '')}`;

        container.insertAdjacentHTML('beforeend', `
            <div style="display: flex; align-items: center; gap: var(--space-2);">
                <input type="checkbox" id="${checkboxId}" name="${fieldName}" value="${escapeHtml(value)}" style="cursor: pointer;">
                <label for="${checkboxId}" style="cursor: pointer; font-size: var(--font-size-md); color: var(--color-text-primary);">${escapeHtml(label)}</label>
            </div>
        `);
    });
}

async function populate(tableName, groupId, fieldName) {
    const container = document.getElementById(groupId);
    if (!container) return;


    const backendOptions = await fetchOptionsFromBackend(tableName, fieldName);
    if (Array.isArray(backendOptions) && backendOptions.length > 0) {
        renderOptions(groupId, fieldName, backendOptions);
        return;
    }

    container.innerHTML = `<div class="empty-state">No filter options available.</div>`;
}

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

        if (filter.type === 'select') {
            populate(tableName, `s-${filter.name}-group`, filter.name);
        }
    });
}
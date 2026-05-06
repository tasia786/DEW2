const tableEndpoints = {
    seizures: '/api/Seizure.php',
    emergencies: '/api/Emergency.php',
    campaigns_projects: '/api/Campaigns.php',
    prevention_activities: '/api/Campaigns.php',
    crimes_general: '/api/Crimes.php',
    crimes_sex: '/api/Crimes.php',
    crimes_law: '/api/Crimes.php',
    crimes_sentences: '/api/Crimes.php',
    criminal_groups: '/api/Crimes.php',
};

const queryParamMap = {
    year: 'year',
    drug_type: 'drugType',
    seizure_type: 'seizureType',
    criterion_value: 'criterion',
    drug: 'drug',
    environment: 'environment',
    beneficiary: 'beneficiary',
    category: 'category',
    sex: 'sex',
    age_category: 'ageCategory',
    article: 'article',
    sentence_type: 'sentenceType',
    law: 'law',
    field_name: 'field_name',
};

const selectOptions = {
    year: ['2021', '2022'],
    drug_type: ['Heroină', 'Cocaină', 'Amfetamină', 'Metamfetamină', 'MDMA', 'Metadonă', 'Oxicodonă', 'Morfină', 'LSD', 'Codeină', 'Ciuperci halucinogene', 'Canabinoizi sintetici', 'Catinone', '2C-X', 'Cactus/Mescalină', 'Triptamine', 'ALTELE', 'Cannabis', 'Rezină de canabis', 'Ulei de canbis', 'Ketamină', 'Benzodiazepine', 'Barbiturice', 'Buprenorfină', 'Zolpidem', 'Amfepramonă', 'Mitraginină', 'Masă plante de cababis', 'Fragmente vegetale cu THC'],
    seizure_type: ['Grame', 'Comprimate', 'Doze/Buc', 'Mililitri', 'Nr. Capturi'],
    criterion_value: ['Masculin', 'Feminin', '<25', '25-34', '>35', 'Oral/fumat/prizat', 'Altele', 'Injectabil', 'Consum singular', 'Consum combinat', 'Intoxicație', 'Utilizare nocivă', 'Dependență', 'Sevraj', 'Tulburări de comportament', 'Supradoză', 'Alte diagnostice', 'Testare toxicologică'],
    drug: ['Canabis', 'Stimulanți', 'Opiacee', 'NSP'],
    environment: ['În mediul preşcolar', 'În mediul primar, gimnazial şi liceal', 'În mediul universitar', 'În familie', 'În comunitate'],
    beneficiary: ['activitati_total', 'copii', 'parinti', 'cadre_didactice', 'studenti', 'persoane', 'elevi'],
    category: ['Persoane cercetate', 'Persoane trimise în judecată', 'Persoane condamnate'],
    sex: ['Bărbați', 'Femei'],
    age_category: ['Majori', 'Minori'],
    article: ['Art.2 din Legea nr. 143/2000', 'Art.3 din Legea nr. 143/2000', 'Art.4 din Legea nr. 143/2000', 'Art.7 din Legea nr. 143/2000', 'Legea nr. 194/2011'],
    sentence_type: ['Executarea pedepsei', 'Suspendarea pedepsei', 'Amendă penală', 'Amânarea executării pedepsei'],
    law: ['Legea nr. 143/2000', 'Legea nr. 194/2011'],
    field_name: ['Grupări identificate', 'Număr persoane implicate în grupări'],
};

const tableFilters = {
    seizures: [
        { name: 'year', label: 'Year', type: 'select' },
        { name: 'drug_type', label: 'Drug Type', type: 'select' },
        { name: 'seizure_type', label: 'Seizure Type', type: 'select' },
    ],
    emergencies: [
        { name: 'year', label: 'Year', type: 'select' },
        { name: 'criterion_value', label: 'Criterion', type: 'select' },
        { name: 'drug', label: 'Drug', type: 'select' },
    ],
    campaigns_projects: [
        { name: 'year', label: 'Year', type: 'select' },
    ],
    prevention_activities: [
        { name: 'year', label: 'Year', type: 'select' },
        { name: 'environment', label: 'Environment', type: 'select' },
        { name: 'beneficiary', label: 'Beneficiary', type: 'select' },
    ],
    crimes_general: [
        { name: 'year', label: 'Year', type: 'select' },
        { name: 'category', label: 'Category', type: 'select' },
    ],
    crimes_sex: [
        { name: 'year', label: 'Year', type: 'select' },
        { name: 'sex', label: 'Sex', type: 'select' },
        { name: 'age_category', label: 'Age Category', type: 'select' },
    ],
    crimes_law: [
        { name: 'year', label: 'Year', type: 'select' },
        { name: 'article', label: 'Article', type: 'select' },
    ],
    crimes_sentences: [
        { name: 'year', label: 'Year', type: 'select' },
        { name: 'sentence_type', label: 'Sentence Type', type: 'select' },
        { name: 'law', label: 'Law', type: 'select' },
    ],
    criminal_groups: [
        { name: 'year', label: 'Year', type: 'select' },
        { name: 'field_name', label: 'Field', type: 'select' },
    ],
};

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
            populate(`s-${filter.name}-group`, filter.name);
        }
    });
}

function populate(groupId, fieldName) {
    const container = document.getElementById(groupId);
    if (!container) return;

    const options = selectOptions[fieldName] || [];
    options.forEach((option) => {
        const label = option;
        const value = option;

        const checkboxId = `${groupId}-${value.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_-]/g, '')}`;

        container.insertAdjacentHTML('beforeend', `
            <div style="display: flex; align-items: center; gap: var(--space-2);">
                <input type="checkbox" id="${checkboxId}" name="${fieldName}" value="${value}" style="cursor: pointer;">
                <label for="${checkboxId}" style="cursor: pointer; font-size: var(--font-size-md); color: var(--color-text-primary);">${label}</label>
            </div>
        `);
    });
}

function submitSearchForm() {
    const tableSelect = document.getElementById('s-table');
    if (!tableSelect) return;

    const tableName = tableSelect.value;
    if (!tableName) {
        renderError('Please select a table first.');
        return;
    }

    const endpoint = tableEndpoints[tableName];
    if (!endpoint) {
        renderError('No backend endpoint configured for this table.');
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

    Object.entries(filters).forEach(([filterName, value]) => {
        const paramName = queryParamMap[filterName];
        if (!paramName || !value) return;
        params.set(paramName, value);
    });

    const url = `${endpoint}?${params.toString()}`;
    fetchResults(url);
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

async function fetchResults(url) {
    const resultsContainer = document.getElementById('results-empty-state');
    if (!resultsContainer) return;

    const response = await fetch(url); //asteapta pana primeste raspuns complet de la server
    if (!response.ok) {
        const errorText = await response.text();
        renderError(errorText || 'Search request failed.');//daca serverul trimite body gol
        return;
    }

    const data = await response.json();
    renderResults(data);
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


function renderResults(data) {
    const resultsContainer = document.getElementById('results-empty-state');
    if (!resultsContainer) return;

    //trb sa contina un array de obiecte json
    if (!Array.isArray(data) || data.length === 0) {
        resultsContainer.innerHTML = `<div class="empty-state">No results found.</div>`;
        return;
    }

    //ia numele coloanelor
    const headers = Object.keys(data[0]);

    //pt fiecare coloana din header cream un th
    const headersHtml = headers
    .map(header => `
        <th style="padding: var(--space-3) var(--space-4); text-align: left; font-size: var(--font-size-md); color: var(--color-text-secondary); font-weight: 600; white-space: nowrap; width: ${100 / headers.length}%;">
            ${header}
        </th>`)
    .join('');

    //parcurgem randurile din data si le mapam la td
    const rowsHtml = data.map((item, index) => {
        const cells = headers
            .map(header => `
            <td style="padding: var(--space-3) var(--space-4); font-size: var(--font-size-sm); color: var(--color-text-primary); border-bottom: 1px solid var(--color-border);">
                ${item[header]}
            </td>`)
            .join('');
        return `<tr style="background: ${index % 2 === 0 ? 'white' : 'var(--color-bg-hover)'};">${cells}</tr>`;
    }).join('');


    resultsContainer.innerHTML = `
    <div class="card">
        <div>
            <div class="card-title" style="margin-bottom: var(--space-6)">${data.length} result${data.length === 1 ? '' : 's'}</div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <tr style="background: var(--color-bg-hover); border-bottom: 2px solid var(--color-border);">
                        ${headersHtml}
                    </tr>
                </thead>
                <tbody>
                    ${rowsHtml}
                </tbody>
            </table>
        </div>
    </div>
`;
}
document.addEventListener('DOMContentLoaded', initialize);

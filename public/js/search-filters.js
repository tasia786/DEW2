

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

const selectOptions = {
    year: ['2021', '2022'],
    drug_type: ['Heroină', 'Cocaină', 'Amfetamină', 'Metamfetamină', 'MDMA', 'Metadonă', 'Oxicodonă', 'Morfină', 'LSD', 'Codeină', 'Ciuperci halucinogene', 'Canabinoizi sintetici', 'Catinone', '2C-X', 'Cactus/Mescalină', 'Triptamine', 'ALTELE', 'Cannabis', 'Rezină de canabis', 'Ulei de canbis', 'Ketamină', 'Benzodiazepine', 'Barbiturice', 'Buprenorfină', 'Zolpidem', 'Amfepramonă', 'Mitraginină', 'Masă plante de cababis', 'Fragmente vegetale cu THC'],
    seizure_type: ['Grame', 'Comprimate', 'Doze/Buc', 'Mililitri', 'Nr. Capturi'],
    criterion_value: ['Masculin', 'Feminin', '<25', '25-34', '>35', 'Oral/fumat/prizat', 'Altele', 'Injectabil', 'Consum singular', 'Consum combinat', 'Intoxicație', 'Utilizare nocivă', 'Dependență', 'Sevraj', 'Tulburări de comportament', 'Supradoză', 'Alte diagnostice', 'Testare toxicologică'],
    drug: ['Canabis', 'Stimulanți', 'Opiacee', 'NSP'],
    environment: ['În mediul preşcolar', 'În mediul primar, gimnazial şi liceal', 'În mediul universitar', 'În familie', 'În comunitate'],
    beneficiary: ['Nr. activități', 'Nr. copii', 'Nr. părinți', 'Nr. cadre didactice'],
    category: ['Persoane cercetate', 'Persoane trimise în judecată', 'Persoane condamnate'],
    sex: ['Bărbați', 'Femei'],
    age_category: ['Majori', 'Minori'],
    article: ['Art.2 din Legea nr. 143/2000', 'Art.3 din Legea nr. 143/2000', 'Art.4 din Legea nr. 143/2000', 'Art.7 din Legea nr. 143/2000', 'Legea nr. 194/2011'],
    sentence_type: ['Executarea pedepsei', 'Suspendarea pedepsei', 'Amendă penală', 'Amânarea executării pedepsei'],
    law: ['Legea nr. 143/2000', 'Legea nr. 194/2011'],
    field_name: ['Grupări identificate', 'Număr persoane implicate în grupări']
};

function initialize() {
    const tableSelect = document.getElementById('s-table');
    const form = document.getElementById('search-form');
    const clearButton = document.getElementById('btn-clear');

    if (!tableSelect || !form || !clearButton) {
        return;
    }

    tableSelect.addEventListener('change', () => {
        getFilters(tableSelect.value);
    });

    clearButton.addEventListener('click', () => {
        setTimeout(() => {
            getFilters(tableSelect.value);
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

    const options = selectOptions[fieldName];

    options.forEach((option) => {
        const value = option.toLowerCase().replace(' ', '_');
        const checkboxId = `${groupId}-${value}`;

        container.insertAdjacentHTML('beforeend', `
            <div style="display: flex; align-items: center; gap: var(--space-2);">
                <input type="checkbox" id="${checkboxId}" name="${fieldName}" value="${value}" style="cursor: pointer;">
                <label for="${checkboxId}" style="cursor: pointer; font-size: var(--font-size-m); color: var(--color-text-primary);">${option}</label>
            </div>
        `);
    });
}


document.addEventListener('DOMContentLoaded', initialize);

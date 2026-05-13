function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function translateOption(fieldName, value) {
    const translations = optionTranslations[fieldName];
    if (!translations) return value;

    return translations[value] || translations[String(value).trim()] || value;
}

function formatHeader(header) {
    const headerLabels = {
        beneficiaries_count: 'Nr. Beneficiari',
        age_category: 'Categorie Vârstă',
        criterion_value: 'Criteriu',
        drug_type: 'Drog',
        seizure_type: 'Captură',
        sentence_type: 'Sentință',
        field_name: 'Câmp',
        year: 'An',
        value: 'Valoare',
        name: 'Nume',
        drug: 'Drog',
        type: 'Tip',
        environment: 'Mediu',
        beneficiary: 'Beneficiari',
        category: 'Categorie',
        age_category : 'Categorie',
        article: 'Articol',
        law: 'Lege'
    };

    return headerLabels[header] || header.replaceAll('_', ' ').replace(/\b\w/g, char => char.toUpperCase());
}

function formatResultValue(header, value) {
    if (value === null || value === undefined) return '';
    return translateOption(header, String(value));
}
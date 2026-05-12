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
        beneficiaries_count: 'Beneficiaries',
        age_category: 'Age Category',
        criterion_value: 'Criterion',
        drug_type: 'Drug Type',
        seizure_type: 'Seizure Type',
        sentence_type: 'Sentence Type',
        field_name: 'Field',
    };

    return headerLabels[header] || header.replaceAll('_', ' ').replace(/\b\w/g, char => char.toUpperCase());
}

function formatResultValue(header, value) {
    if (value === null || value === undefined) return '';
    return translateOption(header, String(value));
}
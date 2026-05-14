
//se foloseste de optionTranslations pt a afisa valorile bune pt filtrul beneficiary
function translateOption(fieldName, value) {
    const translations = optionTranslations[fieldName];
    if (!translations) return value;

    return translations[value] || value;
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

    return headerLabels[header];
}


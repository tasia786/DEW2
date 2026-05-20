const tableEndpoints = {
    seizures: 'http://localhost:8080/api/seizures',
    emergencies: 'http://localhost:8080/api/emergencies',
    campaigns_projects: 'http://localhost:8080/api/campaigns-projects',
    prevention_activities: 'http://localhost:8080/api/prevention-activities',
    crimes_general: 'http://localhost:8080/api/crimes-general',
    crimes_sex: 'http://localhost:8080/api/crimes-sex',
    crimes_law: 'http://localhost:8080/api/crimes-law',
    crimes_sentences: 'http://localhost:8080/api/crimes-sentences',
    criminal_groups: 'http://localhost:8080/api/criminal-groups',
};

//mapam denumirile filtrelor din frontend la cele din backend ca sa putem face fetch ul
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
    field_name: 'fieldName',
    type: 'type',
};

const optionsEndpoints = {
    seizures: 'http://localhost:8080/api/seizures',
    emergencies: 'http://localhost:8080/api/emergencies',
    campaigns_projects: 'http://localhost:8080/api/campaigns-projects',
    prevention_activities: 'http://localhost:8080/api/prevention-activities',
    crimes_general: 'http://localhost:8080/api/crimes-general',
    crimes_sex: 'http://localhost:8080/api/crimes-sex',
    crimes_law: 'http://localhost:8080/api/crimes-law',
    crimes_sentences: 'http://localhost:8080/api/crimes-sentences',
    criminal_groups: 'http://localhost:8080/api/criminal-groups',
};

//coloana beneficiary este creata de noi in plus fata de csv si trb traduse valorile pe care le poate lua
const optionTranslations = {
    beneficiary: {
        'activitati_total': 'Activități total',
        'copii': 'Copii',
        'parinti': 'Părinți',
        'cadre_didactice': 'Cadre didactice',
        'studenti': 'Studenți',
        'persoane': 'Persoane',
        'elevi': 'Elevi',
    }
};

//numele filtrelor pt fiecare tabel
const tableFilters = {
    seizures: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'drug_type', label: 'Tip Drog', type: 'select' },
        { name: 'seizure_type', label: 'Tip Captură', type: 'select' },
    ],
    emergencies: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'criterion_value', label: 'Criteriu', type: 'select' },
        { name: 'drug', label: 'Drog', type: 'select' },
    ],
    campaigns_projects: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'type', label: 'Tip', type: 'select' }

    ],
    prevention_activities: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'environment', label: 'Mediu', type: 'select' },
        { name: 'beneficiary', label: 'Beneficiari', type: 'select' },
    ],
    crimes_general: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'category', label: 'Categorie', type: 'select' },
    ],
    crimes_sex: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'sex', label: 'Sex', type: 'select' },
        { name: 'age_category', label: 'Categorie vârstă', type: 'select' },
    ],
    crimes_law: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'article', label: 'Articol', type: 'select' },
    ],
    crimes_sentences: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'sentence_type', label: 'Tip pedeapsă', type: 'select' },
        { name: 'law', label: 'Lege', type: 'select' },
    ],
    criminal_groups: [
        { name: 'year', label: 'An', type: 'select' },
        { name: 'field_name', label: 'Câmp', type: 'select' }
    ],
};
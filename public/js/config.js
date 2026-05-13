const tableEndpoints = {
    seizures: 'http://localhost:8080/api/Seizure.php',
    emergencies: 'http://localhost:8080/api/Emergency.php',
    campaigns_projects: 'http://localhost:8080/api/Campaigns.php',
    prevention_activities: 'http://localhost:8080/api/Campaigns.php',
    crimes_general: 'http://localhost:8080/api/Crimes.php',
    crimes_sex: 'http://localhost:8080/api/Crimes.php',
    crimes_law: 'http://localhost:8080/api/Crimes.php',
    crimes_sentences: 'http://localhost:8080/api/Crimes.php',
    criminal_groups: 'http://localhost:8080/api/Crimes.php',
};

const queryParamMap = {
    year: 'year',
    drug_type: 'drugType',
    seizure_type: 'column',
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

const optionsEndpoints = {
    seizures: 'http://localhost:8080/api/OptionsSeizure.php',
    emergencies: 'http://localhost:8080/api/OptionsEmergency.php',
    campaigns_projects: 'http://localhost:8080/api/OptionsCampaigns.php',
    prevention_activities: 'http://localhost:8080/api/OptionsCampaigns.php',
    crimes_general: 'http://localhost:8080/api/OptionsCrimes.php',
    crimes_sex: 'http://localhost:8080/api/OptionsCrimes.php',
    crimes_law: 'http://localhost:8080/api/OptionsCrimes.php',
    crimes_sentences: 'http://localhost:8080/api/OptionsCrimes.php',
    criminal_groups: 'http://localhost:8080/api/OptionsCrimes.php',
};

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
        { name: 'year', label: 'An', type: 'select' }
    ],
};
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
    seizures: '/api/OptionsSeizure.php',
    emergencies: '/api/OptionsEmergency.php',
    campaigns_projects: '/api/OptionsCampaigns.php',
    prevention_activities: '/api/OptionsCampaigns.php',
    crimes_general: '/api/OptionsCrimes.php',
    crimes_sex: '/api/OptionsCrimes.php',
    crimes_law: '/api/OptionsCrimes.php',
    crimes_sentences: '/api/OptionsCrimes.php',
    criminal_groups: '/api/OptionsCrimes.php',
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
        { name: 'year', label: 'Year', type: 'select' }
    ],
};
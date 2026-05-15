<?php
require_once __DIR__ . '/../../dtos/SearchRequestSeizure.php';
require_once __DIR__ . '/../../config/Constant.php';

class SearchRequestSeizureValidator
{
    static public function validate(SearchRequestSeizure $request): array
    {
        if ($request->getYears() !== null) {
            foreach ($request->getYears() as $year) {
                if ($year > MAX_YEAR || $year < MIN_YEAR) {
                    return array('isSuccess' => false, 'message' => 'invalid years - <min >max');
                }
            }
        }

        $allowedSeizureTypes = [
            'Grame',
            'Comprimate',
            'Doze/Buc',
            'Mililitri',
            'Nr. Capturi'
        ];
        if ($request->getSeizureType() !== null) {
            foreach ($request->getSeizureType() as $seizureType) {
                if (!in_array($seizureType, $allowedSeizureTypes, true)) {
                    return ['isSuccess' => false, 'message' => 'invalid seizure type: "' . $seizureType . '"; accepted: ' . implode(', ', $allowedSeizureTypes)];
                }
            }
        }

        return array('isSuccess' => true);
    }
}

<?php
require_once __DIR__ . '/../../dtos/SearchRequestEmergency.php';
require_once __DIR__ . '/../../config/Constant.php';

class SearchRequestEmergencyValidator
{
    static public function validate(SearchRequestEmergency $request): array
    {
        if ($request->getYears() !== null) {
            foreach ($request->getYears() as $year) {
                if ($year > MAX_YEAR || $year < MIN_YEAR) {
                    return array('isSuccess' => false, 'message' => 'invalid years - <min >max');
                }
            }
        }

        $allowedDrugs = ['Canabis', 'Stimulanti', 'Opiacee', 'NSP'];
        if ($request->getDrug() !== null) {
            foreach ($request->getDrug() as $drug) {
                if (!in_array($drug, $allowedDrugs, true)) {
                    return ['isSuccess' => false, 'message' => 'invalid drug: "' . $drug . '"; accepted: ' . implode(', ', $allowedDrugs)];
                }
            }
        }

        return array('isSuccess' => true);
    }
}

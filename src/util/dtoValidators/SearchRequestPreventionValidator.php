<?php
require_once __DIR__ . '/../../dtos/SearchRequestPrevention.php';
require_once __DIR__ . '/../../config/Constant.php';

class SearchRequestPreventionValidator
{
    static public function validate(SearchRequestPrevention $request): array
    {
        if ($request->getYears() !== null) {
            foreach ($request->getYears() as $year) {
                if ($year > MAX_YEAR || $year < MIN_YEAR) {
                    return array('isSuccess' => false, 'message' => 'invalid years - <min >max');
                }
            }
        }

        $allowedTypes = [
            'activitati_total',
            'copii',
            'parinti',
            'cadre_didactice',
            'studenti',
            'persoane',
            'elevi'
        ];
        if ($request->getBeneficiary() !== null) {
            foreach ($request->getBeneficiary() as $beneficiary) {
                if (!in_array($beneficiary, $allowedTypes, true)) {
                    return ['isSuccess' => false, 'message' => 'invalid beneficiary: "' . $beneficiary . '"; accepted: ' . implode(', ', $allowedTypes)];
                }
            }
        }

        return array('isSuccess' => true);
    }
}

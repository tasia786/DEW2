<?php
require_once __DIR__ . '/../../dtos/SearchRequestCrimeSex.php';
require_once __DIR__ . '/../../config/Constant.php';

class SearchRequestCrimeSexValidator
{
    static public function validate(SearchRequestCrimeSex $request): array
    {
        if ($request->getYears() !== null) {
            foreach ($request->getYears() as $year) {
                if ($year > MAX_YEAR || $year < MIN_YEAR) {
                    return array('isSuccess' => false, 'message' => 'invalid years - <min >max');
                }
            }
        }

        $allowedSexValues = ['Bărbați', 'Femei'];
        if ($request->getSex() !== null) {
            foreach ($request->getSex() as $sex) {
                if (!in_array($sex, $allowedSexValues, true)) {
                    return ['isSuccess' => false, 'message' => 'invalid sex: "' . $sex . '"; accepted: ' . implode(', ', $allowedSexValues)];
                }
            }
        }

        $allowedAgeCategories = ['Majori', 'Minori'];
        if ($request->getAgeCategory() !== null) {
            foreach ($request->getAgeCategory() as $ageCategory) {
                if (!in_array($ageCategory, $allowedAgeCategories, true)) {
                    return ['isSuccess' => false, 'message' => 'invalid age category: "' . $ageCategory . '"; accepted: ' . implode(', ', $allowedAgeCategories)];
                }
            }
        }

        return array('isSuccess' => true);
    }
}

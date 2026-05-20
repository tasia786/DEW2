<?php
require_once __DIR__ . '/../../dtos/SearchRequestCrimeSentence.php';
require_once __DIR__ . '/../../config/Constant.php';

class SearchRequestCrimeSentenceValidator
{
    static public function validate(SearchRequestCrimeSentence $request): array
    {
        if ($request->getYears() !== null) {
            foreach ($request->getYears() as $year) {
                if ($year > MAX_YEAR || $year < MIN_YEAR) {
                    return array('isSuccess' => false, 'message' => 'invalid years - <min >max');
                }
            }
        }

        $allowedLaws = ['Legea nr. 143/2000', 'Legea nr. 194/2011'];
        if ($request->getLaw() !== null) {
            foreach ($request->getLaw() as $law) {
                if (!in_array($law, $allowedLaws, true)) {
                    return ['isSuccess' => false, 'message' => 'invalid law: "' . $law . '"; accepted: ' . implode(', ', $allowedLaws)];
                }
            }
        }

        return array('isSuccess' => true);
    }
}

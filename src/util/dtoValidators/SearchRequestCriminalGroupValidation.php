<?php
require_once __DIR__ . '/../../dtos/SearchRequestCriminalGroup.php';
require_once __DIR__ . '/../../config/Constant.php';

class SearchRequestCriminalGroupValidator
{
    static public function validate(SearchRequestCriminalGroup $request): array
    {
        if ($request->getYears() !== null) {
            foreach ($request->getYears() as $year) {
                if ($year > MAX_YEAR || $year < MIN_YEAR) {
                    return array('isSuccess' => false, 'message' => 'invalid years - <min >max');
                }
            }
        }

        return array('isSuccess' => true);
    }
}

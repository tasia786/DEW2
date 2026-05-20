<?php
require_once __DIR__ . '/../../dtos/SearchRequestCampaign.php';
require_once __DIR__ . '/../../config/Constant.php';

class SearchRequestCampaignValidator
{
    static public function validate(SearchRequestCampaign $request): array
    {
        if ($request->getYears() !== null) {
            foreach ($request->getYears() as $year) {
                if ($year > MAX_YEAR || $year < MIN_YEAR) {
                    return array('isSuccess' => false, 'message' => 'invalid years - <min >max');
                }
            }
        }

        $allowedTypes = ['proiect', 'campanie'];
        if ($request->getType() !== null && !in_array($request->getType(), $allowedTypes, true)) {
            return array('isSuccess' => false, 'message' => 'invalid type; accepted campanie, proiect');
        }

        return array('isSuccess' => true);
    }
}

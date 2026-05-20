<?php
class SearchRequestEmergency
{
    private ?array $years;
    private ?array $criterionValue;
    private ?array $drug;
    private int $nmbPage;

    public function __construct(?array $years, ?array $criterionValue, ?array $drug, int $nmbPage)
    {
        $this->years = $years;
        $this->criterionValue = $criterionValue;
        $this->drug = $drug;
        $this->nmbPage = $nmbPage;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }

    public function getCriterionValue(): ?array
    {
        return $this->criterionValue;
    }

    public function getDrug(): ?array
    {
        return $this->drug;
    }

    public function getNmbPage(): int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestEmergency(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => trim($y), explode(',', $data['year']));
    }

    if (!isset($data['criterion']) || trim($data['criterion']) === '') {
        $criterion = null;
    } elseif (!Validator::isCommaSeparatedStrings($data['criterion'])) {
        return ['isSuccess' => false, 'message' => 'invalid criterion format'];
    } else {
        $criterion = array_map(fn($d) => trim($d), explode(',', $data['criterion']));
    }

    if (!isset($data['drug']) || trim($data['drug']) === '') {
        $drug = null;
    } elseif (!Validator::isCommaSeparatedStrings($data['drug'])) {
        return ['isSuccess' => false, 'message' => 'invalid drug format'];
    } else {
        $drug = array_map(fn($d) => trim($d), explode(',', $data['drug']));
    }

    $nmbPage = 1;
    if (isset($data['nmbPage']) && trim($data['nmbPage']) !== '') {
        if (!ctype_digit(trim($data['nmbPage']))) {
            return ['isSuccess' => false, 'message' => 'invalid page format'];
        }
        $nmbPage = (int) $data['nmbPage'];
    }

    return ['isSuccess' => true, 'object' => new SearchRequestEmergency($years, $criterion, $drug, $nmbPage)];
}

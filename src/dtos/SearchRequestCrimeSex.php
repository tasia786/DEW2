<?php
class SearchRequestCrimeSex
{
    private ?array $years;
    private ?array $sex;
    private ?array $ageCategory;
    private int $nmbPage;

    public function __construct(?array $years, ?array $sex, ?array $ageCategory, int $nmbPage)
    {
        $this->years = $years;
        $this->sex = $sex;
        $this->ageCategory = $ageCategory;
        $this->nmbPage = $nmbPage;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }

    public function getSex(): ?array
    {
        return $this->sex;
    }

    public function getAgeCategory(): ?array
    {
        return $this->ageCategory;
    }

    public function getNmbPage(): int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestCrimeSex(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => (int) trim($y), explode(',', $data['year']));
    }

    if (!isset($data['sex']) || trim($data['sex']) === '') {
        $sex = null;
    } elseif (!Validator::isCommaSeparatedStrings($data['sex'])) {
        return ['isSuccess' => false, 'message' => 'invalid sex format'];
    } else {
        $sex = array_map(fn($s) => trim($s), explode(',', $data['sex']));
    }

  
    if (!isset($data['ageCategory']) || trim($data['ageCategory']) === '') {
        $ageCategory = null;
    } elseif (!Validator::isCommaSeparatedStrings($data['ageCategory'])) {
        return ['isSuccess' => false, 'message' => 'invalid ageCategory format'];
    } else {
        $ageCategory = array_map(fn($s) => trim($s), explode(',', $data['ageCategory']));
    }


    $nmbPage = 1;
    if (isset($data['nmbPage']) && trim($data['nmbPage']) !== '') {
        if (!ctype_digit(trim($data['nmbPage']))) {
            return ['isSuccess' => false, 'message' => 'invalid page format'];
        }
        $nmbPage = (int) $data['nmbPage'];
    }

    return ['isSuccess' => true, 'object' => new SearchRequestCrimeSex($years, $sex, $ageCategory, $nmbPage)];
}

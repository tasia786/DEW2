<?php
class SearchRequestCrimeGeneral
{
    private ?array $years;
    private ?array $category;
    private int $nmbPage;

    public function __construct(?array $years, ?array $category, int $nmbPage)
    {
        $this->years = $years;
        $this->category = $category;
        $this->nmbPage = $nmbPage;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }

    public function getCategory(): ?array
    {
        return $this->category;
    }

    public function getNmbPage(): int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestCrimeGeneral(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => (int) trim($y), explode(',', $data['year']));
    }

    if (!isset($data['category']) || trim($data['category']) === '') {
        $category = null;
    } elseif (!Validator::isCommaSeparatedStrings($data['category'])) {
        return ['isSuccess' => false, 'message' => 'invalid category format'];
    } else {
        $category = array_map(fn($c) => trim($c), explode(',', $data['category']));
    }

    $nmbPage = 1;
    if (isset($data['nmbPage']) && trim($data['nmbPage']) !== '') {
        if (!ctype_digit(trim($data['nmbPage']))) {
            return ['isSuccess' => false, 'message' => 'invalid page format'];
        }
        $nmbPage = (int) $data['nmbPage'];
    }

    return ['isSuccess' => true, 'object' => new SearchRequestCrimeGeneral($years, $category, $nmbPage)];
}

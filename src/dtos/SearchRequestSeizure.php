<?php
class SearchRequestSeizure
{
    private ?array $years;
    private ?array $drugType;
    private ?array $seizureType;
    private int $nmbPage;

    public function __construct(?array $years, ?array $drugType, ?array $seizureType, int $nmbPage)
    {
        $this->years = $years;
        $this->drugType = $drugType;
        $this->seizureType = $seizureType;
        $this->nmbPage = $nmbPage;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }

    public function getDrugType(): ?array
    {
        return $this->drugType;
    }

    public function getSeizureType(): ?array
    {
        return $this->seizureType;
    }

    public function getNmbPage(): int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestSeizure(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => (int) trim($y), explode(',', $data['year']));
    }

    if (!isset($data['drugType']) || trim($data['drugType']) === '') {
        $drugType = null;
    } else {
        $drugType = array_map(fn($d) => trim($d), explode(',', $data['drugType']));
    }

    if (!isset($data['seizureType']) || trim($data['seizureType']) === '') {
        $seizureType = null;
    } else {
        $seizureType = array_map(fn($d) => trim($d), explode(',', $data['seizureType']));
    }

    $nmbPage = 1;
    if (isset($data['nmbPage']) && trim($data['nmbPage']) !== '') {
        if (!ctype_digit(trim($data['nmbPage']))) {
            return ['isSuccess' => false, 'message' => 'invalid page format'];
        }
        $nmbPage = (int) $data['nmbPage'];
    }

    return ['isSuccess' => true, 'object' => new SearchRequestSeizure($years, $drugType, $seizureType, $nmbPage)];
}

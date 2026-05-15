<?php
class SearchRequestPrevention
{
    private ?array $years;
    private ?array $environment;
    private ?array $beneficiary;
    private int $nmbPage;

    public function __construct(?array $years, ?array $environment, ?array $beneficiary, int $nmbPage)
    {
        $this->years       = $years;
        $this->environment = $environment;
        $this->beneficiary = $beneficiary;
        $this->nmbPage     = $nmbPage;
    }

    public function getEnvironment(): ?array
    {
        return $this->environment;
    }
    public function getBeneficiary(): ?array
    {
        return $this->beneficiary;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }

    public function getNmbPage(): int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestPrevention(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => (int) trim($y), explode(',', $data['year']));
    }

    if (!isset($data['environment']) || trim($data['environment']) === '') {
        $environment = null;
    } elseif (!Validator::isCommaSeparatedStrings($data['environment'])) {
        return ['isSuccess' => false, 'message' => 'invalid environment format'];
    } else {
        $environment = array_map(fn($e) => trim($e), explode(',', $data['environment']));
    }

    if (!isset($data['beneficiary']) || trim($data['beneficiary']) === '') {
        $beneficiary = null;
    } elseif (!Validator::isCommaSeparatedStrings($data['beneficiary'])) {
        return ['isSuccess' => false, 'message' => 'invalid beneficiary format'];
    } else {
        $beneficiary = array_map(fn($b) => trim($b), explode(',', $data['beneficiary']));
    }

    $nmbPage = 1;
    if (isset($data['nmbPage']) && trim($data['nmbPage']) !== '') {
        if (!ctype_digit(trim($data['nmbPage']))) {
            return ['isSuccess' => false, 'message' => 'invalid page format'];
        }
        $nmbPage = (int) $data['nmbPage'];
    }

    return ['isSuccess' => true, 'object' => new SearchRequestPrevention($years, $environment, $beneficiary, $nmbPage)];
}

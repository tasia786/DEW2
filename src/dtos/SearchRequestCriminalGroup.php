<?php
class SearchRequestCriminalGroup
{
    private ?array $years;
    private ?array $fieldName;
    private ?int $nmbPage;

    public function __construct(?array $years, ?array $fieldName, ?int $nmbPage)
    {
        $this->years = $years;
        $this->fieldName = $fieldName;
        $this->nmbPage = $nmbPage;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }

    public function getFieldName(): ?array
    {
        return $this->fieldName;
    }

    public function getNmbPage(): ?int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestCriminalGroup(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => (int) trim($y), explode(',', $data['year']));
    }

    if (!isset($data['fieldName']) || trim($data['fieldName']) === '') {
        $fieldName = null;
    } else {
        $fieldName = array_map(fn($l) => trim($l), explode(',', $data['fieldName']));
    }

    if (!isset($data['nmbPage']) || trim($data['nmbPage']) === '') {
        $nmbPage = null;
    } elseif (!ctype_digit(trim($data['nmbPage']))) {
        return ['isSuccess' => false, 'message' => 'invalid page format'];
    } else {
        $nmbPage = (int) $data['nmbPage'];
    }
    
    return ['isSuccess' => true, 'object' => new SearchRequestCriminalGroup($years, $fieldName, $nmbPage)];
}

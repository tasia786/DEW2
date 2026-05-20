<?php
class SearchRequestCampaign
{
    private ?array $years;
    private ?string $type;
    private ?int $nmbPage;

    public function __construct(?array $years, ?string $type, ?int $nmbPage)
    {
        $this->years = $years;
        $this->type = $type;
        $this->nmbPage = $nmbPage;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function getNmbPage(): ?int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestCampaign(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => (int) trim($y), explode(',', $data['year']));
    }

    $type = isset($data['type']) ? trim($data['type']) : null;

    if (!isset($data['nmbPage']) || trim($data['nmbPage']) === '') {
        $nmbPage = null;
    } elseif (!ctype_digit(trim($data['nmbPage']))) {
        return ['isSuccess' => false, 'message' => 'invalid page format'];
    } else {
        $nmbPage = (int) $data['nmbPage'];
    }


    return array('isSuccess' => true, 'object' => new SearchRequestCampaign($years, $type, $nmbPage));
}

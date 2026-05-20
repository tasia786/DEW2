<?php
class SearchRequestCrimeSentence
{
    private ?array $years;
    private ?array $sentenceType;
    private ?array $law;
    private int $nmbPage;

    public function __construct(?array $years, ?array $sentenceType, ?array $law, int $nmbPage)
    {
        $this->years = $years;
        $this->sentenceType = $sentenceType;
        $this->law = $law;
        $this->nmbPage = $nmbPage;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }

    public function getSentenceType(): ?array
    {
        return $this->sentenceType;
    }

    public function getLaw(): ?array
    {
        return $this->law;
    }

    public function getNmbPage(): int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestCrimeSentence(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => (int) trim($y), explode(',', $data['year']));
    }

    if (!isset($data['sentenceType']) || trim($data['sentenceType']) === '') {
        $sentenceType = null;
    } else {
        $sentenceType = array_map(fn($s) => trim($s), explode(',', $data['sentenceType']));
    }


    if (!isset($data['law']) || trim($data['law']) === '') {
        $law = null;
    } else {
        $law = array_map(fn($l) => trim($l), explode(',', $data['law']));
    }

    $nmbPage = 1;
    if (isset($data['nmbPage']) && trim($data['nmbPage']) !== '') {
        if (!ctype_digit(trim($data['nmbPage']))) {
            return ['isSuccess' => false, 'message' => 'invalid page format'];
        }
        $nmbPage = (int) $data['nmbPage'];
    }

    return ['isSuccess' => true, 'object' => new SearchRequestCrimeSentence($years, $sentenceType, $law, $nmbPage)];
}

<?php
class SearchRequestCrimeLaw
{
    private ?array $years;
    private ?array $article;
    private int $nmbPage;

    public function __construct(?array $years, ?array $article, int $nmbPage)
    {
        $this->years = $years;
        $this->article = $article;
        $this->nmbPage = $nmbPage;
    }

    public function getYears(): ?array
    {
        return $this->years;
    }

    public function getArticle(): ?array
    {
        return $this->article;
    }

    public function getNmbPage(): int
    {
        return $this->nmbPage;
    }
}

function parseSearchRequestCrimeLaw(array $data): array
{
    if (!isset($data['year']) || trim($data['year']) === '') {
        $years = null;
    } elseif (!Validator::isCommaSeparatedIntegers($data['year'])) {
        return ['isSuccess' => false, 'message' => 'invalid years format'];
    } else {
        $years = array_map(fn($y) => (int) trim($y), explode(',', $data['year']));
    }

    if (!isset($data['article']) || trim($data['article']) === '') {
        $article = null;
    } else {
        $article = array_map(fn($s) => trim($s), explode(',', $data['article']));
    }

    $nmbPage = 1;
    if (isset($data['nmbPage']) && trim($data['nmbPage']) !== '') {
        if (!ctype_digit(trim($data['nmbPage']))) {
            return ['isSuccess' => false, 'message' => 'invalid page format'];
        }
        $nmbPage = (int) $data['nmbPage'];
    }

    return ['isSuccess' => true, 'object' => new SearchRequestCrimeLaw($years, $article, $nmbPage)];
}

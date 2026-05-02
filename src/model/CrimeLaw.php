<?php

class CrimeLaw {
    private ?int $id;
    private int $year;
    private string $article;
    private ?int $value;

    public function __construct(?int $id, int $year, string $article, ?int $value) {
        $this->id = $id;
        $this->year = $year;
        $this->article = $article;
        $this->value = $value;
    }

    public function getId(): ?int { return $this->id; }
    public function getYear(): int { return $this->year; }
    public function getArticle(): string { return $this->article; }
    public function getValue(): ?int { return $this->value; }

    public static function fromArrayToObjsSet(array $arrayObj): array {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['article'],
                $obj['value'] ?? null
            );
        }
        return $models;
    }

    public function toArray(): array {
        return [
            'id'      => $this->id,
            'year'    => $this->year,
            'article' => $this->article,
            'value'   => $this->value,
        ];
    }
}
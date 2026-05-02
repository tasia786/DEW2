<?php

class CrimeSentence {
    private ?int $id;
    private int $year;
    private string $sentenceType;
    private string $law;
    private ?int $value;

    public function __construct(?int $id, int $year, string $sentenceType, string $law, ?int $value) {
        $this->id = $id;
        $this->year = $year;
        $this->sentenceType = $sentenceType;
        $this->law = $law;
        $this->value = $value;
    }

    public function getId(): ?int { return $this->id; }
    public function getYear(): int { return $this->year; }
    public function getSentenceType(): string { return $this->sentenceType; }
    public function getLaw(): string { return $this->law; }
    public function getValue(): ?int { return $this->value; }

    public static function fromArrayToObjsSet(array $arrayObj): array {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['sentence_type'],
                $obj['law'],
                $obj['value'] ?? null
            );
        }
        return $models;
    }
}
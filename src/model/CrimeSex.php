<?php

class CrimeSex {
    private ?int $id;
    private int $year;
    private string $sex;
    private string $ageCategory;
    private ?int $value;

    public function __construct(?int $id, int $year, string $sex, string $ageCategory, ?int $value) {
        $this->id = $id;
        $this->year = $year;
        $this->sex = $sex;
        $this->ageCategory = $ageCategory;
        $this->value = $value;
    }

    public function getId(): ?int { return $this->id; }
    public function getYear(): int { return $this->year; }
    public function getSex(): string { return $this->sex; }
    public function getAgeCategory(): string { return $this->ageCategory; }
    public function getValue(): ?int { return $this->value; }

    public static function fromArrayToObjsSet(array $arrayObj): array {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['sex'],
                $obj['age_category'],
                $obj['value'] ?? null
            );
        }
        return $models;
    }

    public function toArray(): array {
        return [
            'id'           => $this->id,
            'year'         => $this->year,
            'sex'          => $this->sex,
            'age_category' => $this->ageCategory,
            'value'        => $this->value,
        ];
    }
}
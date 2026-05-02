<?php

class Seizure {
    private ?int $id;
    private int $year;
    private string $drugType;
    private string $seizureType;
    private ?float $value;

    public function __construct(?int $id, int $year, string $drugType, string $seizureType, ?float $value) {
        $this->id = $id;
        $this->year = $year;
        $this->drugType = $drugType;
        $this->seizureType = $seizureType;
        $this->value = $value;
    }

    public function getId(): ?int { return $this->id; }
    public function getYear(): int { return $this->year; }
    public function getDrugType(): string { return $this->drugType; }
    public function getSeizureType(): string { return $this->seizureType; }
    public function getValue(): ?float { return $this->value; }

    public static function fromArrayToObjSet(array $arrayObj): array {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['drug_type'],
                $obj['seizure_type'],
                $obj['value'] ?? null
            );
        }
        return $models;
    }
}
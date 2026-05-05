<?php

class CriminalGroup {
    private ?int $id;
    private int $year;
    private ?string $fieldName;
    private ?int $value;

    public function __construct(?int $id, int $year, ?string $fieldName, ?int $value) {
        $this->id = $id;
        $this->year = $year;
        $this->fieldName = $fieldName;
        $this->value = $value;
    }

    public function getId(): ?int { return $this->id; }
    public function getYear(): int { return $this->year; }
    public function getFieldName(): ?string { return $this->fieldName; }
    public function getValue(): ?int { return $this->value; }

    public static function fromArrayToObjsSet(array $arrayObj): array {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['field_name'] ?? null,
                $obj['value'] ?? null
            );
        }
        return $models;
    }

    public function toArray(): array {
        return [
            'id'         => $this->id,
            'year'       => $this->year,
            'field_name' => $this->fieldName,
            'value'      => $this->value,
        ];
    }
}
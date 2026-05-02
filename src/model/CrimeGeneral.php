<?php
class CrimeGeneral {
    private ?int $id;
    private int $year;
    private string $category;
    private ?int $value;

    public function __construct(?int $id, int $year, string $category, ?int $value) {
        $this->id = $id;
        $this->year = $year;
        $this->category = $category;
        $this->value = $value;
    }

    public function getId(): ?int { return $this->id; }
    public function getYear(): int { return $this->year; }
    public function getCategory(): string { return $this->category; }
    public function getValue(): ?int { return $this->value; }

    public static function fromArrayToObjsSet(array $arrayObj): array {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['category'],
                $obj['value'] ?? null
            );
        }
        return $models;
    }

    public function toArray(): array {
        return [
            'id'       => $this->id,
            'year'     => $this->year,
            'category' => $this->category,
            'value'    => $this->value,
        ];
    }
}
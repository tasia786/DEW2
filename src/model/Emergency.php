<?php
class Emergency
{
    private ?int $id;
    private int $year;
    private string $criterionValue;
    private string $drug;
    private ?float $value;

    public function __construct(?int $id, int $year, string $criterionValue, string $drug, ?float $value)
    {
        $this->id = $id;
        $this->year = $year;
        $this->criterionValue = $criterionValue;
        $this->drug = $drug;
        $this->value = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getYear(): int
    {
        return $this->year;
    }
    public function getCriterionValue(): string
    {
        return $this->criterionValue;
    }
    public function getDrug(): string
    {
        return $this->drug;
    }
    public function getValue(): ?float
    {
        return $this->value;
    }

    public static function fromArrayToObjsSet(array $arrayObj): array
    {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['criterion_value'],
                $obj['drug'],
                $obj['value'] ?? null
            );
        }
        return $models;
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'year'           => $this->year,
            'criterion_value' => $this->criterionValue,
            'drug'           => $this->drug,
            'value'          => $this->value,
        ];
    }
}

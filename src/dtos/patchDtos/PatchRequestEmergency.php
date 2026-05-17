<?php
class PatchRequestEmergency
{
    private int $id;
    private ?int $year;
    private ?string $criterionValue;
    private ?string $drug;
    private ?float $value;

    public function __construct(int $id, ?int $year, ?string $criterionValue, ?string $drug, ?float $value)
    {
        $this->id = $id;
        $this->year = $year;
        $this->criterionValue = $criterionValue;
        $this->drug = $drug;
        $this->value = $value;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getYear(): ?int
    {
        return $this->year;
    }
    public function getCriterionValue(): ?string
    {
        return $this->criterionValue;
    }
    public function getDrug(): ?string
    {
        return $this->drug;
    }
    public function getValue(): ?float
    {
        return $this->value;
    }

    public function hasChanges(): bool
    {
        if ($this->year !== null || $this->criterionValue !== null || $this->drug !== null || $this->value !== null) {
            return true;
        }
        return false;
    }
}

function parsePatchRequestEmergency(?string $id, array $data): array
{
    if ($id === null || trim($id) === '') {
        return ['isSuccess' => false, 'message' => 'id is required'];
    }

    if (!isset($data['year']) || trim($data['year']) === '') {
        $year = null;
    } elseif (!ctype_digit((string)$data['year'])) {
        return ['isSuccess' => false, 'message' => 'year must be valid'];
    } else {
        $year = (int) $data['year'];
    }

    if (!isset($data['criterionValue']) || trim($data['criterionValue']) === '') {
        $criterionValue = null;
    } else {
        $criterionValue = trim($data['criterionValue']);
    }

    if (!isset($data['drug']) || trim($data['drug']) === '') {
        $drug = null;
    } else {
        $drug = trim($data['drug']);
    }

    if (!isset($data['value']) || trim($data['value']) === '') {
        $value = null;
    } elseif (!is_numeric($data['value'])) {
        return ['isSuccess' => false, 'message' => 'value must be valid'];
    } else {
        $value = (float)trim($data['value']);
    }

    return array('isSuccess' => true, 'object' => new PatchRequestEmergency($id, $year, $criterionValue, $drug, $value));
}

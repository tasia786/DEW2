<?php
class PatchRequestCriminalGroup
{
    private int $id;
    private ?int $year;
    private ?string $fieldName;
    private ?int $value;

    public function __construct(int $id, ?int $year, ?string $fieldName, ?int $value)
    {
        $this->id = $id;
        $this->year = $year;
        $this->fieldName = $fieldName;
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
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }
    public function getValue(): ?int
    {
        return $this->value;
    }

    public function hasChanges(): bool
    {
        if ($this->year !== null || $this->fieldName !== null || $this->value !== null) {
            return true;
        }
        return false;
    }
}

function parsePatchRequestCriminalGroup(?string $id, array $data): array
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

    if (!isset($data['fieldName']) || trim($data['fieldName']) === '') {
        $fieldName = null;
    } else {
        $fieldName = trim($data['fieldName']);
    }

    if (!isset($data['value']) || trim($data['value']) === '') {
        $value = null;
    } elseif (!ctype_digit((string)$data['value'])) {
        return ['isSuccess' => false, 'message' => 'value must be valid'];
    } else {
        $value = (int) $data['value'];
    }

    return array('isSuccess' => true, 'object' => new PatchRequestCriminalGroup($id, $year, $fieldName, $value));
}

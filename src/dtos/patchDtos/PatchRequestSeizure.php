<?php
class PatchRequestSeizure
{
    private int $id;
    private ?int $year;
    private ?string $drugType;
    private ?string $seizureType;
    private ?float $value;

    public function __construct(int $id, ?int $year, ?string $drugType, ?string $seizureType, ?float $value)
    {
        $this->id = $id;
        $this->year = $year;
        $this->drugType = $drugType;
        $this->seizureType = $seizureType;
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
    public function getDrugType(): ?string
    {
        return $this->drugType;
    }
    public function getSeizureType(): ?string
    {
        return $this->seizureType;
    }
    public function getValue(): ?float
    {
        return $this->value;
    }

    public function hasChanges(): bool
    {
        if ($this->year !== null || $this->drugType !== null || $this->seizureType !== null || $this->value !== null) {
            return true;
        }
        return false;
    }
}

function parsePatchRequestSeizure(?string $id, array $data): array
{
    if ($id === null || trim($id) === '') {
        return ['isSuccess' => false, 'message' => 'id is required'];
    } elseif (!ctype_digit($id)) {
        return ['isSuccess' => false, 'message' => 'id must be valid'];
    } else {
        $id = (int)$id;
    }

    if (!isset($data['year']) || trim($data['year']) === '') {
        $year = null;
    } elseif (!ctype_digit((string)$data['year'])) {
        return ['isSuccess' => false, 'message' => 'year must be valid'];
    } else {
        $year = (int) $data['year'];
    }

    if (!isset($data['drugType']) || trim($data['drugType']) === '') {
        $drugType = null;
    } else {
        $drugType = trim($data['drugType']);
    }

    if (!isset($data['seizureType']) || trim($data['seizureType']) === '') {
        $seizureType = null;
    } else {
        $seizureType = trim($data['seizureType']);
    }

    if (!isset($data['value']) || trim($data['value']) === '') {
        $value = null;
    } elseif (!is_numeric($data['value'])) {
        return ['isSuccess' => false, 'message' => 'value must be valid'];
    } else {
        $value = (float)trim($data['value']);
    }

    return array('isSuccess' => true, 'object' => new PatchRequestSeizure($id, $year, $drugType, $seizureType, $value));
}

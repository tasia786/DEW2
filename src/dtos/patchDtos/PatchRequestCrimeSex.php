<?php
class PatchRequestCrimeSex
{
    private int $id;
    private ?int $year;
    private ?string $sex;
    private ?string $ageCategory;
    private ?int $value;

    public function __construct(int $id, ?int $year, ?string $sex, ?string $ageCategory, ?int $value)
    {
        $this->id = $id;
        $this->year = $year;
        $this->sex = $sex;
        $this->ageCategory = $ageCategory;
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
    public function getSex(): ?string
    {
        return $this->sex;
    }
    public function getAgeCategory(): ?string
    {
        return $this->ageCategory;
    }
    public function getValue(): ?int
    {
        return $this->value;
    }

    public function hasChanges(): bool
    {
        if ($this->year !== null || $this->sex !== null || $this->ageCategory !== null || $this->value !== null) {
            return true;
        }
        return false;
    }
}

function parsePatchRequestCrimeSex(?string $id, array $data): array
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

    if (!isset($data['sex']) || trim($data['sex']) === '') {
        $sex = null;
    } else {
        $sex = trim($data['sex']);
    }

    if (!isset($data['ageCategory']) || trim($data['ageCategory']) === '') {
        $ageCategory = null;
    } else {
        $ageCategory = trim($data['ageCategory']);
    }

    if (!isset($data['value']) || trim($data['value']) === '') {
        $value = null;
    } elseif (!ctype_digit((string)$data['value'])) {
        return ['isSuccess' => false, 'message' => 'value must be valid'];
    } else {
        $value = (int) $data['value'];
    }

    return array('isSuccess' => true, 'object' => new PatchRequestCrimeSex($id, $year, $sex, $ageCategory, $value));
}

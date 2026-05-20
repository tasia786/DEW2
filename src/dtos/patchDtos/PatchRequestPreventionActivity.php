<?php
class PatchRequestPreventionActivity
{
    private int $id;
    private ?int $year;
    private ?string $environment;
    private ?string $beneficiary;
    private ?float $value;

    public function __construct(int $id, ?int $year, ?string $environment, ?string $beneficiary, ?float $value)
    {
        $this->id = $id;
        $this->year = $year;
        $this->environment = $environment;
        $this->beneficiary = $beneficiary;
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
    public function getEnvironment(): ?string
    {
        return $this->environment;
    }
    public function getBeneficiary(): ?string
    {
        return $this->beneficiary;
    }
    public function getValue(): ?float
    {
        return $this->value;
    }

    public function hasChanges(): bool
    {
        if ($this->year !== null || $this->environment !== null || $this->beneficiary !== null || $this->value !== null) {
            return true;
        }
        return false;
    }
}

function parsePatchRequestPreventionActivity(?string $id, array $data): array
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

    if (!isset($data['environment']) || trim($data['environment']) === '') {
        $environment = null;
    } else {
        $environment = trim($data['environment']);
    }

    if (!isset($data['beneficiary']) || trim($data['beneficiary']) === '') {
        $beneficiary = null;
    } else {
        $beneficiary = trim($data['beneficiary']);
    }

    if (!isset($data['value']) || trim($data['value']) === '') {
        $value = null;
    } elseif (!is_numeric($data['value'])) {
        return ['isSuccess' => false, 'message' => 'value must be valid'];
    } else {
        $value = (float)trim($data['value']);
    }

    return array('isSuccess' => true, 'object' => new PatchRequestPreventionActivity($id, $year, $environment, $beneficiary, $value));
}

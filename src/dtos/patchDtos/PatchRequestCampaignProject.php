<?php
class PatchRequestCampaignProject
{
    private int $id;
    private ?int $year;
    private ?string $type;
    private ?string $name;
    private ?int $beneficiariesCount;

    public function __construct(int $id, ?int $year, ?string $type, ?string $name, ?int $beneficiariesCount)
    {
        $this->id = $id;
        $this->year = $year;
        $this->type = $type;
        $this->name = $name;
        $this->beneficiariesCount = $beneficiariesCount;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getYear(): ?int
    {
        return $this->year;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function getBeneficiariesCount(): ?int
    {
        return $this->beneficiariesCount;
    }

    public function hasChanges(): bool
    {
        if ($this->year !== null || $this->type !== null || $this->name !== null || $this->beneficiariesCount !== null) {
            return true;
        }
        return false;
    }
}

function parsePatchRequestCampaignProject(?string $id, array $data): array
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

    if (!isset($data['type']) || trim($data['type']) === '') {
        $type = null;
    } else {
        $type = trim($data['type']);
    }

    if (!isset($data['name']) || trim($data['name']) === '') {
        $name = null;
    } else {
        $name = trim($data['name']);
    }

    if (!isset($data['beneficiariesCount']) || trim($data['beneficiariesCount']) === '') {
        $beneficiariesCount = null;
    } elseif (!ctype_digit((string)$data['beneficiariesCount'])) {
        return ['isSuccess' => false, 'message' => 'beneficiariesCount must be valid'];
    } else {
        $beneficiariesCount = (int) $data['beneficiariesCount'];
    }

    return array('isSuccess' => true, 'object' => new PatchRequestCampaignProject($id, $year, $type, $name, $beneficiariesCount));
}

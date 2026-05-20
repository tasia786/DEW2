<?php
class PatchRequestCrimeSentence
{
    private int $id;
    private ?int $year;
    private ?string $sentenceType;
    private ?string $law;
    private ?int $value;

    public function __construct(int $id, ?int $year, ?string $sentenceType, ?string $law, ?int $value)
    {
        $this->id = $id;
        $this->year = $year;
        $this->sentenceType = $sentenceType;
        $this->law = $law;
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
    public function getSentenceType(): ?string
    {
        return $this->sentenceType;
    }
    public function getLaw(): ?string
    {
        return $this->law;
    }
    public function getValue(): ?int
    {
        return $this->value;
    }

    public function hasChanges(): bool
    {
        if ($this->year !== null || $this->sentenceType !== null || $this->law !== null || $this->value !== null) {
            return true;
        }
        return false;
    }
}

function parsePatchRequestCrimeSentence(?string $id, array $data): array
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

    if (!isset($data['sentenceType']) || trim($data['sentenceType']) === '') {
        $sentenceType = null;
    } else {
        $sentenceType = trim($data['sentenceType']);
    }

    if (!isset($data['law']) || trim($data['law']) === '') {
        $law = null;
    } else {
        $law = trim($data['law']);
    }

    if (!isset($data['value']) || trim($data['value']) === '') {
        $value = null;
    } elseif (!ctype_digit((string)$data['value'])) {
        return ['isSuccess' => false, 'message' => 'value must be valid'];
    } else {
        $value = (int) $data['value'];
    }

    return array('isSuccess' => true, 'object' => new PatchRequestCrimeSentence($id, $year, $sentenceType, $law, $value));
}

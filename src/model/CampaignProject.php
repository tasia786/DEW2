<?php
class CampaignProject {
    private ?int $id;
    private int $year;
    private string $type;
    private string $name;
    private ?int $beneficiariesCount;

    public function __construct(?int $id, int $year, string $type, string $name, ?int $beneficiariesCount) {
        $this->id = $id;
        $this->year = $year;
        $this->type = $type;
        $this->name = $name;
        $this->beneficiariesCount = $beneficiariesCount;
    }

    public function getId(): ?int { return $this->id; }
    public function getYear(): int { return $this->year; }
    public function getType(): string { return $this->type; }
    public function getName(): string { return $this->name; }
    public function getBeneficiariesCount(): ?int { return $this->beneficiariesCount; }

    public static function fromArrayToObjsSet(array $arrayObj): array {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['type'],
                $obj['name'],
                $obj['beneficiaries_count'] ?? null
            );
        }
        return $models;
    }

    public function toArray(): array {
        return [
            'id'                  => $this->id,
            'year'                => $this->year,
            'type'                => $this->type,
            'name'                => $this->name,
            'beneficiaries_count' => $this->beneficiariesCount,
        ];
    }
}
<?php
class PreventionActivity {
    private ?int $id;
    private int $year;
    private string $environment;
    private string $beneficiary;
    private ?float $value;

    public function __construct(?int $id, int $year, string $environment, string $beneficiary, ?float $value) {
        $this->id = $id;
        $this->year = $year;
        $this->environment = $environment;
        $this->beneficiary = $beneficiary;
        $this->value = $value;
    }

    public function getId(): ?int { return $this->id; }
    public function getYear(): int { return $this->year; }
    public function getEnvironment(): string { return $this->environment; }
    public function getBeneficiary(): string { return $this->beneficiary; }
    public function getValue(): ?float { return $this->value; }

    public static function fromArrayToObjsSet(array $arrayObj): array {
        $models = [];
        foreach ($arrayObj as $obj) {
            $models[] = new self(
                $obj['id'] ?? null,
                $obj['year'],
                $obj['environment'],
                $obj['beneficiary'],
                $obj['value'] ?? null
            );
        }
        return $models;
    }

    public function toArray(): array {
        return [
            'id'          => $this->id,
            'year'        => $this->year,
            'environment' => $this->environment,
            'beneficiary' => $this->beneficiary,
            'value'       => $this->value,
        ];
    }
}
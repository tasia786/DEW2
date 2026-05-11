<?php
class User {
    private ?int $id;
    private string $username;
    private string $password;

    public function __construct(?int $id, string $username, string $password) {
        $this->id       = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getPassword(): string { return $this->password; }

    public static function fromArrayToObj(array $row): self {
        return new self(
            $row['id'] ?? null, 
            $row['username'], 
            $row['password']
        );
    }

    public function toArray(): array {
        return [
            'id'           => $this->id,
            'username'     => $this->username,
            'password'     => $this->password
        ];
    }
}
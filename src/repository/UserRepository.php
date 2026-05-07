<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../config/Database.php';

class UserRepository
{
    private PDOStatement $selectStmt;

    public function __construct()
    {
        $this->selectStmt = Database::getConnection()->prepare(
            'SELECT * FROM users WHERE username = ?'
        );
    }

    public function findByUsername(string $username): ?User
    {
        $this->selectStmt->execute([$username]);
        $row = $this->selectStmt->fetch();

        return $row ? User::fromArrayToObj($row) : null;
    }
}

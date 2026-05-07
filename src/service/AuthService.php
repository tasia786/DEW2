<?php
require_once __DIR__ . '/../repository/UserRepository.php';

class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function login(string $username, string $password): bool
    {
        $user = $this->userRepo->findByUsername($username);

        if (!$user || !password_verify($password, $user->getPassword())) {
            return false;
        }
        session_start();
        $_SESSION['admin']   = true;
        return true;
    }

    public function logout(): void
    {
        session_start();
        session_destroy();
    }
}

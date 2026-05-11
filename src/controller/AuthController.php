<?php
require_once __DIR__ . '/../service/AuthService.php';
require_once __DIR__ . '/../util/Response.php';

class AuthController {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::badRequest('Only POST allowed');
            return;
        }

        $body     = json_decode(file_get_contents('php://input'), true);
        $username = trim($body['username'] ?? '');
        $password = trim($body['password'] ?? '');

        if (empty($username) || empty($password)) {
            Response::badRequest('Username and password required');
            return;
        }

        if (!$this->authService->login($username, $password)) {
            Response::error('Invalid credentials', 401);
            return;
        }

        Response::json(['message' => 'Login successful']);
    }

    public function logout(): void {
        $this->authService->logout();
        Response::json(['message' => 'Logged out']);
    }
}
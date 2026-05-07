<?php

require_once __DIR__ . '/Response.php';
class Auth
{
    public static function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION['admin'] ?? false) !== true) {
            Response::error('Unauthorized', 401);
            exit;
        }
    }
}

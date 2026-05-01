<?php

class Database
{
    private static $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO('sqlite:' . __DIR__ . '/../../data/database.sqlite');
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Error("database dude" . $e->errorInfo);
            }
        }
        return self::$pdo;
    }
}

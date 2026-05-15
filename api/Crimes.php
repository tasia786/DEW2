<?php
require_once __DIR__ . '/../src/controller/CrimeController.php';
$controller = new CrimesController();
$method = $_SERVER['REQUEST_METHOD'];

match($method) {
    'GET'    => $controller->executeFilter(),
    'DELETE' => $controller->delete(),
};
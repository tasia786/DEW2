<?php
require_once __DIR__ . '/../src/controller/SeizureController.php';
$controller = new SeizuresController();
$method = $_SERVER['REQUEST_METHOD'];

match($method) {
    'GET'    => $controller->executeFilter(),
    'DELETE' => $controller->delete(),
};
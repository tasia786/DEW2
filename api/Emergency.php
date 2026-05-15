<?php
require_once __DIR__ . '/../src/controller/EmergencyController.php';
$controller = new EmergencyController(); 
$method = $_SERVER['REQUEST_METHOD'];

match($method) {
    'GET'    => $controller->executeFilter(),
    'DELETE' => $controller->delete(),
};
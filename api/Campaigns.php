<?php
require_once __DIR__ . '/../src/controller/CampaignController.php';
$controller = new CampaignController();
$method = $_SERVER['REQUEST_METHOD'];

match($method) {
    'GET'    => $controller->executeFilter(),
    'DELETE' => $controller->delete(),
};
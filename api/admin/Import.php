<?php
require_once __DIR__ . '/../../src/controller/ImportController.php';
require_once __DIR__ . '/../../src/util/Auth.php';

Auth::requireAdmin();
(new ImportController())->handle();
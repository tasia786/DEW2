<?php
require_once __DIR__ . '/../service/ImportService.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../config/Constant.php';
require_once __DIR__ . '/../util/Validator.php';

class ImportController
{
    private ImportService $service;

    public function __construct()
    {
        $this->service = new ImportService();
    }

    public function handle(): void
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::badRequest('Only POST allowed');
            return;
        }

        if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            Response::badRequest('No file uploaded or upload error');
            return;
        }

        $ext = strtolower(pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            Response::badRequest('Only CSV files accepted');
            return;
        }

        $table = $_POST['table'] ?? '';
        if (!Validator::validString($table, ['crimes', 'seizures', 'emergencies', 'prevention'])) {
            Response::badRequest('Invalid table; accepted: crimes, seizures, emergencies, prevention');
            return;
        }

        $year = $_POST['year'] ?? '';
        if (!Validator::validInt($year, MIN_YEAR, MAX_YEAR)) {
            Response::badRequest('Invalid year');
            return;
        }

        $tmpPath = $_FILES['csv']['tmp_name'];

        try {
            $this->service->import($table, (int)$year, $tmpPath);
            Response::json('Import succesful');
        } catch (InvalidArgumentException $e) {
            Response::badRequest($e->getMessage());
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}

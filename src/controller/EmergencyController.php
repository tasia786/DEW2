<?php
require_once __DIR__ . '/../repository/EmergenciesRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../config/Constant.php';

class EmergencyController
{
    private EmergenciesRepository $emergencyRepo;

    public function __construct()
    {
        $this->emergencyRepo = new EmergenciesRepository();
    }

    public function executeFilter(): void
    {
        $values = [];
        $columnNames = [];
        if (isset($_GET['year']) && !empty($_GET['year'])) {
            if (!Validator::validInt($_GET['year'], MIN_YEAR, MAX_YEAR)) {
                Response::badRequest('Invalid year');
                return;
            }
            array_push($values, $_GET['year']);
            array_push($columnNames, 'year');
        }

        if (isset($_GET['criterion']) && !empty($_GET['criterion'])) {
            array_push($values, $_GET['criterion']);
            array_push($columnNames, 'criterion_value');
        }

        if (isset($_GET['drug']) && !empty($_GET['drug'])) {
            if (!Validator::validString($_GET['drug'], ['Canabis', 'Stimulanți', 'Opiacee', 'NSP'])) {
                Response::badRequest('Invalid drog type; accepted: Canabis, Stimulanți, Opiacee, NSP');
                return;
            }
            array_push($values, $_GET['drug']);
            array_push($columnNames, 'drug');
        }
        $data = $this->emergencyRepo->selectWithFilter($values, $columnNames);
        $result = array_map(fn($e) => $e->toArray(), $data);
        Response::sendData($result);
    }
}

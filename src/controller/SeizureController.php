<?php
require_once __DIR__ . '/../repository/SeizuresRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../config/Constant.php';

class SeizuresController
{
    private SeizuresRepository $repo;

    public function __construct()
    {
        $this->repo = new SeizuresRepository();
    }

    public function executeFilter(): void
    {
        $values      = [];
        $columnNames = [];

        if (isset($_GET['year']) && !empty($_GET['year'])) {
            if (!Validator::validInt($_GET['year'], MIN_YEAR, MAX_YEAR)) {
                Response::badRequest('Invalid year');
                return;
            }
            array_push($values, $_GET['year']);
            array_push($columnNames, 'year');
        }

        if (isset($_GET['seizureType']) && !empty($_GET['seizureType'])) {
            if (!Validator::validString($_GET['seizureType'], [
                'Grame',
                'Comprimate',
                'Doze/Buc',
                'Mililitri',
                'Nr. Capturi'
            ])) {
                Response::badRequest('Invalid seizure type');
                return;
            }
            array_push($values, $_GET['seizureType']);
            array_push($columnNames, 'seizure_type');
        }

        if (isset($_GET['drugType']) && !empty($_GET['drugType'])) {
            array_push($values, $_GET['drugType']);
            array_push($columnNames, 'drug_type');
        }

        $data   = $this->repo->selectWithFilter($values, $columnNames);
        $result = array_map(fn($s) => $s->toArray(), $data);
        Response::sendData($result);
    }
}

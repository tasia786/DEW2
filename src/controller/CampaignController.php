<?php
require_once __DIR__ . '/../repository/CampaignsProjectsRepository.php';
require_once __DIR__ . '/../repository/PreventionActivitiesRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../config/Constant.php';

class CampaignController
{
    private CampaignsProjectsRepository  $campaignProjectRepo;
    private PreventionActivitiesRepository $preventionActivitiesRepo;

    public function __construct()
    {
        $this->campaignProjectRepo  = new CampaignsProjectsRepository();
        $this->preventionActivitiesRepo = new PreventionActivitiesRepository();
    }

    public function executeFilter(): void
    {
        //project = campanie/proiect
        //prevention = activitati de prevenire
        if (!isset($_GET['activity']) || empty($_GET['activity']) || !in_array($_GET['activity'], ['prevention', 'project'], true)) {
            Response::badRequest('activity is required; accepted: prevention, project');
            return;
        }

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

        if ($_GET['activity'] === 'project') {
            if (isset($_GET['type']) && !empty($_GET['type'])) {
                if (!Validator::validString($_GET['type'], ['campanie', 'proiect'])) {
                    Response::badRequest('Invalid type; accepted: campanie, proiect');
                    return;
                }
                array_push($values, $_GET['type']);
                array_push($columnNames, 'type');
            }
            $data = $this->campaignProjectRepo->selectWithFilter($values, $columnNames);
            $result = array_map(fn($c) => $c->toArray(), $data);
        } else {
            if (isset($_GET['environment']) && !empty($_GET['environment'])) {
                array_push($values, $_GET['environment']);
                array_push($columnNames, 'environment');
            }

            if (isset($_GET['beneficiary']) && !empty($_GET['beneficiary'])) {
                if (!Validator::validString($_GET['beneficiary'], [
                    'activitati_total', 
                    'copii', 
                    'parinti', 
                    'cadre_didactice', 
                    'studenti', 
                    'persoane', 
                    'elevi'])) {
                    Response::badRequest('Invalid beneficiary type; alowwed: activitati_total, copii, parinti, cadre_didactice, studenti, persoane, elevi');
                    return;
                }
                array_push($values, $_GET['beneficiary']);
                array_push($columnNames, 'beneficiary');
            }

            $data = $this->preventionActivitiesRepo->selectWithFilter($values, $columnNames);
            $result = array_map(fn($p) => $p->toArray(), $data);
        }

        Response::sendData($result);
    }
}

<?php
require_once __DIR__ . '/../repository/CampaignsProjectsRepository.php';
require_once __DIR__ . '/../repository/PreventionActivitiesRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../config/Constant.php';
require_once __DIR__ . '/../dtos/SearchRequestCampaign.php';
require_once __DIR__ . '/..//util/dtoValidators/SearchRequestCampaignValidator.php';
require_once __DIR__ . '/../dtos/SearchRequestPrevention.php';
require_once __DIR__ . '/..//util/dtoValidators/SearcRequestPreventionValidator.php';

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

        if ($_GET['activity'] === 'project') {
            $responseRequest = parseSearchRequestCampaign($_GET);
            if (!$responseRequest['isSuccess']) {
                Response::badRequest($responseRequest['message']);
                return;
            }

            $request = $responseRequest['object'];
            $validationResult = SearchRequestCampaignValidator::validate($request);
            if (!$validationResult['isSuccess']) {
                Response::badRequest($validationResult['message']);
                return;
            }
            $data = $this->campaignProjectRepo->search($request);
            $result = array_map(fn($p) => $p->toArray(), $data);

            Response::sendData($result);
        } else {
            $responseRequest = parseSearchRequestPrevention($_GET);
            if (!$responseRequest['isSuccess']) {
                Response::badRequest($responseRequest['message']);
                return;
            }

            $request = $responseRequest['object'];
            $validationResult = SearchRequestPreventionValidator::validate($request);
            if (!$validationResult['isSuccess']) {
                Response::badRequest($validationResult['message']);
                return;
            }
            $data = $this->preventionActivitiesRepo->search($request);
            $result = array_map(fn($p) => $p->toArray(), $data);

            Response::sendData($result);
        }
    }

    public function selectOptions(): void
    {
        if (!isset($_GET['activity']) || empty($_GET['activity']) || !in_array($_GET['activity'], ['prevention', 'project'], true)) {
            Response::badRequest('activity is required; accepted: prevention, project');
            return;
        }

        if (!isset($_GET['column']) || empty($_GET['column'])) {
            Response::badRequest("column must be specified");
            return;
        }

        if ($_GET['activity'] === 'project') {
            if (!Validator::validString($_GET['column'], ['id', 'year', 'type', 'name', 'value'])) {
                Response::badRequest('Invalid column; accepted: id, year, type, name, value');
                return;
            }
            Response::json($this->campaignProjectRepo->selectDistinct($_GET['column']));
        } else {
            if (!Validator::validString($_GET['column'], ['id', 'year', 'environment', 'beneficiary', 'value'])) {
                Response::badRequest('Invalid column; accepted: id, year, environment, beneficiary, value');
                return;
            }
            Response::json($this->preventionActivitiesRepo->selectDistinct($_GET['column']));
        }
    }
}

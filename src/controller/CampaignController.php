<?php
require_once __DIR__ . '/../repository/CampaignsProjectsRepository.php';
require_once __DIR__ . '/../repository/PreventionActivitiesRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../config/Constant.php';
require_once __DIR__ . '/../dtos/Id.php';
require_once __DIR__ . '/../dtos/SearchRequestCampaign.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestCampaignValidator.php';
require_once __DIR__ . '/../dtos/SearchRequestPrevention.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestPreventionValidator.php';
require_once __DIR__ . '/../dtos/patchDtos/PatchRequestCampaignProject.php';
require_once __DIR__ . '/../dtos/patchDtos/PatchRequestPreventionActivity.php';

class CampaignController
{
    private const TABLE_ACTIVITIES = [
        'campaigns-projects'     => 'project',
        'prevention-activities'  => 'prevention',
    ];

    private CampaignsProjectsRepository  $campaignProjectRepo;
    private PreventionActivitiesRepository $preventionActivitiesRepo;

    public function __construct()
    {
        $this->campaignProjectRepo  = new CampaignsProjectsRepository();
        $this->preventionActivitiesRepo = new PreventionActivitiesRepository();
    }

    public function executeFilter(string $table): void
    {
        $key = strtolower(trim($table));
        if (!isset(self::TABLE_ACTIVITIES[$key])) {
            Response::badRequest("wrong table");
            return;
        }
        $activity = self::TABLE_ACTIVITIES[$key];

        if ($activity === 'project') {
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


    public function patch(string $table, string $id): void
    {
        $key = strtolower(trim($table));
        if (!isset(self::TABLE_ACTIVITIES[$key])) {
            Response::badRequest("wrong table");
            return;
        }
        $activity = self::TABLE_ACTIVITIES[$key];
        $body = json_decode(file_get_contents('php://input'), true);
        if (!is_array($body)) {
            Response::badRequest("body has to be a json");
        }

        if ($activity === 'project') {
            $responseRequest = parsePatchRequestCampaignProject($id, $body);
            if (!$responseRequest['isSuccess']) {
                Response::badRequest($responseRequest['message']);
                return;
            }

            $request = $responseRequest['object'];

            if (!$request->hasChanges()) {
                Response::badRequest("no changes to make");
            }

            $isPatched = $this->campaignProjectRepo->patch($request);
            if ($isPatched) {
                Response::json(array('message' => 'patched'));
            } else {
                Response::json(array('message' => 'id does not exist'));
            }
        } else {
            $responseRequest = parsePatchRequestPreventionActivity($id, $body);
            if (!$responseRequest['isSuccess']) {
                Response::badRequest($responseRequest['message']);
                return;
            }
            $request = $responseRequest['object'];
            if (!$request->hasChanges()) {
                Response::badRequest("no changes to make");
            }
            $isPatched = $this->preventionActivitiesRepo->patch($request);
            if ($isPatched) {
                Response::json(array('message' => 'patched'));
            } else {
                Response::json(array('message' => 'id does not exist'));
            }
        }
    }

    public function selectOptions(string $table): void
    {
        $key = strtolower(trim($table));
        if (!isset(self::TABLE_ACTIVITIES[$key])) {
            Response::badRequest("wrong table");
            return;
        }
        $activity = self::TABLE_ACTIVITIES[$key];

        if (!isset($_GET['column']) || empty($_GET['column'])) {
            Response::badRequest("column must be specified");
            return;
        }

        if ($activity === 'project') {
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

    public function delete(string $table, ?string $id = null): void
    {
        $key = strtolower(trim($table));
        if (!isset(self::TABLE_ACTIVITIES[$key])) {
            Response::badRequest("wrong table");
            return;
        }
        $activity = self::TABLE_ACTIVITIES[$key];

        $responseRequest = parseId($id);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            return;
        }

        $request = $responseRequest['object'];
        if ($activity === 'project') {
            $isDeleted = $this->campaignProjectRepo->delete($request);
        } else {
            $isDeleted = $this->preventionActivitiesRepo->delete($request);
        }
        if ($isDeleted) {
            Response::json(array('message' => 'deleted'));
        } else {
            Response::json(array('message' => 'id does not exist'));
        }
    }
}

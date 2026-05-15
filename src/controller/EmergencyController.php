<?php
require_once __DIR__ . '/../repository/EmergenciesRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../config/Constant.php';
require_once __DIR__ . '/../dtos/SearchRequestEmergency.php';
require_once __DIR__ . '/../dtos/Id.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestEmergencyValidation.php';

class EmergencyController
{
    private EmergenciesRepository $emergencyRepo;

    public function __construct()
    {
        $this->emergencyRepo = new EmergenciesRepository();
    }

    public function executeFilter(): void
    {
        $responseRequest = parseSearchRequestEmergency($_GET);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            return;
        }

        $request = $responseRequest['object'];
        $validationResult = SearchRequestEmergencyValidator::validate($request);
        if (!$validationResult['isSuccess']) {
            Response::badRequest($validationResult['message']);
            return;
        }

        $data = $this->emergencyRepo->search($request);
        $result = array_map(fn($e) => $e->toArray(), $data);
        Response::sendData($result);
    }

    public function selectOptions(): void
    {
        if (!isset($_GET['column']) || empty($_GET['column'])) {
            Response::badRequest("column must be specified");
            return;
        }

        if (!Validator::validString($_GET['column'], ['id', 'year', 'criterion_value', 'drug', 'value'])) {
            Response::badRequest('Invalid column; accepted: id, year, criterion_value, drug, value');
            return;
        }
        Response::json($this->emergencyRepo->selectDistinct($_GET['column']));
    }

    public function delete(): void
    {
        $responseRequest = parseId($_GET);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            return;
        }

        $request = $responseRequest['object'];
        $isDeleted = $this->emergencyRepo->delete($request);
        if ($isDeleted) {
            Response::json(array('message' => 'deleted'));
        } else {
            Response::json(array('message' => 'id does not exist'));
        }
    }
}

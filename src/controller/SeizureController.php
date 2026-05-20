<?php
require_once __DIR__ . '/../repository/SeizuresRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../config/Constant.php';
require_once __DIR__ . '/../dtos/SearchRequestSeizure.php';
require_once __DIR__ . '/../dtos/Id.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestSeizureValidation.php';

class SeizuresController
{
    private SeizuresRepository $repo;

    public function __construct()
    {
        $this->repo = new SeizuresRepository();
    }

    public function executeFilter(): void
    {
        $responseRequest = parseSearchRequestSeizure($_GET);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            return;
        }

        $request = $responseRequest['object'];
        $validationResult = SearchRequestSeizureValidator::validate($request);
        if (!$validationResult['isSuccess']) {
            Response::badRequest($validationResult['message']);
            return;
        }

        $data = $this->repo->search($request);
        $result = array_map(fn($s) => $s->toArray(), $data);
        Response::sendData($result);
    }

    public function selectOptions(): void
    {
        if (!isset($_GET['column']) || empty($_GET['column'])) {
            Response::badRequest("column must be specified");
            return;
        }

        if (!Validator::validString($_GET['column'], ['id', 'year', 'drug_type', 'seizure_type', 'value'])) {
            Response::badRequest('Invalid column; accepted: id, year, drug_type, seizure_type, value');
            return;
        }
        Response::json($this->repo->selectDistinct($_GET['column']));
    }

    public function delete(string|null $id = null): void
    {
        $responseRequest = parseId($id);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            return;
        }

        $request = $responseRequest['object'];
        $isDeleted = $this->repo->delete($request);
        if ($isDeleted) {
            Response::json(array('message' => 'deleted'));
        } else {
            Response::json(array('message' => 'id does not exist'));
        }
    }
}

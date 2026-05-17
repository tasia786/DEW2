<?php
require_once __DIR__ . '/../repository/CrimesGeneralRepository.php';
require_once __DIR__ . '/../repository/CrimesSexRepository.php';
require_once __DIR__ . '/../repository/CrimesLawRepository.php';
require_once __DIR__ . '/../repository/CrimesSentencesRepository.php';
require_once __DIR__ . '/../repository/CriminalGroupsRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../dtos/Id.php';
require_once __DIR__ . '/../config/Constant.php';
require_once __DIR__ . '/../dtos/SearchRequestCrimeGeneral.php';
require_once __DIR__ . '/../dtos/SearchRequestCrimeSex.php';
require_once __DIR__ . '/../dtos/SearchRequestCrimeLaw.php';
require_once __DIR__ . '/../dtos/SearchRequestCrimeSentence.php';
require_once __DIR__ . '/../dtos/SearchRequestCriminalGroup.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestCrimeGeneralValidation.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestCrimeSexValidation.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestCrimeLawValidation.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestCrimeSentenceValidation.php';
require_once __DIR__ . '/../util/dtoValidators/SearchRequestCriminalGroupValidation.php';

class CrimesController
{
    private const TABLE_TYPES = [
        'crimes-general'    => 'general',
        'crimes-sex'        => 'sex',
        'crimes-law'        => 'law',
        'crimes-sentences'  => 'sentences',
        'criminal-groups'   => 'groups',
    ];

    private CrimesGeneralRepository   $generalRepo;
    private CrimesSexRepository       $sexRepo;
    private CrimesLawRepository       $lawRepo;
    private CrimesSentencesRepository $sentencesRepo;
    private CriminalGroupsRepository  $groupsRepo;

    public function __construct()
    {
        $this->generalRepo   = new CrimesGeneralRepository();
        $this->sexRepo       = new CrimesSexRepository();
        $this->lawRepo       = new CrimesLawRepository();
        $this->sentencesRepo = new CrimesSentencesRepository();
        $this->groupsRepo    = new CriminalGroupsRepository();
    }

    public function executeFilter(string $table): void
    {
        $key = strtolower(trim($table));
        if(!isset(self::TABLE_TYPES[$key])){
            Response::badRequest("wrong table");
            return;
        }
        $type=self::TABLE_TYPES[$key];

        $data = match ($type) {
            'general'   => $this->searchGeneral(),
            'sex'       => $this->searchSex(),
            'law'       => $this->searchLaw(),
            'sentences' => $this->searchSentences(),
            'groups'    => $this->searchGroups(),
        };

        $result = array_map(fn($item) => $item->toArray(), $data);
        Response::sendData($result);
    }

    public function selectOptions(?string $table = null): void
    {
        $key = strtolower(trim($table));
        if(!isset(self::TABLE_TYPES[$key])){
            Response::badRequest("wrong table");
            return;
        }
        $type=self::TABLE_TYPES[$key];

        if (!isset($_GET['column']) || empty($_GET['column'])) {
            Response::badRequest("column must be specified");
            return;
        }

        switch ($type) {
            case 'general':
                if (!Validator::validString($_GET['column'], ['id', 'year', 'category', 'value'])) {
                    Response::badRequest('Invalid column, accepted: id, year, category, value');
                    return;
                }
                break;
            case 'sex':
                if (!Validator::validString($_GET['column'], ['id', 'year', 'sex', 'age_category', 'value'])) {
                    Response::badRequest('Invalid column, accepted: id, year, sex, age_category, value');
                    return;
                }
                break;
            case 'law':
                if (!Validator::validString($_GET['column'], ['id', 'year', 'article', 'value'])) {
                    Response::badRequest('Invalid column, accepted: id, year, article, value');
                    return;
                }
                break;
            case 'sentences':
                if (!Validator::validString($_GET['column'], ['id', 'year', 'sentence_type', 'law', 'value'])) {
                    Response::badRequest('Invalid column, accepted: id, year, sentence_type, law, value');
                    return;
                }
                break;
            case 'groups':
                if (!Validator::validString($_GET['column'], ['id', 'year', 'field_name', 'value'])) {
                    Response::badRequest('Invalid column, accepted: id, year, field_name, value');
                    return;
                }
                break;
        }


        $data = match ($type) {
            'general'   => $this->generalRepo->selectDistinct($_GET['column']),
            'sex'       => $this->sexRepo->selectDistinct($_GET['column']),
            'law'       => $this->lawRepo->selectDistinct($_GET['column']),
            'sentences' => $this->sentencesRepo->selectDistinct($_GET['column']),
            'groups'    => $this->groupsRepo->selectDistinct($_GET['column']),
        };

        Response::json($data);
    }

    public function delete(string $table, ?string $id = null): void
    {
        $key = strtolower(trim($table));
        if(!isset(self::TABLE_TYPES[$key])){
            Response::badRequest("wrong table");
            return;
        }
        $type=self::TABLE_TYPES[$key];

        $responseRequest = parseId($id);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            return;
        }

        $request = $responseRequest['object'];
        $isDeleted = match ($type) {
            'general'   => $this->generalRepo->delete($request),
            'sex'       => $this->sexRepo->delete($request),
            'law'       => $this->lawRepo->delete($request),
            'sentences' => $this->sentencesRepo->delete($request),
            'groups'    => $this->groupsRepo->delete($request),
        };

        if ($isDeleted) {
            Response::json(array('message' => 'deleted'));
        } else {
            Response::json(array('message' => 'id does not exist'));
        }
    }

    private function searchGeneral(): array
    {
        $responseRequest = parseSearchRequestCrimeGeneral($_GET);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            exit;
        }

        $request = $responseRequest['object'];
        $validationResult = SearchRequestCrimeGeneralValidator::validate($request);
        if (!$validationResult['isSuccess']) {
            Response::badRequest($validationResult['message']);
            exit;
        }

        return $this->generalRepo->search($request);
    }

    private function searchSex(): array
    {
        $responseRequest = parseSearchRequestCrimeSex($_GET);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            exit;
        }

        $request = $responseRequest['object'];
        $validationResult = SearchRequestCrimeSexValidator::validate($request);
        if (!$validationResult['isSuccess']) {
            Response::badRequest($validationResult['message']);
            exit;
        }

        return $this->sexRepo->search($request);
    }

    private function searchSentences(): array
    {
        $responseRequest = parseSearchRequestCrimeSentence($_GET);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            exit;
        }

        $request = $responseRequest['object'];
        $validationResult = SearchRequestCrimeSentenceValidator::validate($request);
        if (!$validationResult['isSuccess']) {
            Response::badRequest($validationResult['message']);
            exit;
        }

        return $this->sentencesRepo->search($request);
    }

    private function searchLaw(): array
    {
        $responseRequest = parseSearchRequestCrimeLaw($_GET);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            exit;
        }

        $request = $responseRequest['object'];
        $validationResult = SearchRequestCrimeLawValidator::validate($request);
        if (!$validationResult['isSuccess']) {
            Response::badRequest($validationResult['message']);
            exit;
        }

        return $this->lawRepo->search($request);
    }

    private function searchGroups(): array
    {
        $responseRequest = parseSearchRequestCriminalGroup($_GET);
        if (!$responseRequest['isSuccess']) {
            Response::badRequest($responseRequest['message']);
            exit;
        }

        $request = $responseRequest['object'];
        $validationResult = SearchRequestCriminalGroupValidator::validate($request);
        if (!$validationResult['isSuccess']) {
            Response::badRequest($validationResult['message']);
            exit;
        }

        return $this->groupsRepo->search($request);
    }
}

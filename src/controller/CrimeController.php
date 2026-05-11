<?php
require_once __DIR__ . '/../repository/CrimesGeneralRepository.php';
require_once __DIR__ . '/../repository/CrimesSexRepository.php';
require_once __DIR__ . '/../repository/CrimesLawRepository.php';
require_once __DIR__ . '/../repository/CrimesSentencesRepository.php';
require_once __DIR__ . '/../repository/CriminalGroupsRepository.php';
require_once __DIR__ . '/../util/Response.php';
require_once __DIR__ . '/../util/Validator.php';
require_once __DIR__ . '/../config/Constant.php';

class CrimesController
{
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

    public function executeFilter(): void
    {
        if (!isset($_GET['type']) || empty($_GET['type']) || !in_array($_GET['type'], ['general', 'sex', 'law', 'sentences', 'groups'], true)) {
            Response::badRequest('type is required; accepted: general, sex, law, sentences, groups');
            return;
        }

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

        $data = match ($_GET['type']) {
            'general'   => $this->handleGeneral($values, $columnNames),
            'sex'       => $this->handleSex($values, $columnNames),
            'law'       => $this->handleLaw($values, $columnNames),
            'sentences' => $this->handleSentences($values, $columnNames),
            'groups'    => $this->groupsRepo->selectWithFilter($values, $columnNames),
        };

        $result = array_map(fn($item) => $item->toArray(), $data);
        Response::sendData($result);
    }

    public function selectOptions(): void
    {
        if (!isset($_GET['type']) || empty($_GET['type']) || !in_array($_GET['type'], ['general', 'sex', 'law', 'sentences', 'groups'], true)) {
            Response::badRequest('type is required; accepted: general, sex, law, sentences, groups');
            return;
        }

        if (!isset($_GET['column']) || empty($_GET['column'])) {
            Response::badRequest("column must be specified");
            return;
        }

        switch ($_GET['type']) {
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


        $data = match ($_GET['type']) {
            'general'   => $this->generalRepo->selectDistinct($_GET['column']),
            'sex'       => $this->sexRepo->selectDistinct($_GET['column']),
            'law'       => $this->lawRepo->selectDistinct($_GET['column']),
            'sentences' => $this->sentencesRepo->selectDistinct($_GET['column']),
            'groups'    => $this->groupsRepo->selectDistinct($_GET['column']),
        };

        Response::json($data);
    }

    private function handleGeneral(array $values, array $columnNames): array
    {
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            array_push($values, $_GET['category']);
            array_push($columnNames, 'category');
        }
        return $this->generalRepo->selectWithFilter($values, $columnNames);
    }

    private function handleSex(array $values, array $columnNames): array
    {
        if (isset($_GET['sex']) && !empty($_GET['sex'])) {
            if (!Validator::validString($_GET['sex'], ['Bărbați', 'Femei'])) {
                Response::badRequest('Invalid sex; accepted: Bărbați, Femei');
                exit;
            }
            array_push($values, $_GET['sex']);
            array_push($columnNames, 'sex');
        }
        if (isset($_GET['ageCategory']) && !empty($_GET['ageCategory'])) {
            if (!Validator::validString($_GET['ageCategory'], ['Majori', 'Minori'])) {
                Response::badRequest('Invalid age category; accepted: Majori, Minori');
                exit;
            }
            array_push($values, $_GET['ageCategory']);
            array_push($columnNames, 'age_category');
        }
        return $this->sexRepo->selectWithFilter($values, $columnNames);
    }

    private function handleSentences(array $values, array $columnNames): array
    {
        if (isset($_GET['law']) && !empty($_GET['law'])) {
            if (!Validator::validString($_GET['law'], ['Legea 143/2000', 'Legea 194/2011'])) {
                Response::badRequest('Invalid law; accepted: Legea 143/2000,Legea 194/2011');
                exit;
            }
            array_push($values, $_GET['law']);
            array_push($columnNames, 'law');
        }

        if (isset($_GET['sentenceType']) && !empty($_GET['sentenceType'])) {
            array_push($values, $_GET['sentenceType']);
            array_push($columnNames, 'sentence_type');
        }
        return $this->sentencesRepo->selectWithFilter($values, $columnNames);
    }

    private function handleLaw(array $values, array $columnNames): array
    {
        if (isset($_GET['article']) && !empty($_GET['article'])) {
            array_push($values, $_GET['article']);
            array_push($columnNames, 'article');
        }
        return $this->lawRepo->selectWithFilter($values, $columnNames);
    }
}

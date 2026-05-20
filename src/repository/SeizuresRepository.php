<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Seizure.php';
require_once __DIR__ . '/../dtos/SearchRequestSeizure.php';
require_once __DIR__ . '/../dtos/Id.php';
require_once __DIR__ . '/../dtos/patchDtos/PatchRequestSeizure.php';

class SeizuresRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;
    private array $acceptedColumns = ['id', 'year', 'drug_type', 'seizure_type', 'value'];

    public function __construct()
    {
        $this->insertStmt = Database::getConnection()->prepare(
            "INSERT INTO seizures (year, drug_type, seizure_type, value)
             VALUES (?, ?, ?, ?)"
        );
    }

    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'seizures', $params);

        $db = Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return Seizure::fromArrayToObjSet($result);
    }

    public function insert(object $model): bool
    {
        return $this->insertStmt->execute([
            $model->getYear(),
            $model->getDrugType(),
            $model->getSeizureType(),
            $model->getValue()
        ]);
    }

    public function selectDistinct(string $columnName): ?array
    {
        $columnName = strtolower($columnName);
        if (!in_array($columnName, $this->acceptedColumns)) {
            return null;
        }

        $stmt = Database::getConnection()->prepare(
            "SELECT DISTINCT " . $columnName . " FROM seizures ORDER BY " . $columnName
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $columnName);
    }

    public function search(SearchRequestSeizure $searchRequest): array
    {
        $values = [];
        $dbColumnNames = [];

        if ($searchRequest->getYears() !== null) {
            $values[] = $searchRequest->getYears();
            $dbColumnNames[] = 'year';
        }

        if ($searchRequest->getDrugType() !== null) {
            $values[] = $searchRequest->getDrugType();
            $dbColumnNames[] = 'drug_type';
        }

        if ($searchRequest->getSeizureType() !== null) {
            $values[] = $searchRequest->getSeizureType();
            $dbColumnNames[] = 'seizure_type';
        }

        $params = [];
        $query = appendInQuery2($values, $dbColumnNames, 'seizures', $params, $searchRequest->getNmbPage());

        $db = Database::getConnection();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return Seizure::fromArrayToObjSet($result);
    }

    public function delete(Id $id) : bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM seizures WHERE id = ?");
        $stmt->execute([$id->getId()]);

        return $stmt->rowCount() > 0;
    }

    public function patch(PatchRequestSeizure $patchRequest): bool
    {
        $fields = [];
        $params = [];

        if ($patchRequest->getYear() !== null) {
            $fields[] = 'year = ?';
            $params[] = $patchRequest->getYear();
        }
        if ($patchRequest->getDrugType() !== null) {
            $fields[] = 'drug_type = ?';
            $params[] = $patchRequest->getDrugType();
        }
        if ($patchRequest->getSeizureType() !== null) {
            $fields[] = 'seizure_type = ?';
            $params[] = $patchRequest->getSeizureType();
        }
        if ($patchRequest->getValue() !== null) {
            $fields[] = 'value = ?';
            $params[] = $patchRequest->getValue();
        }

        if (empty($fields)) return false;

        $params[] = $patchRequest->getId();
        $sql = 'UPDATE seizures SET ' . implode(', ', $fields) . ' WHERE id = ?';

        $db = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }
}

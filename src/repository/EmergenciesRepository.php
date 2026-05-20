<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Emergency.php';
require_once __DIR__ . '/../dtos/SearchRequestEmergency.php';
require_once __DIR__ . '/../dtos/patchDtos/PatchRequestEmergency.php';

class EmergenciesRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;
    private array $acceptedColumns = ['id', 'year', 'criterion_value', 'drug', 'value'];

    public function __construct()
    {
        $this->insertStmt = Database::getConnection()->prepare(
            "INSERT INTO emergencies (year, criterion_value, drug, value)
             VALUES (?, ?, ?, ?)"
        );
    }

    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'emergencies', $params);

        $db = Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return Emergency::fromArrayToObjsSet($result);
    }

    public function insert(object $model): bool
    {
        return $this->insertStmt->execute([
            $model->getYear(),
            $model->getCriterionValue(),
            $model->getDrug(),
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
            "SELECT DISTINCT " . $columnName . " FROM emergencies ORDER BY " . $columnName
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $columnName);
    }

    public function search(SearchRequestEmergency $searchRequest): array
    {
        $values = [];
        $dbColumnNames = [];

        if ($searchRequest->getYears() !== null) {
            $values[] = $searchRequest->getYears();
            $dbColumnNames[] = 'year';
        }

        if ($searchRequest->getCriterionValue() !== null) {
            $values[] = $searchRequest->getCriterionValue();
            $dbColumnNames[] = 'criterion_value';
        }

        if ($searchRequest->getDrug() !== null) {
            $values[] = $searchRequest->getDrug();
            $dbColumnNames[] = 'drug';
        }

        $params = [];
        $query = appendInQuery2($values, $dbColumnNames, 'emergencies', $params, $searchRequest->getNmbPage());

        $db = Database::getConnection();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return Emergency::fromArrayToObjsSet($result);
    }

    public function delete(Id $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM emergencies WHERE id = ?");
        $stmt->execute([$id->getId()]);

        return $stmt->rowCount() > 0;
    }

    public function patch(PatchRequestEmergency $patchRequest): bool
    {
        $fields = [];
        $params = [];

        if ($patchRequest->getYear() !== null) {
            $fields[] = 'year = ?';
            $params[] = $patchRequest->getYear();
        }
        if ($patchRequest->getCriterionValue() !== null) {
            $fields[] = 'criterion_value = ?';
            $params[] = $patchRequest->getCriterionValue();
        }
        if ($patchRequest->getDrug() !== null) {
            $fields[] = 'drug = ?';
            $params[] = $patchRequest->getDrug();
        }
        if ($patchRequest->getValue() !== null) {
            $fields[] = 'value = ?';
            $params[] = $patchRequest->getValue();
        }

        if (empty($fields)) return false;

        $params[] = $patchRequest->getId();
        $sql = 'UPDATE emergencies SET ' . implode(', ', $fields) . ' WHERE id = ?';

        $db = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }
}

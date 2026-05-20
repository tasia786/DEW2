<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/CriminalGroup.php';
require_once __DIR__ . '/../dtos/SearchRequestCriminalGroup.php';
require_once __DIR__ . '/../dtos/patchDtos/PatchRequestCriminalGroup.php';

class CriminalGroupsRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;
    private array $acceptedColumns = ['id', 'year', 'field_name', 'value'];

    public function __construct()
    {
        $this->insertStmt = Database::getConnection()->prepare(
            "INSERT INTO criminal_groups (year, field_name, value)
             VALUES (?, ?, ?)"
        );
    }

    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'criminal_groups', $params);

        $db = Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CriminalGroup::fromArrayToObjsSet($result);
    }

    public function insert(object $model): bool
    {
        return $this->insertStmt->execute([
            $model->getYear(),
            $model->getFieldName(),
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
            "SELECT DISTINCT " . $columnName . " FROM criminal_groups ORDER BY " . $columnName
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $columnName);
    }

    public function search(SearchRequestCriminalGroup $searchRequest): array
    {
        $values = [];
        $dbColumnNames = [];

        if ($searchRequest->getYears() !== null) {
            $values[] = $searchRequest->getYears();
            $dbColumnNames[] = 'year';
        }

        if ($searchRequest->getFieldName() !== null) {
            $values[] = $searchRequest->getFieldName();
            $dbColumnNames[] = 'field_name';
        }

        $params = [];
        $query = appendInQuery2($values, $dbColumnNames, 'criminal_groups', $params, $searchRequest->getNmbPage());

        $db = Database::getConnection();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CriminalGroup::fromArrayToObjsSet($result);
    }

    public function delete(Id $id): bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM criminal_groups WHERE id = ?");
        $stmt->execute([$id->getId()]);

        return $stmt->rowCount() > 0;
    }

    public function patch(PatchRequestCriminalGroup $patchRequest): bool
    {
        $fields = [];
        $params = [];

        if ($patchRequest->getYear() !== null) {
            $fields[] = 'year = ?';
            $params[] = $patchRequest->getYear();
        }
        if ($patchRequest->getFieldName() !== null) {
            $fields[] = 'field_name = ?';
            $params[] = $patchRequest->getFieldName();
        }
        if ($patchRequest->getValue() !== null) {
            $fields[] = 'value = ?';
            $params[] = $patchRequest->getValue();
        }

        if (empty($fields)) return false;

        $params[] = $patchRequest->getId();
        $sql = 'UPDATE criminal_groups SET ' . implode(', ', $fields) . ' WHERE id = ?';

        $db = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }
}

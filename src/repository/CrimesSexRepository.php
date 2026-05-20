<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/CrimeSex.php';
require_once __DIR__ . '/../dtos/SearchRequestCrimeSex.php';
require_once __DIR__ . '/../dtos/patchDtos/PatchRequestCrimeSex.php';

class CrimesSexRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;
    private array $acceptedColumns = ['id', 'year', 'sex', 'age_category', 'value'];

    public function __construct()
    {
        $this->insertStmt = Database::getConnection()->prepare(
            "INSERT INTO crimes_sex (year, sex, age_category, value)
             VALUES (?, ?, ?, ?)"
        );
    }

    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'crimes_sex', $params);

        $db = Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CrimeSex::fromArrayToObjsSet($result);
    }

    public function insert(object $model): bool
    {
        return $this->insertStmt->execute([
            $model->getYear(),
            $model->getSex(),
            $model->getAgeCategory(),
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
            "SELECT DISTINCT " . $columnName . " FROM crimes_sex ORDER BY " . $columnName
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $columnName);
    }

    public function search(SearchRequestCrimeSex $searchRequest): array
    {
        $values = [];
        $dbColumnNames = [];

        if ($searchRequest->getYears() !== null) {
            $values[] = $searchRequest->getYears();
            $dbColumnNames[] = 'year';
        }

        if ($searchRequest->getSex() !== null) {
            $values[] = $searchRequest->getSex();
            $dbColumnNames[] = 'sex';
        }

        if ($searchRequest->getAgeCategory() !== null) {
            $values[] = $searchRequest->getAgeCategory();
            $dbColumnNames[] = 'age_category';
        }

        $params = [];
        $query = appendInQuery2($values, $dbColumnNames, 'crimes_sex', $params, $searchRequest->getNmbPage());

        $db = Database::getConnection();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CrimeSex::fromArrayToObjsSet($result);
    }

    public function delete(Id $id): bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM crimes_sex WHERE id = ?");
        $stmt->execute([$id->getId()]);

        return $stmt->rowCount() > 0;
    }

    public function patch(PatchRequestCrimeSex $patchRequest): bool
    {
        $fields = [];
        $params = [];

        if ($patchRequest->getYear() !== null) {
            $fields[] = 'year = ?';
            $params[] = $patchRequest->getYear();
        }
        if ($patchRequest->getSex() !== null) {
            $fields[] = 'sex = ?';
            $params[] = $patchRequest->getSex();
        }
        if ($patchRequest->getAgeCategory() !== null) {
            $fields[] = 'age_category = ?';
            $params[] = $patchRequest->getAgeCategory();
        }
        if ($patchRequest->getValue() !== null) {
            $fields[] = 'value = ?';
            $params[] = $patchRequest->getValue();
        }

        if (empty($fields)) return false;

        $params[] = $patchRequest->getId();
        $sql = 'UPDATE crimes_sex SET ' . implode(', ', $fields) . ' WHERE id = ?';

        $db = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }
}

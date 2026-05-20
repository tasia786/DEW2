<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/CrimeLaw.php';
require_once __DIR__ . '/../dtos/SearchRequestCrimeLaw.php';
require_once __DIR__ . '/../dtos/patchDtos/PatchRequestCrimeLaw.php';

class CrimesLawRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;
    private array $acceptedColumns = ['id', 'year', 'article', 'value'];


    public function __construct()
    {
        $this->insertStmt = Database::getConnection()->prepare(
            "INSERT INTO crimes_law (year, article, value)
             VALUES (?, ?, ?)"
        );
    }

    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'crimes_law', $params);

        $db = Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CrimeLaw::fromArrayToObjsSet($result);
    }

    public function insert(object $model): bool
    {
        return $this->insertStmt->execute([
            $model->getYear(),
            $model->getArticle(),
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
            "SELECT DISTINCT " . $columnName . " FROM crimes_law ORDER BY " . $columnName
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $columnName);
    }

    public function search(SearchRequestCrimeLaw $searchRequest): array
    {
        $values = [];
        $dbColumnNames = [];

        if ($searchRequest->getYears() !== null) {
            $values[] = $searchRequest->getYears();
            $dbColumnNames[] = 'year';
        }

        if ($searchRequest->getArticle() !== null) {
            $values[] = $searchRequest->getArticle();
            $dbColumnNames[] = 'article';
        }

        $params = [];
        $query = appendInQuery2($values, $dbColumnNames, 'crimes_law', $params, $searchRequest->getNmbPage());

        $db = Database::getConnection();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CrimeLaw::fromArrayToObjsSet($result);
    }

    public function delete(Id $id): bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM crimes_law WHERE id = ?");
        $stmt->execute([$id->getId()]);

        return $stmt->rowCount() > 0;
    }

    public function patch(PatchRequestCrimeLaw $patchRequest): bool
    {
        $fields = [];
        $params = [];

        if ($patchRequest->getYear() !== null) {
            $fields[] = 'year = ?';
            $params[] = $patchRequest->getYear();
        }
        if ($patchRequest->getArticle() !== null) {
            $fields[] = 'article = ?';
            $params[] = $patchRequest->getArticle();
        }
        if ($patchRequest->getValue() !== null) {
            $fields[] = 'value = ?';
            $params[] = $patchRequest->getValue();
        }

        if (empty($fields)) return false;

        $params[] = $patchRequest->getId();
        $sql = 'UPDATE crimes_law SET ' . implode(', ', $fields) . ' WHERE id = ?';

        $db = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }
}

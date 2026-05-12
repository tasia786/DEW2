<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/PreventionActivity.php';

class PreventionActivitiesRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;
    private array $acceptedColumns = ['id', 'year', 'environment', 'beneficiary', 'value'];

    public function __construct()
    {
        $this->insertStmt = Database::getConnection()->prepare(
            "INSERT INTO prevention_activities (year, environment, beneficiary, value)
             VALUES (?, ?, ?, ?)"
        );
    }

    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'prevention_activities', $params);

        $db = Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return PreventionActivity::fromArrayToObjsSet($result);
    }

    public function insert(object $model): bool
    {
        return $this->insertStmt->execute([
            $model->getYear(),
            $model->getEnvironment(),
            $model->getBeneficiary(),
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
            "SELECT DISTINCT " . $columnName . " FROM prevention_activities ORDER BY " . $columnName
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $columnName);
    }
}
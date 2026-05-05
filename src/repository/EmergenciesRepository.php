<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Emergency.php';

class EmergenciesRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;

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

        $db=Database::getConnection();

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
}

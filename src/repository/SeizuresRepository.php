<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Seizure.php';

class SeizuresRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;

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

        $db=Database::getConnection();

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
}


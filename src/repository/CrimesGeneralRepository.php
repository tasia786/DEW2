<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/CrimeGeneral.php';

class CrimesGeneralRepository implements RepositoryInterface
{
    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'crimes_general', $params);

        $db=Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CrimeGeneral::fromArrayToObjsSet($result);
    }
}

print_r(new CrimesGeneralRepository()->selectWithFilter(['2021,2022', 'Persoane cercetate,Persoane trimise în judecată'], ['year', 'category']));


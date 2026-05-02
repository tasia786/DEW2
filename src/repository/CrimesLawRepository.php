<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/CrimeLaw.php';

class CrimesLawRepository implements RepositoryInterface
{
    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'crimes_law', $params);

        $db=Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CrimeLaw::fromArrayToObjsSet($result);
    }
}

print_r(new CrimesLawRepository()->selectWithFilter(['2021,2022', 'Art.7 din Legea nr. 143/2000,Art.2 din Legea nr. 143/2000'], ['year', 'article']));


<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/PreventionActivity.php';

class PreventionActivitiesRepository implements RepositoryInterface
{
    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'prevention_activities', $params);

        $db=Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return PreventionActivity::fromArrayToObjsSet($result);
    }
}

print_r(new PreventionActivitiesRepository()->selectWithFilter(['2021,2022', 'copii,parinti'], ['year', 'beneficiary']));
//print_r(new PreventionActivitiesRepository()->selectWithFilter([], []));


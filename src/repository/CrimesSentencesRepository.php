<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';

class CrimesSentencesRepository implements RepositoryInterface
{
    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'crimes_sentences', $params);

        $db=Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}

print_r(new CrimesSentencesRepository()->selectWithFilter(['2021,2022', 'Suspendarea pedepsei', 'Legea nr. 194/2011'], ['year', 'sentence_type', 'law']));


<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/CrimeSentence.php';

class CrimesSentencesRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;
    private array $acceptedColumns = ['id', 'year', 'sentence_type', 'law', 'value'];

    public function __construct()
    {
        $this->insertStmt = Database::getConnection()->prepare(
            "INSERT INTO crimes_sentences (year, sentence_type, law, value)
             VALUES (?, ?, ?, ?)"
        );
    }

    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'crimes_sentences', $params);

        $db = Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CrimeSentence::fromArrayToObjsSet($result);
    }

    public function insert(object $model): bool
    {
        return $this->insertStmt->execute([
            $model->getYear(),
            $model->getSentenceType(),
            $model->getLaw(),
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
            "SELECT DISTINCT " . $columnName . " FROM crimes_sentences ORDER BY " . $columnName
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $columnName);
    }
}
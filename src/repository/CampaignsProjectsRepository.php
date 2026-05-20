<?php
require_once __DIR__ . '/RepositoryInterface.php';
require_once __DIR__ . '/../util/appendInQuery.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/CampaignProject.php';
require_once __DIR__ . '/../dtos/SearchRequestCampaign.php';
require_once __DIR__ . '/../dtos/patchDtos/PatchRequestCampaignProject.php';

class CampaignsProjectsRepository implements RepositoryInterface
{
    private PDOStatement $insertStmt;
    private array $acceptedColumns = ['id', 'year', 'type', 'name', 'value'];

    public function __construct()
    {
        $this->insertStmt = Database::getConnection()->prepare(
            "INSERT INTO campaigns_projects (year, type, name, beneficiaries_count)
             VALUES (?, ?, ?, ?)"
        );
    }

    public function selectWithFilter(array $values, array $dbColumnNames)
    {
        $params = [];
        $query = appendInQuery($values, $dbColumnNames, 'campaigns_projects', $params);

        $db = Database::getConnection();

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return CampaignProject::fromArrayToObjsSet($result);
    }

    public function insert(object $model): bool
    {
        return $this->insertStmt->execute([
            $model->getYear(),
            $model->getType(),
            $model->getName(),
            $model->getBeneficiariesCount()
        ]);
    }
    public function selectDistinct(string $columnName): ?array
    {
        $columnName = strtolower($columnName);
        if (!in_array($columnName, $this->acceptedColumns)) {
            return null;
        }

        $stmt = Database::getConnection()->prepare(
            "SELECT DISTINCT " . $columnName . " FROM campaigns_projects ORDER BY " . $columnName
        );
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $columnName);
    }

    public function search(SearchRequestCampaign $searchRequest): array
    {
        $values = [];
        $dbColumnNames = [];

        if ($searchRequest->getYears() !== null) {
            $values[] = $searchRequest->getYears();
            $dbColumnNames[] = 'year';
        }

        if ($searchRequest->getType() !== null) {
            $values[] = $searchRequest->getType();
            $dbColumnNames[] = 'type';
        }

        $params = [];
        $query  = appendInQuery2($values, $dbColumnNames, 'campaigns_projects', $params, $searchRequest->getNmbPage());

        $db   = Database::getConnection();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return CampaignProject::fromArrayToObjsSet($result);
    }

    public function delete(Id $id): bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM campaigns_projects WHERE id = ?");
        $stmt->execute([$id->getId()]);

        return $stmt->rowCount() > 0;
    }

    public function patch(PatchRequestCampaignProject $patchRequest): bool
    {
        $fields = [];
        $params = [];

        if ($patchRequest->getYear() !== null) {
            $fields[] = 'year = ?';
            $params[] = $patchRequest->getYear();
        }
        if ($patchRequest->getType() !== null) {
            $fields[] = 'type = ?';
            $params[] = $patchRequest->getType();
        }
        if ($patchRequest->getName() !== null) {
            $fields[] = 'name = ?';
            $params[] = $patchRequest->getName();
        }
        if ($patchRequest->getBeneficiariesCount() !== null) {
            $fields[] = 'beneficiaries_count = ?';
            $params[] = $patchRequest->getBeneficiariesCount();
        }

        if (empty($fields)) return false;

        $params[] = $patchRequest->getId();
        $sql = 'UPDATE campaigns_projects SET ' . implode(', ', $fields) . ' WHERE id = ?';

        $db = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }
}

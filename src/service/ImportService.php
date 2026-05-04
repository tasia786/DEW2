<?php
require_once __DIR__ . '/../util/parsers/crimes.php';
require_once __DIR__ . '/../util/parsers/emergencies.php';
require_once __DIR__ . '/../util/parsers/prevention.php';
require_once __DIR__ . '/../util/parsers/seizures.php';
require_once __DIR__ . '/../util/validateInput.php';
require_once __DIR__ . '/../config/Database.php';

class ImportService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function import(string $table, int $year, string $filePath): void
    {

        switch ($table) {
            case 'crimes':
                validateCsv([2, 2, 3, 2, 3], ["CERCETATE", "juridic", "sexe", "grupărilor", "pedepselor"], $filePath);
                break;
            case 'seizures':
                validateCsv([6], ["grame"], $filePath);
                break;
            case 'emergencies':
                validateCsv([5], ["canabis"], $filePath);
                break;
            case 'prevention':
                validateCsv([2, 2, 8], ["proiecte", "campanii", "nr."], $filePath);
                break;
        }

        $this->db->beginTransaction();
        try {
            match ($table) {
                'crimes'      => parseCrimes($filePath, $year),
                'seizures'    => parseSeizures($filePath, $year),
                'emergencies' => parseEmergencies($filePath, $year),
                'prevention'   => parsePrevention($filePath, $year)
            };
        $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

<?php

function parseSeizures(string $fileName, int $year, PDO $db): void
{
    if (!file_exists($fileName)) {
        throw new RuntimeException("-File does not exist: {$fileName}");
    }

    $open = fopen($fileName, 'r');
    if ($open === false) {
        throw new RuntimeException("-Cannot open: {$fileName}");
    }

    $stmt = $db->prepare(
        "INSERT INTO seizures (year, drug_type, seizure_type, value)
         VALUES (?, ?, ?, ?)"
    );

    $toFloat = fn($v) => ($v !== '' && $v !== null) ? (float)$v : null;
    $seizureTypes = ['Grame', 'Comprimate', 'Doze/Buc', 'Mililitri', 'Nr. Capturi'];

    $db->beginTransaction();
    try {
        $data = fgetcsv($open, 1000, ',', '"', '\\');
        while (($data = fgetcsv($open, 1000, ',', '"', '\\')) !== false) {
            if (count($data) < 6 || empty(trim($data[0]))) continue;

            for ($index = 0; $index < sizeof($seizureTypes); $index++) {
                $value = $toFloat($data[$index + 1]);
                if ($value === null) {
                    continue;
                }

                $stmt->execute([
                    $year,
                    trim($data[0]),
                    $seizureTypes[$index],
                    $value
                ]);
            }
        }
        $db->commit();
    } catch (PDOException $e) {
        $db->rollBack();
        throw $e;
    } finally {
        fclose($open);
    }
}

<?php

function parseEmergencies(string $fileName, int $year, PDO $db): void
{
    if (!file_exists($fileName)) {
        throw new RuntimeException("Fisier negasit: {$fileName}");
    }

    $open = fopen($fileName, 'r');
    if ($open === false) {
        throw new RuntimeException("Nu pot deschide: {$fileName}");
    }

    $stmt = $db->prepare(
        "INSERT INTO emergencies (year, criterion_value, drug, value)
         VALUES (?, ?, ?, ?)"
    );

    $toInt   = fn($v) => ($v !== '' && $v !== null) ? (int)$v   : null;

    $drogType = ['Canabis', 'Stimulanti', 'Opiacee', 'NSP'];

    $db->beginTransaction();
    try {
        while (($data = fgetcsv($open, 1000, ',', '"', '\\')) !== false) {
            if (count($data) < 5 || empty(trim($data[0]))) continue;

            for ($index = 0; $index < sizeof($drogType); $index++) {
                $value = $toInt($data[$index + 1]);
                if ($value === null) {
                    continue;
                }

                $stmt->execute([
                    $year,
                    trim($data[0]),
                    $drogType[$index],
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

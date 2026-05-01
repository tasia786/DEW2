<?php

function parsePrevention(string $fileName, int $year, PDO $db): void
{
    if (!file_exists($fileName)) {
        throw new RuntimeException("-File does not exist: {$fileName}");
    }

    $open = fopen($fileName, 'r');
    if ($open === false) {
        throw new RuntimeException("-Cannot open: {$fileName}");
    }

    $stmt1 = $db->prepare(
        "INSERT INTO campaigns_projects (year, type, name, beneficiaries_count)
         VALUES (?, ?, ?, ?)"
    );

    $stmt2 = $db->prepare(
        "INSERT INTO prevention_activities (year, environment, beneficiary, value)
         VALUES (?, ?, ?, ?)"
    );

    $beneficiaries = ['activitati_total', 'copii', 'parinti', 'cadre_didactice', 'studenti', 'persoane', 'elevi'];
    $toInt   = fn($v) => ($v !== '' && $v !== null) ? (int)$v   : null;
    $type = "unknown";

    $db->beginTransaction();
    try {
        while (($data = fgetcsv($open, 1000, ',', '"', '\\')) !== false) {
            if ((count($data) != 2 && count($data) != 8) || empty(trim($data[0]))) continue;
            if (count($data) == 2) {
                if (strpos(strtolower($data[0]), "proiecte") !== FALSE && strpos(strtolower($data[1]), "nr.") !== FALSE) {
                    $type = "proiect";
                    continue;
                }

                if (strpos(strtolower($data[0]), "campanii") !== FALSE && strpos(strtolower($data[1]), "nr.") !== FALSE) {
                    $type = "campanie";
                    continue;
                }

                $stmt1->execute([
                    $year,
                    $type,
                    trim($data[0]),
                    $toInt($data[1])
                ]);
            } else {
                if (strpos(strtolower($data[1]), "nr.") !== FALSE) {
                    continue;
                }

                for ($index = 0; $index < sizeof($beneficiaries); $index++) {
                    $value = $toInt($data[$index + 1]);
                    if ($value === null) {
                        continue;
                    }

                    $stmt2->execute([
                        $year,
                        trim($data[0]),
                        $beneficiaries[$index],
                        $value
                    ]);
                }
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

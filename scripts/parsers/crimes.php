<?php

function parseCrimes(string $fileName, int $year, PDO $db): void
{
    if (!file_exists($fileName)) {
        throw new RuntimeException("-File not found: {$fileName}");
    }

    $open = fopen($fileName, 'r');
    if ($open === false) {
        throw new RuntimeException("-Cannot open file: {$fileName}");
    }

    $stmt1 = $db->prepare(
        "INSERT INTO crimes_general (year, category, value)
         VALUES (?, ?, ?)"
    );

    $stmt2 = $db->prepare(
        "INSERT INTO crimes_law (year, article, value)
         VALUES (?, ?, ?)"
    );

    $stmt3 = $db->prepare(
        "INSERT INTO crimes_sex (year, sex, age_category, value)
         VALUES (?, ?, ?, ?)"
    );

    $stmt4 = $db->prepare(
        "INSERT INTO criminal_groups (year, field_name, value)
         VALUES (?, ?, ?)"
    );

    $stmt5 = $db->prepare(
        "INSERT INTO crimes_sentences (year, sentence_type, law, value)
         VALUES (?, ?, ?, ?)"
    );


    $toInt   = fn($v) => ($v !== '' && $v !== null) ? (int)$v   : null;

    $db->beginTransaction();
    try {
        $data = fgetcsv($open, 1000, ',', '"', '\\');
        while (($data = fgetcsv($open, 1000, ',', '"', '\\')) !== false) {
            if (empty(trim($data[0] ?? ''))) continue;

            if (strpos(strtolower($data[0]), "juridic") !== FALSE) {
                break;
            }

            $stmt1->execute([
                $year,
                trim($data[0]),
                $toInt($data[1])
            ]);
        }
        while (($data = fgetcsv($open, 1000, ',', '"', '\\')) !== false) {
            if (empty(trim($data[0] ?? ''))) continue;

            if (strpos(strtolower($data[0]), "sexe") !== FALSE) {
                break;
            }

            $stmt2->execute([
                $year,
                trim($data[0]),
                $toInt($data[1])
            ]);
        }

        while (($data = fgetcsv($open, 1000, ',', '"', '\\')) !== false) {
            if (empty(trim($data[0] ?? ''))) continue;

            $toLowerData = mb_strtolower($data[0], 'UTF-8');
            if (mb_strpos($toLowerData, "grupărilor") !== FALSE || mb_strpos($toLowerData, "gruparilor") !== FALSE) {
                break;
            }

            $stmt3->execute([
                $year,
                trim($data[0]),
                "Majori",
                $toInt($data[1])
            ]);
            $stmt3->execute([
                $year,
                trim($data[0]),
                "Minori",
                $toInt($data[2])
            ]);
        }

        while (($data = fgetcsv($open, 1000, ',', '"', '\\')) !== false) {
            if (empty(trim($data[0] ?? ''))) continue;

            if (strpos(strtolower($data[0]), "pedepselor") !== FALSE) {
                break;
            }

            $stmt4->execute([
                $year,
                trim($data[0]),
                trim($data[1])
            ]);
        }


        while (($data = fgetcsv($open, 1000, ',', '"', '\\')) !== false) {
            if (empty(trim($data[0] ?? ''))) continue;


            $stmt5->execute([
                $year,
                trim($data[0]),
                "Legea nr. 143/2000",
                $toInt($data[1])
            ]);
            $stmt5->execute([
                $year,
                trim($data[0]),
                "Legea nr. 194/2011",
                $toInt($data[2])
            ]);
        }
        $db->commit();
    } catch (PDOException $e) {
        $db->rollBack();
        throw $e;
    } finally {
        fclose($open);
    }
}

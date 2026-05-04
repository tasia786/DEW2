<?php
function validateCsv(array $nmbColTotal, array $keyWords, string $filePath)
{
    //daca col si key au val dif
    if (($file = fopen($filePath, 'r')) == null) {
        throw new Error("file could not pe opened");
    }
    $sizeList = sizeof($keyWords);
    $indexKeyWord = 0;
    $nmbCol = $nmbColTotal[0];

    while (($row = fgetcsv($file, escape: "\\")) !== FALSE) {
        if (sizeof($row) == 1 && $row[0] == null) {
            continue;
        }
        //var_dump($row);
        if ($indexKeyWord < $sizeList) {
            foreach ($row as $element) {
                if ($element && mb_stristr($element, $keyWords[$indexKeyWord], encoding: 'UTF-8')) {
                    $nmbCol = $nmbColTotal[$indexKeyWord];
                    $indexKeyWord++;
                    break;
                }
            }
        }
        if (sizeof($row) != $nmbCol) {
            //echo $nmbCol;
            throw new InvalidArgumentException("Invalid CSV format");
        }
    }

    if ($indexKeyWord < $sizeList) {
        throw new InvalidArgumentException("Invalid CSV format");
    }
}

/*
var_dump(validateCsv([2, 2, 8], ["proiecte", "campanii", "nr."], "../../seeds/data_csv/proiecte-si-campanii-2022.csv"));
echo '<br>';
var_dump(validateCsv([2, 2, 8], ["proiecte", "campanii", "nr."], "../../seeds/data_csv/prevenire-2021.csv"));
echo '<br>';
echo '<br>';

var_dump(validateCsv([2, 2, 3, 2, 3], ["CERCETATE", "juridic", "sexe", "grupărilor", "pedepselor"], "../../seeds/data_csv/infractionalitate-2022.csv"));
echo '<br>';
var_dump(validateCsv([2, 2, 3, 2, 3], ["CERCETATE", "juridic", "sexe", "grupărilor", "pedepselor"], "../../seeds/data_csv/infractionalitate-2021.csv"));

echo '<br>';
echo '<br>';

var_dump(validateCsv([6], ["grame"], "../../seeds/data_csv/capturi-droguri-2022.csv"));
echo '<br>';
var_dump(validateCsv([6], ["grame"], "../../seeds/data_csv/capturi-droguri-2021.csv"));

echo '<br>';
echo '<br>';
var_dump(validateCsv([5], ["canabis"], "../../seeds/data_csv/urgente_medicale_2021.csv"));
echo '<br>';
var_dump(validateCsv([5], ["canabis"], "../../seeds/data_csv/urgente_medicale_2022.csv"));
*/

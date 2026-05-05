<?php
require_once 'db_schema.php';
require_once __DIR__ . '/parsers/seizures.php'; 
require_once __DIR__ . '/parsers/emergencies.php'; 
require_once __DIR__ . '/parsers/prevention.php'; 
require_once __DIR__ . '/parsers/crimes.php'; 


$db = new PDO('sqlite:' . __DIR__ . '/../data/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

initSchema($db);

parseSeizures(__DIR__ . "/../data_csv/capturi-droguri-2021.csv", 2021, $db);
parseSeizures(__DIR__ . "/../data_csv/capturi-droguri-2022.csv", 2022, $db);

parseEmergencies(__DIR__ . "/../data_csv/urgente_medicale_2021.csv", 2021, $db);
parseEmergencies(__DIR__ . "/../data_csv/urgente_medicale_2022.csv", 2022, $db);

parsePrevention(__DIR__ . "/../data_csv/proiecte-si-campanii-2022.csv", 2022, $db);
parsePrevention(__DIR__ . "/../data_csv/prevenire-2021.csv", 2021, $db);

parseCrimes(__DIR__ . "/../data_csv/infractionalitate-2022.csv", 2022, $db);
parseCrimes(__DIR__ . "/../data_csv/infractionalitate-2021.csv", 2021, $db);

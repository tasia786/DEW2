<?php
class Response
{
    private static $columnTranslations = [
        'id' => 'ID',
        'year' => 'an',
        'type' => 'tip',
        'name' => 'nume',
        'beneficiaries_count' => 'număr beneficiari',
        'value' => 'valoare',
        'sex' => 'sex',
        'age_category' => 'categorie de vârstă',
        'sentence_type' => 'tip pedeapsă',
        'law' => 'lege',
        'criterion_value' => 'criteriu',
        'drug' => 'drog',
        'drug_type' => 'tip drog',
        'seizure_type' => 'tip captură',
        'field_name' => 'câmp',
        'environment' => 'mediu',
        'beneficiary' => 'beneficiar',
        'article' => 'articol',
        'category' => 'categorie',
    ];

    public static function sendData(array $data)
    {
        if (isset($_GET['output']) && $_GET['output'] == "csv") {
            self::csv($data);
        } elseif (isset($_GET['output']) && $_GET['output'] == "html") {
            self::html($data);
        } else {
            self::json($data);
        }
    }

    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        //header('Access-Control-Allow-Origin: *'); // necesar pentru AJAX
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function csv(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=download.csv");

        $file = fopen('php://output', 'w');
        if (empty($data)) {
            echo 'no data found for this filters';
            exit;
        }

        $firstElement = $data[0];
        unset($firstElement['id']);

        $translatedColumns = array_map(
            fn($columnName) => self::$columnTranslations[$columnName] ?? $columnName,
            array_keys($firstElement)
        );

        fputcsv($file, $translatedColumns, escape: "\\");

        foreach ($data as $dataElement) {
            unset($dataElement['id']);
            fputcsv($file, array_values($dataElement), escape: "\\");
        }

        fclose($file);
        exit;
    }

    public static function html(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');

        if (empty($data)) {
            echo "No data available.";
            exit;
        }

        echo "<style>table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #f2f2f2; }</style>";
        echo "<table>";
        echo "<thead><tr>";

        $firstElement = $data[0];
        unset($firstElement['id']);
        $translatedColumns = array_map(
            fn($columnName) => self::$columnTranslations[$columnName] ?? $columnName,
            array_keys($firstElement)
        );

        foreach ($translatedColumns as $colName) {
            echo "<th>" . htmlspecialchars($colName) . "</th>";
        }
        echo "</tr></thead><tbody>";
        foreach ($data as $dataElement) {
            unset($dataElement['id']);
            echo "<tr>";
            foreach ($dataElement as $value) {
                echo "<td>" . htmlspecialchars((string)$value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
        exit;
    }

    public static function error(string $message, int $status = 500): void
    {
        self::json(['error' => $message], $status);
        exit;
    }

    public static function badRequest(string $message, int $status = 400): void
    {
        self::json(['bad_request' => $message], $status);
        exit;
    }
}
